<?php

namespace App\Notifications;

use App\Functions\Fcm;
use App\Mail\OrderStatusChanged as MailOrderStatusChanged;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mail;

class OrderStatusChanged extends Notification implements ShouldQueue
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
            'data' => [
                'title_en' => $this->status->{$this->for . '_title_en'},
                'body_en' => $this->status->{$this->for . '_body_en'},
                'title_ar' => $this->status->{$this->for . '_title_ar'},
                'body_ar' => $this->status->{$this->for . '_body_ar'},
                'alert_type' => $this->status->alert_type,
            ]
        ];

        $this->toFcm($notifiable, $data);

        return $data;
    }

    private function toFcm($notifiable, $data)
    {
        $data = [
            'title' => $this->status->{$this->for . '_title_' . $notifiable->language},
            'body' => $this->status->{$this->for . '_body_' . $notifiable->language},
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
        ];
        $send = Fcm::send($data, $notifiable->fcm);

        Mail::to(
            [
                [
                    'email' => $notifiable->email,
                    'name' => $notifiable->name
                ]
            ]
        )->send(new MailOrderStatusChanged($data['title'] . ' #' . $data['order_number'], $data['body']));
    }
}
