<?php

namespace App\Http\Common\Auth;

use App\Mail\EmailOtp;
use App\Models\UserOtp;
use Mail;

class Otp
{
    protected $user;
    protected $otp;

    public function __construct($user)
    {
        $this->user = $user;

        /**
         * TODO: Generate OTP
         */
        $this->otp = $this->generateOtp();
    }

    public function sendToEmail()
    {
        /**
         * TODO: Send OTP email to user
         * * 1. Save OTP to database
         * * 2. Send OTP to user email
         */

        /**
         * TODO: Save OTP to database
         */

        $this->saveOtpToDatabase('email');

        /**
         * TODO: Send OTP to user email
         */
        if (!app()->environment('local')) {
            return Mail::to($this->user->email)->send(new EmailOtp($this->otp));
        }
    }

    public function sendToPhone()
    {
        /**
         * TODO: Send OTP phone to user
         * * 1. Save OTP to database
         * * 2. Send OTP to user phone
         */

        /**
         * TODO: Save OTP to database
         */
        $this->saveOtpToDatabase('phone');

        /**
         * TODO: Send OTP to user phone
         */
        if (!app()->environment('local')) {
            // TODO: Send OTP to user phone function
        }
    }

    private function generateOtp()
    {
        // ? Generate static OTP for development
        // * @value = 123456
        $otp = 123456;

        // ? if Environment is not local, generate random OTP
        if (!app()->environment('local')) {
            $otp = rand(100000, 999999);
        }
        return $otp;
    }

    private function saveOtpToDatabase($for)
    {
        UserOtp::create([
            'user_id' => $this->user->id,
            'for' => $for,
            'otp' => $this->otp,
            'expire_at' => now()->addMinutes(5),
        ]);
    }
}
