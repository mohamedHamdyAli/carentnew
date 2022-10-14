<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Helpers\CountryHelper;
use App\Http\Controllers\Controller;
use App\Jobs\AddRoleFcmSub;
use App\Models\DriverLicense;
use App\Models\IdentityDocument;
use App\Models\RenterApplication;
use App\Models\User;
use App\Notifications\ApplicationAlert;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            $applications = RenterApplication::whereHas('user')->with('user');

            // filter by status
            if (request()->has('statuses')) {
                $applications = $applications->whereIn('status', request('statuses'));
            } else {
                $applications = $applications->where('status', '!=', 'created');
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

                // send notification
                $user = User::findOrFail($application->user_id);
                $user->notify(new ApplicationAlert([
                    'title_en' => 'Application in review',
                    'title_ar' => 'مراجعة الطلب',
                    'body_en' => 'Your renter application is in review',
                    'body_ar' => 'طلب الإنضمام كمستأجر قيد المراجعة',
                    'alert_type' => 'info', // info, success, warning, danger
                ]));
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

                // assign renter role
                $user->assignRole('renter');

                // grant the corosponding privilege to user
                $user->grantPrivilege('book_car');
                // update driver license verified at
                $verifyLicense = DriverLicense::whereVerifiedAt(null)
                    ->where('id', $application->driver_license_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                // grant the corosponding privilege to user
                $user->grantPrivilege('rent_without_driver');

                Log::info("user country: " . $user->country);

                if ($user->country && $user->fcm && $user->language) {
                    $data = [
                        'fcm' => $user->fcm,
                        'userCountry' => $user->country,
                        'userLang' => $user->language,
                        'countryCode' => $user->country,
                        'lang' => $user->language,
                        'role' => 'renter',
                    ];

                    AddRoleFcmSub::dispatch($data);
                }

                // send notification
                $user->notify(new ApplicationAlert([
                    'title_en' => 'Application approved',
                    'title_ar' => 'تم الموافقة على الطلب',
                    'body_en' => 'Your renter application is approved',
                    'body_ar' => 'تم الموافقة على طلب الإنضمام كمستأجر',
                    'alert_type' => 'success', // info, success, warning, danger
                ]));

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

                // send notification
                $user = User::findOrFail($application->user_id);
                $user->notify(new ApplicationAlert([
                    'title_en' => 'Application rejected',
                    'title_ar' => 'تم رفض الطلب',
                    'body_en' => 'Your renter application is rejected',
                    'body_ar' => 'تم رفض طلب الإنضمام كمستأجر',
                    'alert_type' => 'danger', // info, success, warning, danger
                ]));
                
                Cache::tags(['renters'])->flush();
                Cache::tags(['counters'])->flush();
            });

            return response()->json(['message' => 'Application is rejected']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
