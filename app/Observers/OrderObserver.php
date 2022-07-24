<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Report;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public $afterCommit = true;

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
        OrderStatusHistory::create(['order_id' => $order->id, 'order_status_id' => 1]);
        $this->sendRequiredNotifications($order);
        $todayReport = Report::firstOrCreate(['date' => Carbon::today()]);
        $todayReport->increment('bookings');
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
        if ($order->wasChanged('order_status_id')) {
            // Order Status Were Changed
            OrderStatusHistory::create(['order_id' => $order->id, 'order_status_id' => $order->order_status_id]);
            $this->sendRequiredNotifications($order);
        }
    }



    private function sendRequiredNotifications($order)
    {
        Log::info('Sending Notifications');
        $status = OrderStatus::find($order->order_status_id);
        if ($status->notify_client) {
            $order->user->notify(new OrderStatusChanged($order, 'client'));
        }
        if ($status->notify_owner) {
            $order->owner->notify(new OrderStatusChanged($order, 'owner'));
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
