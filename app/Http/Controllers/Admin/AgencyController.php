<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Helpers\CountryHelper;
use App\Http\Controllers\Controller;
use App\Jobs\AddRoleFcmSub;
use App\Models\AgencyApplication;
use App\Models\BusinessDocument;
use App\Models\IdentityDocument;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
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

        $data = Cache::tags(['agencies'])->remember(CacheHelper::makeKey('agencies'), 600, function () {
            $applications = AgencyApplication::whereHas('user')->with('user');

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
                    'identity_document_uploaded',
                    'business_document_verified',
                    'business_document_uploaded',
                    'terms_agreed',
                    'reason',
                    'approved',
                ])->makeVisible([
                    'id',
                    'created_at',
                ]);
                $application->name = $application->user->name;
                $application->email = $application->user->email;
                $application->phone = $application->user->phone;
                $application->type = 'agency';
                unset($application->user);
                return $application;
            }));

            return $applications;
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $application = AgencyApplication::with([
            'user',
            'identityDocument',
            'businessDocument',
        ])->with('identityDocument')->findOrFail($id);
        $application->makeVisible([
            'id',
            'created_at',
        ]);
        $application->name = $application->user->name;
        $application->type = 'agency';
        return response()->json($application);
    }

    public function inReview($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $application = AgencyApplication::findOrFail($id);
                $application->status = 'in-review';
                $application->save();
                Cache::tags(['agencies'])->flush();
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
                $application = AgencyApplication::findOrFail($id);

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
                    'business_document_verified' => true,
                ]);

                $verifyIdentity = IdentityDocument::whereVerifiedAt(null)
                    ->where('id', $application->identity_document_id)
                    ->update([
                        'verified_at' => now()
                    ]);

                // update business document verified at
                $verifyBusiness = BusinessDocument::whereVerifiedAt(null)->where('id', $application->business_document_id)->update([
                    'verified_at' => now()
                ]);

                // assign role as a resullt of verification
                $user->assignRole('agency');

                Log::info("user country: " . $user->country);

                if ($user->country && $user->fcm && $user->language) {
                    $data = [
                        'fcm' => $user->fcm,
                        'userCountry' => $user->country,
                        'userLang' => $user->language,
                        'countryCode' => $user->country,
                        'lang' => $user->language,
                        'role' => 'agency',
                    ];

                    AddRoleFcmSub::dispatch($data);
                }

                Cache::tags(['agencies'])->flush();
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
                $application = AgencyApplication::findOrFail($id);
                $application->status = 'rejected';
                $application->reason = request('reason');
                $application->save();
                Cache::tags(['agencies'])->flush();
                Cache::tags(['counters'])->flush();
            });

            return response()->json(['message' => 'Application is rejected']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
