<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    //
    public function settings($key)
    {
        $setting = Cache::rememberForever('setting-' . $key . '-' . app()->getLocale(), function () use ($key) {
            return Setting::where('key', $key)->first();
        });

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $setting,
            'error' => null
        ]);
    }
}
