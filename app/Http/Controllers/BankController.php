<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $data = cache()->tags(['countries', 'banks'])->rememberForever(CacheHelper::makeKey('banks-' . request()->header('Country')), function () {
            return Bank::where('active', true)
                ->where('country_id', request()->header('Country'))
                ->orderBy('name')
                ->get();
        });

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null,
        ], 200);
    }
}
