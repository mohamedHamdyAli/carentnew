<?php

namespace App\Http\Controllers;

use App\Http\Functions\OrderManager;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Vehicle;
use App\Models\VehiclePricing;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{

    public function getTotals()
    {
        // validate request
        $this->validate(request(), [
            'vehicle_id' => 'required|string|exists:vehicles,id',
            'start_date' => 'required|date|date_format:Y-m-d|after:yesterday',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
            'with_driver' => 'required|boolean',
            'suggested_price' => 'sometimes|integer|min:0',
        ]);

        $queryString = request()->getQueryString();

        // get vehicle pricing
        $pricing = VehiclePricing::where('vehicle_id', request('vehicle_id'))->first();
        
        // validate vehicle pricing with driver
        if (!$pricing->has_driver && (bool) request('with_driver')) {
            return response()->json([
                'message' => 'The vehicle does not have a driver.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        // check if vehicle has any booking overlaps with the requested dates
        $hasOverlap = Order::where('vehicle_id', request('vehicle_id'))->Overlaps(request('start_date'), request('end_date'))->first();

        if ($hasOverlap) {
            return response()->json([
                'message' => 'The requested dates are not available.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        $data = Cache::tags(['totals'])->remember('totals-' . $queryString, 600, function () use ($pricing) {
            return OrderManager::getTotals($pricing, request('start_date'), request('end_date'), request('with_driver'), request('suggested_price'));
        });

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }

    public function create()
    {
        // validate request
        $this->validate(request(), [
            'vehicle_id' => 'required|string|exists:vehicles,id',
            'start_date' => 'required|date|date_format:Y-m-d|after:yesterday',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
            'with_driver' => 'required|boolean',
            'suggested_price' => 'sometimes|integer|min:0',
        ]);

        // get vehicle pricing
        $pricing = VehiclePricing::where('vehicle_id', request('vehicle_id'))->first();
        
        // validate vehicle pricing with driver
        if (!$pricing->has_driver && (bool) request('with_driver')) {
            return response()->json([
                'message' => 'The vehicle does not have a driver.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        if (!request('with_driver') && !Auth::user()->hasPrivilege('rent_without_driver')) {
            return response()->json([
                'message' => 'You do not have permission to rent without driver.',
                'data' => null,
                'error' => true,
            ], 403);
        }

        $isDriverRequired = Vehicle::find(request('vehicle_id'))->pricing->is_driver_required;

        if ($isDriverRequired && !request('with_driver')) {
            return response()->json([
                'message' => 'You need to select driver.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        // check if vehicle has any booking overlaps with the requested dates
        $hasOverlap = Order::where('vehicle_id', request('vehicle_id'))->Overlaps(request('start_date'), request('end_date'))->first();

        if ($hasOverlap) {
            return response()->json([
                'message' => 'The requested dates are not available.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        $totals = OrderManager::getTotals($pricing, request('start_date'), request('end_date'), request('with_driver'), request('suggested_price'));

        if (intval(request('suggested_price')) > 0 && (intval(request('suggested_price')) < $totals['original_total'] * 0.8) || (intval(request('suggested_price')) > $totals['original_total'])) {
            return response()->json([
                'message' => 'The suggested price can\'t be less than 80% of the total price or greater than total price.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        $number = OrderManager::generateNumber();

        $vehicle = Vehicle::findOrFail(request('vehicle_id'));

        $data = [
            'number' => $number,
            'user_id' => auth()->user()->id,
            'vehicle_id' => request('vehicle_id'),
            'owner_id' => $vehicle->user_id,
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'with_driver' => request('with_driver'),
            'suggested_price' => request('suggested_price'),
            'order_status_id' => 1,
            'vehicle_total' => $totals['vehicle_total'],
            'driver_total' => $totals['driver_total'],
            'sub_total' => $totals['sub_total'],
            'vat' => $totals['vat'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
        ];

        $order = Order::create($data);



        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $order,
            'error' => null
        ], 201);
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)->simplePaginate();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $orders,
            'error' => null
        ], 200);
    }

    public function byNumber($number)
    {
        $data = Order::where('number', $number)->firstOrFail();

        $settings = [
            'can_cancel' => $data->renterCanCancel(),
            'can_pay' => $data->renterCanPay(),
            'can_extend' => $data->renterCanExtend(),
        ];

        // add settings to data
        $data->settings = $settings;

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }

    public function getStatuses()
    {
        $data = Cache::rememberForever('order-statuses-' . app()->getLocale(), function () {
            return OrderStatus::get([
                'id',
                'name_en',
                'name_ar',
                'alert_type',
                'terminate'
            ]);
        });

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }
}
