<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class OrderStatusChanged extends Notification
{
    use Queueable;

    private $order;
    private $status;
    private $for;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order, $for)
    {
        $this->order = $order;
        $this->status = OrderStatus::find($order->order_status_id);
        $this->for = $for;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $data = [
            'order_number' => $this->order->number,
            'data' => [
                'title_en' => $this->status->{$this->for . '_title_en'},
                'body_en' => $this->status->{$this->for . '_body_en'},
                'title_ar' => $this->status->{$this->for . '_title_ar'},
                'body_ar' => $this->status->{$this->for . '_body_ar'},
            ]
        ];

        return $data;
    }

    private function toFcm($notifiable, $data)
    {
        // TODO: send FCM notification
    }
}
