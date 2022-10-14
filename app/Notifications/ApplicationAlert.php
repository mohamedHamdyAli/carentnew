<?php

namespace App\Notifications;

use App\Functions\Fcm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationAlert extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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
            'order_id' => null,
            'order_number' => null,
            'data' => [
                'title_en' => $this->data['title_en'],
                'body_en' => $this->data['body_en'],
                'title_ar' => $this->data['title_ar'],
                'body_ar' => $this->data['body_ar'],
                'alert_type' => $this->data['alert_type'],
            ]
        ];

        $this->toFcm($notifiable, $data);

        return $data;
    }

    private function toFcm($notifiable, $data)
    {
        $data = [
            'title' => $this->data['title_' . $notifiable->language],
            'body' => $this->data['body_' . $notifiable->language],
            'order_id' => null,
            'order_number' => null,
        ];
        Fcm::send($data, $notifiable->fcm);
    }
}
