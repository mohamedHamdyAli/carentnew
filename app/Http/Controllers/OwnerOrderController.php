<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Models\Order;
use App\Models\OrderExtend;
use Cache;
use Carbon\Carbon;
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
        $data = Order::with(['OrderEarlyReturn', 'renter'])
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
            'can_deliver' => $data->ownerCanDeliver(),
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

    public function accept($id)
    {
        try {
            $order = Order::findOrFail($id);

            if (!$order->ownerCanAccept()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be accepted'
                ], 400);
            }

            $order->order_status_id = Status::ACCEPTED;
            $order->save();
            $order = Order::findOrFail($id);
            $order->order_status_id = Status::PENDING_PAYMENT;
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

    public function reject($id)
    {
        try {
            $order = Order::findOrFail($id);

            if (!$order->ownerCanReject()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be rejected'
                ], 400);
            }

            $order->order_status_id = Status::REJECTED;
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

    public function cancel($id)
    {
        try {
            $order = Order::findOrFail($id);
            if (!$order->ownerCanCancel()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be canceled'
                ], 400);
            }
            $order->order_status_id = Status::CANCELED;
            $order->save();

            // TODO: refund the total amount if the order is paid
            /**
             * ? check if the order is paid
             * ? if yes, refund the total amount
             * ? if no, do nothing
             */

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

    public function deliver($id)
    {
        try {
            $order = Order::findOrFail($id);
            dd($order);
            if (!$order->ownerCanDeliver()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Vehicle can not be delivered'
                ], 400);
            }
            $order->order_status_id = Status::CAR_ARRIVED;
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

    public function complete($id)
    {
        try {
            $order = Order::findOrFail($id);
            if (!$order->ownerCanCompleteOrder()) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be completed'
                ], 400);
            }

            // TODO: apply any refund logic here
            /**
             * ? if order is early return, and the order end date is more than 1 day,
             * ? then calculate the refund amount for the remaining days (difference between order end date and today - 1 day)
             * ? then substract the early return penalty from the refund amount
             * ? then refund the amount to the customer
             */

            // TODO: apply reward logic here
            /**
             * ? get money to point ratio
             * ? get the order total amount
             * ? calculate the reward amount (order total amount * money to point ratio)
             * ? add the reward amount to the customer account
             */


            $order->order_status_id = Status::CAR_RETURNED;
            $order->save();
            $order = Order::findOrFail($id);
            $order->order_status_id = Status::COMPLETED;
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
