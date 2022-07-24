<?php

namespace App\Http\Controllers;

use App\Models\AgencyApplication;
use App\Models\BusinessDocument;
use App\Models\IdentityDocument;
use Auth;
use Cache;
use Illuminate\Http\Request;

class AgencyApplicationController extends Controller
{
    public function status()
    {
        $ongoingApplication =
            AgencyApplication::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $ongoingApplication,
            'error' => null
        ]);
    }

    public function signAgreement()
    {
        // find on going application
        $ongoingApplication =
            AgencyApplication::where('user_id', auth()->user()->id)
            ->where('status', '!=', 'in-review')
            ->first();

        if ($ongoingApplication) {
            // return 400 response user already has ongoing or approved request
            return response()->json([
                'message' => $ongoingApplication->status === 'approved' ? __('messages.error.approved') : __('messages.error.request_ongoing'),
                'data' => null,
                'error' => true
            ], 400);
        }

        $application =  AgencyApplication::create([
            'user_id' => auth()->id(),
            'terms_agreed' => true,
            'identity_document_verified' => IdentityDocument::where('user_id', auth()->id())->where('verified_at', '!=', null)->first() ? true : false,
            'business_document_verified' => BusinessDocument::where('user_id', auth()->id())->where('verified_at', '!=', null)->first() ? true : false,
        ]);

        return response()->json([
            'message' => __('messages.success.agreement_signed'),
            'data' => AgencyApplication::find($application->id),
            'error' => null,
        ]);
    }

    public function submit()
    {
        $userId = auth()->user()->id;
        // find on going application
        $application =
            AgencyApplication::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$application) {
            // return 400 response user already has ongoing request
            return response()->json([
                'message' => __('messages.error.agreement'),
                'data' => null,
                'error' => true
            ], 400);
        } else {
            if (in_array($application->status, [/** 'approved',*/ 'in-review'])) {
                // return 400 response user already has ongoing or approved request
                return response()->json([
                    'message' =>
                    /** $application->status === 'approved' ? __('messages.error.approved') : */
                    __('messages.error.request_ongoing'),
                    'data' => null,
                    'error' => true
                ], 400);
            }
        }

        $identityDocument = $this->getIdentityDocument();

        if (!$identityDocument) {
            return response()->json([
                'message' => __('messages.error.data_missing'),
                'data' => null,
                'error' => true,
            ], 400);
        }

        $businessDocument = $this->getBusinessDocument();

        if (!$businessDocument) {
            return response()->json([
                'message' => __('messages.error.data_missing'),
                'data' => null,
                'error' => true,
            ], 400);
        }

        $application->update([
            'identity_document_id' => $identityDocument->id,
            'business_document_id' => $businessDocument->id,
            'status' => 'in-review',
            'reason' => null,
        ]);

        // TODO: send email to admin

        Cache::tags(['counters'])->flush();

        return response()->json([
            'message' => __('messages.success.application_submitted'),
            'data' => $application,
            'error' => null,
        ]);
    }

    public function devDelete()
    {
        $userId = auth()->user()->id;
        // find on going application
        if (app()->environment('local')) {
            return AgencyApplication::where('user_id', $userId)->delete();
        }
    }

    public function dev()
    {
        $userId = auth()->user()->id;

        // delete applications
        $application =
            AgencyApplication::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$application) {
            // return 400 response user already has ongoing request
            return response()->json([
                'message' => __('messages.error.missing_data'),
                'data' => null,
                'error' => true
            ], 400);
        }

        // Dev only set status to approved or rejected
        if (app()->environment('local')) {
            if (request('status') && in_array(request('status'), ['approved', 'rejected', 'created', 'in-review'])) {
                $application->update([
                    'status' => request('status'),
                    'reason' => request('reason') ?? null
                ]);

                if (request('status') === 'approved') {
                    $application->update([
                        'identity_document_verified' => request('verify_identity_document') ?? false,
                        'business_document_verified' => request('business_document_verified') ?? false,
                    ]);

                    // update identity document verified at
                    $verifyIdentity = IdentityDocument::whereVerifiedAt(null)->where('id', $application->identity_document_id)->update([
                        'verified_at' => now()
                    ]);

                    // update business document verified at
                    $verifyBusiness = BusinessDocument::whereVerifiedAt(null)->where('id', $application->business_document_id)->update([
                        'verified_at' => now()
                    ]);

                    if ($verifyBusiness && $verifyIdentity) {
                        // assign role as a resullt of verification
                        Auth::user()->assignRole('agency');
                    }
                }
            }
        }
    }

    private function getIdentityDocument()
    {
        return IdentityDocument::where('user_id', auth()->user()->id)
            ->first();
    }

    private function getBusinessDocument()
    {
        return BusinessDocument::where('user_id', auth()->user()->id)
            ->first();
    }
}
