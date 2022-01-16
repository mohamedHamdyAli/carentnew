<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Models\Order;
use App\Models\OrderExtend;
use Illuminate\Http\Request;

class OwnerOrderExtendController extends Controller
{
    public function accept($id)
    {
        // get order
        $order = Order::findOrFail($id);

        // check if order can be extended
        if (!$order->onwerCanHandleExtendRequest()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be extended'
            ], 400);
        }

        $lastExtendRequest = $order->OrderExtends()->orderBy('created_at', 'desc')->first();
        $lastExtendRequest->approved = true;
        $lastExtendRequest->save();

        // update order
        $order->order_status_id = Status::EXTEND_ACCEPTED;
        $order->save();
        $order->order_status_id = Status::CAR_DELIVERED;
        $order->saveQuietly();

        return $this->view($id);
    }

    public function reject($id)
    {
        // get order
        $order = Order::findOrFail($id);

        // check if order can be extended
        if (!$order->onwerCanHandleExtendRequest()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be extended'
            ], 400);
        }

        $lastExtendRequest = $order->OrderExtends()->orderBy('created_at', 'desc')->first();
        $lastExtendRequest->approved = false;
        $lastExtendRequest->save();
        // update order
        $order->order_status_id = Status::EXTEND_REJECTED;
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
            'can_reject' => $data->ownerCanReject(),
            'can_cancel' => $data->ownerCanCancel(),
            'can_accept' => $data->ownerCanAccept(),
            'can_complete' => $data->ownerCanCompleteOrder(),
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
}
