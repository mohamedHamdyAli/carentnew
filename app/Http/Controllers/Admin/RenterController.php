<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\DriverLicense;
use App\Models\IdentityDocument;
use App\Models\RenterApplication;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RenterController extends Controller
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

        $data = Cache::tags(['renters'])->remember(CacheHelper::makeKey('renters'), 600, function () {
            $applications = RenterApplication::with('user');

            // filter by status
            if (request()->has('statuses')) {
                $applications = $applications->whereIn('status', request('statuses'));
            }

            // search
            if (request()->has('search')) {
                $applications = $applications->whereHas('user', function ($query) {
                    return $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%')
                        ->orWhere('phone', 'like', '%' . request('search') . '%');
                });
            }

            $applications = $applications
                ->orderBy('created_at', 'desc')
                ->paginate(request('per_page', 20));

            // add created_at to users
            $applications = $applications->setCollection($applications->getCollection()->map(function ($application) {
                $application->makeHidden([
                    'identity_document_verified',
                    'driver_license_verified',
                    'terms_agreed',
                    'reason',
                    'identity_document_uploaded',
                    'driver_license_uploaded',
                    'approved',
                ])->makeVisible([
                    'id',
                    'created_at',
                ]);
                $application->name = $application->user->name;
                $application->email = $application->user->email;
                $application->phone = $application->user->phone;
                $application->type = 'renter';
                unset($application->user);
                return $application;
            }));

            return $applications;
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $application = RenterApplication::with([
            'user',
            'identityDocument',
            'driverLicense',
        ])->findOrFail($id);
        $application->makeVisible([
            'id',
            'created_at',
        ]);
        $application->name = $application->user->name;
        $application->type = 'renter';
        return response()->json($application);
    }

    public function inReview($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $application = RenterApplication::findOrFail($id);
                $application->status = 'in-review';
                $application->save();
                Cache::tags(['renters'])->flush();
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
                $application = RenterApplication::findOrFail($id);

                $user = User::findOrFail($application->user_id);

                if (!$application) {
                    // return 400 response user already has ongoing request
                    return response()->json([
                        'message' => __('messages.error.missing_data'),
                        'data' => null,
                        'error' => true
                    ], 400);
                }
                $application->update([
                    'status' => 'approved',
                    'identity_document_verified' => true,
                    'driver_license_verified' => true,
                ]);

                $verifyIdentity = IdentityDocument::whereVerifiedAt(null)
                    ->where('id', $application->identity_document_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                if ($verifyIdentity) {
                    // grant the corosponding privilege to user
                    $user->grantPrivilege('book_car');

                    // assign renter role
                    $user->assignRole('renter');
                }
                // update driver license verified at
                $verifyLicense = DriverLicense::whereVerifiedAt(null)
                    ->where('id', $application->driver_license_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                if ($verifyLicense) {
                    // grant the corosponding privilege to user
                    $user->grantPrivilege('rent_without_driver');
                }
                Cache::tags(['renters'])->flush();
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
                $application = RenterApplication::findOrFail($id);
                $application->status = 'rejected';
                $application->reason = request('reason');
                $application->save();
                Cache::tags(['renters'])->flush();
                Cache::tags(['counters'])->flush();
            });

            return response()->json(['message' => 'Application is rejected']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}