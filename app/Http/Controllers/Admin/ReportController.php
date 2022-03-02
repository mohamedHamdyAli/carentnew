<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $data = Cache::remember(CacheHelper::makeKey('reports'), 60, function () {

            if (request()->has('from_date') && request()->has('to_date')) {
                $from_date = Carbon::parse(request()->get('from_date'));
                $to_date = Carbon::parse(request()->get('to_date'));
                $reports = Report::whereBetween('date', [$from_date, $to_date])->get();
            } else if (request()->has('from_date') && !request()->has('to_date')) {
                $from_date = Carbon::parse(request()->get('from_date'));
                $reports = Report::where('date', '>=', $from_date)->get();
            } else {
                $reports = Report::where('date', now()->today());
            }

            return [
                'sales' => (int) $reports->sum('sales'),
                'bookings' => (int) $reports->sum('bookings'),
                'users' => (int) $reports->sum('users'),
                'vehicles' => (int) $reports->sum('vehicles'),
            ];

            return $reports;
        });

        return $data;
    }
}
