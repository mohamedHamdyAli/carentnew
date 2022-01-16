<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Models\Order;
use App\Models\OrderEarlyReturn;
use App\Models\OrderExtend;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RenterOrderEarlyReturnController extends Controller
{


    public function returnEarly($id)
    {
        // get order
        $order = Order::findOrFail($id);

        // check if order can be extended
        if (!$order->renterCanReturnEarly()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be returned early'
            ], 400);
        }

        // create order early return
        $orderReturn = OrderEarlyReturn::create([
            'order_id' => $order->id,
            'refunded' => false,
        ]);

        // update order
        $order->order_status_id = Status::EARLY_RETURN;
        $order->save();
        $order->order_status_id = Status::CAR_DELIVERED;
        $order->saveQuietly();

        return $this->view($id);
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
