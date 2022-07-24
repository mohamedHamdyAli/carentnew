<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Http\Functions\OrderManager;
use App\Models\Order;
use App\Models\OrderExtend;
use App\Models\VehiclePricing;
use Illuminate\Http\Request;

class RenterOrderExtendController extends Controller
{
    public function extend()
    {
        // get order
        $order = Order::findOrFail(request('order_id'));

        // check if order can be extended
        if (!$order->renterCanExtend()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be extended'
            ], 400);
        }

        // get vehicle pricing
        $pricing = VehiclePricing::where('vehicle_id', $order->vehicle_id)->first();

        // validate vehicle pricing with driver
        if (!$pricing->has_driver && (bool) request('with_driver')) {
            return response()->json([
                'message' => 'The vehicle does not have a driver.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        // validate request
        $this->validate(request(), [
            'end_date' => 'required|date|date_format:Y-m-d|after:' . $order->end_date,
            'suggested_price' => 'sometimes|integer|min:0',
        ]);

        // check if vehicle has any booking overlaps with the requested dates
        $hasOverlap = Order::where('vehicle_id', $order->vehicle_id)->Overlaps($order->end_date, request('end_date'))->count();

        if ($hasOverlap) {
            return response()->json([
                'message' => 'The requested dates are not available.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        $totals = OrderManager::getTotals($pricing, $order->end_date, request('end_date'), $order->with_driver, request('suggested_price'));

        if (intval(request('suggested_price')) > 0 && (intval(request('suggested_price')) < $totals['original_total'] * 0.8) || (intval(request('suggested_price')) > $totals['original_total'])) {
            return response()->json([
                'message' => 'The suggested price can\'t be less than 80% of the total price or greater than total price.',
                'data' => null,
                'error' => true,
            ], 400);
        }

        // create order extend
        $orderExtend = OrderExtend::create([
            'order_id' => $order->id,
            'original_end_date' => $order->end_date,
            'request_end_date' => request('end_date'),
            'with_driver' => $order->with_driver,
            'vehicle_total' => $totals['vehicle_total'],
            'driver_total' => $totals['driver_total'],
            'sub_total' => $totals['sub_total'],
            'vat' => $totals['vat'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
        ]);

        // update order
        $order->order_status_id = Status::EXTEND_REQUEST;
        $order->save();
        $order->order_status_id = Status::CAR_DELIVERED;
        $order->saveQuietly();

        return $this->view($order->id);
    }



    public function view($id)
    {
        $data = Order::with('OrderEarlyReturn')
            ->where('id', $id)
            ->firstOrFail();

        $orderExtend = OrderExtend::where('order_id', $id)
            ->where('approved', true)
            ->orWhere('approved', null)
            ->where('paid', false)
            ->first();

        $settings = [
            'can_cancel' => $data->renterCanCancel(),
            'can_pay' => $data->renterCanPay(),
            'can_extend' => $data->renterCanExtend(),
            'can_return_early' => $data->renterCanReturnEarly(),
        ];

        // add settings to data
        $data->settings = $settings;
        $data->extend_requests = $orderExtend->isActive() ? $orderExtend : null;

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }
}
