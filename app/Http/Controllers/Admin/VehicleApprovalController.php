<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleInsurance;
use App\Models\VehicleLicense;
use App\Models\VehicleVerification;
use Cache;
use DB;
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

        $data = Cache::tags(['vehicles'])->remember(CacheHelper::makeKey('vehicle-verification'), 600, function () {
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

    public function show($id)
    {
        $application = VehicleVerification::with([
            'vehicle',
            'vehicle.Brand',
            'vehicle.User',
            'vehicle.VehicleImages',
            'vehicleLicense',
            'vehicleInsurance',
            'vehicle.VehiclePricing',
            'vehicle.VehicleFeatures',
        ]);

        if (request()->has('byVehicleId') && request('byVehicleId') == 1) {
            $application = $application->where('vehicle_id', $id)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
            return response()->json($application);
        } else {
            $application = $application->findOrFail($id);
        }

        $application->makeVisible([
            'id',
            'created_at',
        ]);
        $application->user = $application->vehicle->user;
        $application->name = $application->user->name;
        $application->type = 'vehicle';
        return response()->json($application);
    }

    public function inReview($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $application = VehicleVerification::findOrFail($id);
                $application->status = 'in-review';
                $application->save();
                Cache::tags(['vehicles'])->flush();
                Cache::tags(['counters'])->flush();
            });
            return response()->json(['message' => 'Application is in review']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function approve($id)
    {
        // approve application
        try {
            DB::transaction(function () use ($id) {
                $application = VehicleVerification::findOrFail($id);

                if (!$application) {
                    // return 400 response user already has ongoing request
                    return response()->json([
                        'message' => __('messages.error.missing_data'),
                        'data' => null,
                        'error' => true
                    ], 400);
                }

                $vehicle = Vehicle::find($application->vehicle_id);

                $application->update([
                    'status' => 'approved',
                    'vehicle_insurance_verified' => true,
                    'vehicle_license_verified' => true,
                ]);

                VehicleInsurance::whereVerifiedAt(null)->where('id', $application->vehicle_insurance_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                VehicleLicense::whereVerifiedAt(null)
                    ->where('id', $application->vehicle_license_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                $vehicle->update([
                    'verified_at' => now()
                ]);

                Cache::tags(['vehicles'])->flush();
                Cache::tags(['counters'])->flush();
            });
            return response(['message' => 'Application is approved']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    public function reject($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $application = VehicleVerification::findOrFail($id);
                $application->status = 'rejected';
                $application->reason = request('reason');
                $application->save();
                Cache::tags(['vehicles'])->flush();
                Cache::tags(['counters'])->flush();
            });

            return response()->json(['message' => 'Application is rejected']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
