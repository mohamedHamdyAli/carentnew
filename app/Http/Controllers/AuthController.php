<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Http\Common\Auth\Otp;
use App\Http\Common\Auth\RoleManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register()
    {

        // if(app()->environment('local')){
        //     User::truncate();
        // }
        /**
         * TODO: Register a new user
         * ? Required Parameters:
         * @param name user full name
         * @param email email address
         * @param password user password
         * @param phone international phone number
         * < --------------------------------------- >
         * ! Functionality !
         * < --------------------------------------- >
         * * 1. Validate request
         * * 2. Hash password
         * * 3. Create user
         * * 4. Generate token
         * * 5. Return auth object
         * < --------------------------------------- >
         */

        /**
         * TODO: 1. Validate the request
         */
        request()->validate([
            'name'      => ['required', 'regex:/^(?!.*\d)[أ-يa-z\s]{2,66}$/iu'], // * Name without numbers
            'phone'     => ['required', 'unique:users', 'regex:/^(\+)[0-9]{12,15}$/'], // * International phone number
            'email'     => ['required', 'email', 'unique:users'], // * Unique email address
            'password'  => ['required', Password::min(8)->letters()->numbers()], // * Strong password
        ]);

        /**
         * TODO: 2. Hash the password
         */
        $password = Hash::make(request('password'));

        /**
         * TODO: 3. Create the user
         */
        try {
            $user = User::create([
                'name'      => request('name'),
                'email'     => request('email'),
                'password'  => $password,
                'phone'     => request('phone'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.create_user'),
                'error'     => $e->getMessage(),
            ], 500);
        }

        /** 
         * TODO: 4. Generate token
         */
        try {
            Auth::attempt(request()->only('email', 'password'));
            $token = $this->createToken(auth()->user(), 'password');

            $roleManager = new RoleManager(auth()->user());
            $roleManager->assign('user');

        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.generate_token'),
                'error'     => $e->getMessage(),
            ], 500);
        }

        /**
         * TODO: 5. Return auth object
         */
        return response()->json([
            'message'   => __('messages.success.create_user'),
            'data' => $this->authObject($token),
        ], 201);
    }

    private function createToken($user, $type)
    {
        return $user->createToken('carent-' . $type)->plainTextToken;
    }

    private function authObject($token)
    {
        return [
            'user'      => auth()->user(),
            'access_token'     => $token,
            'email_verified' => auth()->user()->isEmailVerified(),
            'phone_verified' => auth()->user()->isPhoneVerified(),
            'roles' => auth()->user()->roles,
            'privileges' => auth()->user()->privileges,
        ];
    }
}
