<?php

namespace App\Http\Controllers;

use App\Functions\Fcm;
use App\Helpers\CountryHelper;
use App\Jobs\UpdateFcm;
use App\Models\Order;
use App\Models\User;
use Auth;
use Cache;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

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

    public function update(Request $request)
    {
        $request->validate([
            'name'      => ['sometimes', 'regex:/^(?!.*\d)[أ-يa-z\s]{2,66}$/iu'], // * Name without numbers
            'email'     => ['sometimes', 'email', 'unique:users,email,' . Auth::id()],
            'phone'     => ['sometimes', 'regex:/^(\+)[0-9]{10,15}$/', 'unique:users,phone,' . Auth::id()],
            'password'  => ['sometimes'],
        ]);

        $user = Auth::user();

        if ($request->has('password')) {
            // compare old password
            if (!Hash::check(request()->password, $user->password)) {
                return response()->json([
                    'message' => __('messages.r_error'),
                    'data' => null,
                    'error' => __('messages.e_password_wrong'),
                ], 401);
            }
        }

        $user->update($request->except('password'));

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
            ], 401);
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
            'fcm'      => ['required', 'string']
        ]);

        $user = Auth::user();

        $countryCode = CountryHelper::get()->code;
        $headerLanguage = request()->header('Language');
        $lang = $headerLanguage ?? 'ar';

        $userLang = auth()->user()->language;
        $userCountry = auth()->user()->country;

        Log::info("user country: " . $userCountry);
        Log::info("country code: " . $countryCode);

        if ($countryCode) {
            Log::info("country code: " . $countryCode);
            $data = [
                'fcm' => request('fcm'),
                'userCountry' => $userCountry,
                'userLang' => $userLang,
                'countryCode' => $countryCode,
                'lang' => $lang,
                'role' => null,
            ];

            if ($user->hasRole('renter')) {
                $data['role'] = 'renter';
            } else if ($user->hasRole('owner')) {
                $data['role'] = 'owner';
            } else if ($user->hasRole('agency')) {
                $data['role'] = 'agency';
            }

            UpdateFcm::dispatch($data);
        }

        Log::info(["update fcm: ", [
            'fcm' => request('fcm'),
            'country' => $countryCode,
            'language' => $lang
        ]]);

        $user->update([
            'fcm' => request('fcm'),
            'country' => $countryCode,
            'language' => $lang
        ]);

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $user,
            'error' => null,
        ], 200);
    }

    public function fcmOld()
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

    // delete user
    public function delete()
    {
        $user = User::find(Auth::id());

        // check if user balance is more than 0
        if ($user->balance > 0) {
            return response()->json([
                'message' => __('messages.o_error'),
                'data' => null,
                'error' => __('messages.user_balance_not_zero'),
            ], 400);
        }

        // check if user has on going orders
        $onGoingOrders = Order::where('user_id', $user->id)->WhereHas('orderStatus', function ($q) {
            return $q->where('terminate', 0);
        })->first();

        if ($onGoingOrders) {
            return response()->json([
                'message' => __('messages.o_error'),
                'data' => null,
                'error' => __('messages.error.user_has_ongoing_orders'),
            ], 400);
        }

        DB::transaction(function () use ($user) {
            // remove all user sanctum tokens
            auth()->user()->tokens()->delete();

            $user->update(
                [
                    'email' => $user->email . '_deleted_' . time(),
                    'phone' => $user->phone . '_deleted_' . time()
                ]
            );

            $user->delete();
        });

        Cache::tags(['users', 'renters', 'owners', 'agencies', 'vehicles'])->flush();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => null,
            'error' => null,
        ], 200);
    }
}
