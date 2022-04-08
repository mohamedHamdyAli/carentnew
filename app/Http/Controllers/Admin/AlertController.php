<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use Cache;
use DB;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function counters()
    {
        $counters = Cache::tags(['counters'])->remember(CacheHelper::makeKey('counters'), 600, function () {
            $renter = DB::table('renter_applications')->where('status', '=', 'in-review')->count();
            $owner = DB::table('owner_applications')->where('status', '=', 'in-review')->count();
            $agency = DB::table('agency_applications')->where('status', '=', 'in-review')->count();
            $vehicle = DB::table('vehicle_verifications')->where('status', '=', 'in-review')->count();

            $totals = [
                'approvals/renter' => $renter,
                'approvals/owner' => $owner,
                'approvals/agency' => $agency,
                'approvals/vehicle' => $vehicle,
            ];

            $total = array_reduce($totals, function ($carry, $item) {
                return $carry + $item;
            });

            $totals['approvals'] = $total;

            return $totals;
        });

        return response()->json($counters);
    }
}
