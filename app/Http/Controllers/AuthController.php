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
                'data' => null,
                'error'     => $e->getMessage(),
            ], 500);
        }

        /** 
         * TODO: 4. Generate token 🕰️
         */
        Auth::attempt(request()->only('email', 'password'));
        $token = $this->createToken(Auth::user(), 'API_V1');

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
            'error'     => null,
        ], 201);
    }

    /**
     * TODO: Login a user using email and password 📧
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
                'data' => null,
                'error'     => null,
            ], 400);
        }

        /**
         * TODO: 3. Generate token 🕰️
         */
        $token = $this->createToken(Auth::user(), 'API_V1');

        /**
         * TODO: 4. Return auth object 🔑
         */
        return response()->json([
            'message'   => __('messages.success.login'),
            'data' => $this->authObject($token),
            'error'     => null,
        ], 200);
    }

    /**
     * TODO: Login a user using phone and password 📱
     * ? Required Parameters:
     * @param phone international phone number
     * @param password user password
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request
     * * 2. Attempt login
     * * 3. Generate token
     * * 4. Return auth object
     * < --------------------------------------- >
     */
    public function loginWithPhoneAndPassword()
    {
        /**
         * TODO: 1. Validate the request ℹ️
         */
        request()->validate([
            'phone'     => ['required', 'regex:/^(\+)[0-9]{12,15}$/'], // * International phone number
            'password'  => ['required'],
        ]);

        /**
         * TODO: 2. Attempt login 🔑
         */
        try {
            Auth::attempt(request()->only('phone', 'password'));
            Auth::user()->id;
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.credentials'),
                'data' => null,
                'error'     => null,
            ], 400);
        }

        /**
         * TODO: 3. Generate token 🕰️
         */
        $token = $this->createToken(Auth::user(), 'API_V1');

        /**
         * TODO: 4. Return auth object 🔑
         */
        return response()->json([
            'message'   => __('messages.success.login'),
            'data' => $this->authObject($token),
            'error'     => null,
        ], 200);
    }

    /**
     * TODO: refresh token 🌟 
     */
    public function refresh()
    {
        /**
         * TODO: 1. Generate token 🕰️
         */
        try {
            request()->user()->currentAccessToken()->delete();
            Auth::user()->id;
            $token = $this->createToken(request()->user(), 'API_V1');
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.token'),
                'data' => null,
                'error'     => null,
            ], 400);
        }

        /**
         * TODO: 2. Return auth object 🔑
         */
        return response()->json([
            'message'   => __('messages.r_success'),
            'data' => $this->authObject($token),
            'error'     => null,
        ], 200);
    }

    /**
     *  TODO: Logout a user 🔓
     */
    public function logout()
    {
        try {
            auth()->user()->id;
            request()->user()->currentAccessToken()->delete();
            return response()->json([
                'message'   => __('messages.success.logout'),
                'data' => null,
                'error'     => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.token'),
                'data' => null,
                'error'     => null,
            ], 400);
        }
    }

    /**
     * TODO: Send OTP Email 📧
     */
    public function sendEmailOtp()
    {
        try {
            $otp = new Otp(auth()->user());
            return $otp->sendToEmail();
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.otp'),
                'data' => null,
                'error'     => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TODO: Send OTP SMS 📱
     */
    public function sendPhoneOtp()
    {

        try {
            $otp = new Otp(auth()->user());
            return $otp->sendToPhone();
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.otp'),
                'data' => null,
                'error'     => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TODO: Verify OTP 📧📱
     * ? Required Parameters:
     * @param otp user otp
     * < --------------------------------------- >
     * ! Functionality !
     * < --------------------------------------- >
     * * 1. Validate request ℹ️
     * * 2. Check if user is verified
     * * 3. Verify otp ✅
     */
    public function verify($type)
    {
        /**
         * TODO: 1. Validate the request ℹ️
         */
        request()->validate([
            'otp' => ['required', 'regex:/^[0-9]{6}$/'],
        ]);

        /**
         * TODO: 2. Check if user $type is verified 🔐
         */
        if (auth()->user()->{$type . '_verified_at'}) {
            return response()->json([
                'message'   => __('messages.error.verified'),
                'data' => null,
                'error'     => null,
            ], 400);
        }

        /**
         * TODO: 3. Verify OTP ✅
         */
        try {
            $otp = new Otp(auth()->user());
            return $otp->verify(request()->only('otp'), $type);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => __('messages.error.verify'),
                'data' => null,
                'error'     => $e->getMessage(),
            ], 500);
        }
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
                'data' => null,
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
            'verified' => Auth::user()->isVerified(),
            'verification' => [
                'email' => Auth::user()->isEmailVerified(),
                'phone' => Auth::user()->isPhoneVerified(),
                'driver_license' => Auth::user()->isDriverLicenseVerified(),
                'identity_document' => Auth::user()->isIdentityDocumentVerified(),
            ],
            'roles' => $this->rolesToArray(Auth::user()->roles),
            'privileges' => $this->privilegesToArray(Auth::user()->privileges),
        ];
    }

    // convert array to objects to string array of key attributes
    private function rolesToArray($roles)
    {
        $roles_array = [];
        foreach ($roles as $role) {
            array_push($roles_array, $role->role_key);
        }
        return $roles_array;
    }

    // convert array to objects to string array of key attributes
    private function privilegesToArray($privileges)
    {
        $privileges_array = [];
        foreach ($privileges as $privilege) {
            array_push($privileges_array, $privilege->privilege_key);
        }
        return $privileges_array;
    }
}
