<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Cache;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index()
    {
        $data = Cache::remember(CacheHelper::makeKey('statistics'), 60, function () {

            if (request()->has('from_date') && request()->has('to_date')) {
                $from_date = Carbon::parse(request()->get('from_date'));
                $to_date = Carbon::parse(request()->get('to_date'));
                $statistics = Report::whereBetween('date', [$from_date, $to_date])->get();
            } else if (request()->has('from_date') && !request()->has('to_date')) {
                $from_date = Carbon::parse(request()->get('from_date'));
                $statistics = Report::where('date', '>=', $from_date)->get();
            } else {
                $statistics = Report::where('date', now()->today());
            }

            return [
                'sales' => (int) $statistics->sum('sales'),
                'bookings' => (int) $statistics->sum('bookings'),
                'users' => (int) $statistics->sum('users'),
                'vehicles' => (int) $statistics->sum('vehicles'),
            ];

            return $statistics;
        });

        return $data;
    }
}
