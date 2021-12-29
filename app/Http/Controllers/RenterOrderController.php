<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
