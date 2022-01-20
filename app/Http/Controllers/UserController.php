<?php

namespace App\Http\Controllers;

use App\Functions\Fcm;
use Auth;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $user,
            'error' => null,
        ], 200);
    }
    public function update()
    {
        request()->validate([
            'name'      => ['required', 'regex:/^(?!.*\d)[أ-يa-z\s]{2,66}$/iu'], // * Name without numbers
        ]);

        $user = Auth::user();

        $user->update([
            'name' => request('name'),
        ]);

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $user,
            'error' => null,
        ], 200);
    }

    public function password()
    {
        // * Validate the request
        request()->validate([
            'password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        // * Check if the old password is correct
        if (!Hash::check(request('password'), Auth::user()->password)) {
            return response()->json([
                'message' => __('messages.r_error'),
                'data' => null,
                'error' => __('messages.e_password_incorrect'),
            ], 400);
        }

        // * Update the password
        Auth::user()->update([
            'password' => Hash::make(request('new_password')),
        ]);

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function fcm()
    {
        request()->validate([
            'fcm'      => ['required', 'string'],
        ]);

        $user = Auth::user();

        $user->update([
            'fcm' => request('fcm'),
        ]);

        // subscribe to all-countrycode topic
        Fcm::subscribe(request('fcm'), 'all-' . request()->header('country'));

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $user,
            'error' => null,
        ], 200);
    }
}
