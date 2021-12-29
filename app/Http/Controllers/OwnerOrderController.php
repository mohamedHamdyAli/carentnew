<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OwnerOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('owner_id', auth()->user()->id)
            ->whereHas('OrderStatus', function ($query) {
                $ids = request('status_ids');
                if ($ids && count(explode(',', $ids)) > 0) {
                    $query->whereIn('id', explode(',', $ids));
                }
            })
            ->simplePaginate();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $orders,
            'error' => null
        ], 200);
    }

    public function view($id)
    {
        $data = Order::findOrFail($id);

        $settings = [
            'can_reject' => $data->ownerCanReject(),
            'can_cancel' => $data->ownerCanCancel(),
            'can_accept' => $data->ownerCanAccept(),
        ];

        // add settings to data
        $data->settings = $settings;

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null
        ], 200);
    }

    public function accept($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->ownerCanAccept()) {
                $order->order_status_id = 2;
                $order->save();
                $order = Order::findOrFail($id);
                $order->order_status_id = 3;
                $order->save();
            } else {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be accepted'
                ], 400);
            }
            return $this->view($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->ownerCanReject()) {
                $order->order_status_id = 10;
                $order->save();
            } else {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be rejected'
                ], 400);
            }
            return $this->view($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->ownerCanCancel()) {
                $order->order_status_id = 11;
                $order->save();
            } else {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be canceled'
                ], 400);
            }
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
