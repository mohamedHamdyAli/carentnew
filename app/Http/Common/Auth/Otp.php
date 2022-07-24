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

        return response()->json([
            'message' => __('messages.success.otp'),
            'data' => app()->environment('local') ? $this->otp : null,
            'error' => null,
        ]);
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

        return response()->json([
            'message' => __('messages.success.otp'),
            'data' => app()->environment('local') ? $this->otp : null,
            'error' => null,
        ]);
    }

    /**
     * TODO: Verify OTP
     */
    public function verify($otp, $type)
    {
        /**
         * TODO: Verify OTP
         */
        $checkOTP = UserOtp::where('user_id', $this->user->id)
            ->where('for', $type === 'password' ? 'email' : $type)
            ->where('otp', $otp)
            ->where('expire_at', '>', now())
            ->first();


        /**
         * TODO: Check OTP is exist
         */
        if ($checkOTP === null) {
            return response()->json([
                'message' => __('messages.error.verify'),
                'data' => null,
                'error' => null,
            ], 400);
        }

        /**
         * TODO: Expire used OTP
         */
        $checkOTP->expire_at = now();
        $checkOTP->save();

        /**
         * TODO: Genereate password reset token if requested
         */
        $data = null;

        if ($type === 'password') {

            $token = $this->user->createToken('carent-limited', ['password:change'])->plainTextToken;

            $data = [
                'access_token' => $token
            ];
        }

        /**
         * TODO: Verify required field
         */
        if ($type !== 'password') {
            $this->user->{$type . '_verified_at'} = now();
            $this->user->save();

            // verify user if all required field is verified
            if ($this->user->email_verified_at !== null && $this->user->phone_verified_at !== null) {
                $this->user->verified_at = now();
                $this->user->save();
            }
        }

        /**
         * TODO: Return response
         */
        return response()->json([
            'message' => __('messages.success.verify'),
            'data' => $data,
            'error' => null,
        ], 200);
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
