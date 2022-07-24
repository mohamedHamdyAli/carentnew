<?php

namespace App\Http\Controllers;

use App\Http\Common\Auth\Otp;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * TODO: Verify OTP 📧📱
     * ? Required Parameters:
     * @param email user email address
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request ℹ️
     * * 2. Find user by email address 🤵
     * * 3. Send Email OTP 📧
     * * 4. Return response 🌐
     */
    public function reset()
    {
        /**
         * TODO: 1. Validate request ℹ️
         */
        $this->validate(request(), [
            'email' => 'required|email',
        ]);

        /**
         * TODO: 2. Find user by email address
         */
        $user = User::where('email', request('email'))->first();
        if ($user === null) {
            return response()->json([
                'message' => __('messages.error.reset'),
                'data' => null,
                'error' => null,
            ], 400);
        }

        /**
         * TODO: 3. Send Email OTP
         */
        $otp = new Otp($user);
        $otp->sendToEmail();

        /**
         * TODO: 4. Return response
         */
        return response()->json([
            'message' => __('messages.success.otp'),
            'error' => null,
        ], 200);
    }

    /**
     * TODO: Verify OTP 📧
     * ? Required Parameters:
     * @param email user email address
     * @param otp user OTP
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request ℹ️
     * * 2. Find user by email address 🤵
     * * 3. Verify OTP 📧
     * * 4. Generate password reset token 🔑
     * * 5. Return response 🌐
     */
    public function verify()
    {
        /**
         * TODO: 1. Validate request ℹ️
         */
        $this->validate(request(), [
            'email' => 'required|email',
            'otp' => ['required', 'regex:/^[0-9]{6}$/'],
        ]);

        /**
         * TODO: 2. Find user by email address
         */
        $user = User::where('email', request('email'))->first();

        /**
         * TODO: 3. Verify OTP
         */
        try {
            $otp = new Otp($user);
            return $otp->verify(request('otp'), 'password');
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.verify'),
                'error' => null,
                'error'     => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TODO: Change Password using token 🔑
     * ? Required Parameters:
     * @param token user password reset token
     * @param password user new password
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request ℹ️
     * * 2. Find user by token 🤵
     * * 3. Change password 🔑
     * * 4. Delete all tokens ⛓️
     * * 4. Return response 🌐
     */
    public function change()
    {

        /**
         * TODO: 1. Validate request ℹ️
         */
        $this->validate(request(), [
            'password' => ['required', Password::min(8)->letters()->numbers()], // * Strong password
        ]);

        /**
         * TODO: 2. Find user by token
         */
        $user = User::find(auth()->user()->id);

        /**
         * TODO: 3. Change password
         */
        $user->password = Hash::make(request('password'));
        $user->save();

        /**
         * TODO: 4. Delete all tokens ⛓️
         */
        $user->tokens()->delete();

        /**
         * TODO: 5. Return response
         */
        return response()->json([
            'message' => __('messages.success.password-change'),
            'data' => null,
            'error' => null,
        ], 200);
    }
}
