<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Log;

class SendSmsOtp extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['vonage'];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\VonageMessage
     */
    public function toVonage($notifiable)
    {
        $message = [
            'ar' => 'رمز التحقق هو : ' . $this->otp,
            'en' => 'OTP is : ' . $this->otp,
        ];

        $lang = app()->getLocale();

        Log::info("Sending SMS Otp to {$notifiable->phone}: " . $this->otp);

        if ($lang == 'ar') {
            return (new VonageMessage)
                ->content($message['ar'])
                ->unicode(true);
        } else {
            return (new VonageMessage)
                ->content($message['en']);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
