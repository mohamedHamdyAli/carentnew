<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Http\Functions\OrderManager;
use App\Models\Order;
use App\Models\OrderEarlyReturn;
use App\Models\OrderExtend;
use App\Models\VehiclePricing;
use Cache;
use Illuminate\Http\Request;

class RenterOrderController extends Controller
{


    public function index()
    {
        $orders = Order::where('user_id', auth()->user()->id)
            ->whereHas('OrderStatus', function ($query) {
                $ids = request('status_ids');
                if ($ids && count(explode(',', $ids)) > 0) {
                    $query->whereIn('id', explode(',', $ids));
                }
                $isTerminated = request('terminated');
                if ($isTerminated) {
                    $query->where('terminate', $isTerminated);
                }
            })
            ->orderBy('created_at', 'desc')
            ->simplePaginate();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $orders,
            'error' => null
        ], 200);
    }

    public function view($id)
    {
        $data = Order::with('OrderEarlyReturn')
            ->where('id', $id)
            ->firstOrFail();

        $orderExtend = OrderExtend::where('order_id', $id)
            ->where('paid', false)
            ->where('approved', true)
            ->orWhere('approved', null)
            ->first();

        $settings = [
            'can_cancel' => $data->renterCanCancel(),
            'can_pay' => $data->renterCanPay(),
            'can_recieve' => $data->renterCanReceive(),
            'can_extend' => $data->renterCanExtend(),
            'can_return_early' => $data->renterCanReturnEarly(),
        ];

        // add settings to data
        $data->settings = $settings;
        $data->extend_requests = $orderExtend && $orderExtend->isActive() ? $orderExtend : null;

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }

    public function cancel($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->renterCanCancel()) {
                $order->order_status_id = 11;
                $order->save();
            } else {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be canceled'
                ], 400);
            }

            Cache::tags(['orders'])->flush();

            return $this->view($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function receive($id)
    {
        try {
            $order = Order::findOrFail($id);
            if (!$order->renterCanReceive()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Vehicle can not be received'
                ], 400);
            }
            $order->order_status_id = Status::CAR_DELIVERED;
            $order->save();

            Cache::tags(['orders'])->flush();

            return $this->view($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
