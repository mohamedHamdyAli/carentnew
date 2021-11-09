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
     * * 5. Assign role
     * * 6. Return auth object
     * < --------------------------------------- >
     */
    public function register()
    {
        /**
         * TODO: 1. Validate the request ℹ️
         */
        request()->validate([
            'name'      => ['required', 'regex:/^(?!.*\d)[أ-يa-z\s]{2,66}$/iu'], // * Name without numbers
            'phone'     => ['required', 'unique:users', 'regex:/^(\+)[0-9]{12,15}$/'], // * International phone number
            'email'     => ['required', 'email', 'unique:users'], // * Unique email address
            'password'  => ['required', Password::min(8)->letters()->numbers()], // * Strong password
        ]);

        /**
         * TODO: 2. Hash the password #️⃣
         */
        $passwordHash = Hash::make(request('password'));

        /**
         * TODO: 3. Create the user 🤵
         */
        try {
            User::create([
                'name'      => request('name'),
                'email'     => request('email'),
                'password'  => $passwordHash,
                'phone'     => request('phone'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.create_user'),
                'error'     => $e->getMessage(),
            ], 500);
        }

        /** 
         * TODO: 4. Generate token 🕰️
         */
        Auth::attempt(request()->only('email', 'password'));
        $token = $this->createToken(Auth::user(), 'password');

        /**
         * TODO: 5. Assign defualt role 🎩
         */
        $roleManager = new RoleManager(Auth::user());
        $roleManager->assign('user');

        /**
         * TODO: 6. Return auth object 🔑
         */
        return response()->json([
            'message'   => __('messages.success.create_user'),
            'data' => $this->authObject($token),
        ], 201);
    }

    /**
     * TODO: Login a user using email and password
     * ? Required Parameters:
     * @param email email address
     * @param password user password
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request
     * * 2. Attempt login
     * * 3. Generate token
     * * 4. Return auth object
     */
    public function loginWithEmailAndPassword()
    {
        /**
         * TODO: 1. Validate the request ℹ️
         */
        request()->validate([
            'email'     => ['required', 'email'],
            'password'  => ['required'],
        ]);

        /**
         * TODO: 2. Attempt login 🔑
         */
        try {
            Auth::attempt(request()->only('email', 'password'));
            Auth::user()->id;
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.credentials'),
                'error'     => null,
            ], 400);
        }

        /**
         * TODO: 3. Generate token 🕰️
         */
        $token = $this->createToken(Auth::user(), 'password');

        /**
         * TODO: 4. Return auth object 🔑
         */
        return response()->json([
            'message'   => __('messages.success.login'),
            'data' => $this->authObject($token),
        ], 200);
    }

    /**
     * TODO: Create a new token for the user 🕰️
     */
    private function createToken($user, $type)
    {
        try {
            return $user->createToken('carent-' . $type)->plainTextToken;
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.generate_token'),
                'error'     => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TODO: generate auth object 🔑
     */
    private function authObject($token)
    {
        return [
            'user'      => Auth::user(),
            'access_token'     => $token,
            'email_verified' => Auth::user()->isEmailVerified(),
            'phone_verified' => Auth::user()->isPhoneVerified(),
            'roles' => Auth::user()->roles,
            'privileges' => Auth::user()->privileges,
        ];
    }
}
