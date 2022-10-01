<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\AgencyApplication;
use App\Models\OwnerApplication;
use App\Models\RenterApplication;
use App\Models\VehicleVerification;
use Cache;
use DB;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function counters()
    {
        $counters = Cache::tags(['counters'])->remember(CacheHelper::makeKey('counters'), 600, function () {
            $renter = RenterApplication::where('status', '=', 'in-review')->whereHas('user')->count();
            $owner = OwnerApplication::where('status', '=', 'in-review')->whereHas('user')->count();
            $agency = AgencyApplication::where('status', '=', 'in-review')->whereHas('user')->count();
            $vehicle = VehicleVerification::where('status', '=', 'in-review')->whereHas('vehicle.user')->count();

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
