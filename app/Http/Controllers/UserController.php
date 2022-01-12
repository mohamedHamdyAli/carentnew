<?php

namespace App\Http\Controllers;

use Auth;
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

    public function fcm()
    {
        request()->validate([
            'fcm'      => ['required', 'string'],
        ]);

        $user = Auth::user();

        $user->update([
            'fcm' => request('fcm'),
        ]);

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $user,
            'error' => null,
        ], 200);
    }
}
