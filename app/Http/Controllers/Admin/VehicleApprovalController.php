<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\VehicleVerification;
use Cache;
use Illuminate\Http\Request;

class VehicleApprovalController extends Controller
{
    public function index()
    {
        // validate request
        $this->validate(request(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string',
            'statuses' => 'sometimes|array|in:created,in-review,approved,rejected',
        ]);

        $data = Cache::tags(['agencies'])->remember(CacheHelper::makeKey('vehicle-verification'), 600, function () {
            $applications = VehicleVerification::with('vehicle');

            // filter by status
            if (request()->has('statuses')) {
                $applications = $applications->whereIn('status', request('statuses'));
            }

            // filter by state
            if (request()->has('states')) {
                $applications = $applications->whereIn('state_id', request('states'));
            }

            // search
            if (request()->has('search')) {
                $applications = $applications->with(['vehicle'])->whereHas('vehicle', function ($query) {
                    return $query->whereHas('user', function ($query) {
                        return $query->where('name', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%')
                            ->orWhere('phone', 'like', '%' . request('search') . '%');
                    });;
                });
            }

            $applications = $applications
                ->orderBy('created_at', 'desc')
                ->paginate(request('per_page', 20));

            // add created_at to users
            $applications = $applications->setCollection($applications->getCollection()->map(function ($application) {
                $application->makeHidden([
                    'vehicle_license_verified',
                    'vehicle_license_uploaded',
                    'vehicle_insurance_verified',
                    'vehicle_insurance_uploaded',
                    'vehcile_id',
                    'reason',
                    'approved',
                ])->makeVisible([
                    'id',
                    'created_at',
                ]);
                $application->type = 'vehicle';
                // unset($application->vehicle);
                return $application;
            }));

            return $applications;
        });

        return response()->json($data);
    }
}
