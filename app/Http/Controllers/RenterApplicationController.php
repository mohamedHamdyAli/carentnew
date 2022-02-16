<?php

namespace App\Http\Controllers;

use App\Models\DriverLicense;
use App\Models\IdentityDocument;
use App\Models\RenterApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenterApplicationController extends Controller
{
    public function status()
    {
        $ongoingApplication =
            RenterApplication::where('user_id', auth()->user()->id)
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
            RenterApplication::where('user_id', auth()->user()->id)
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

        $application =  RenterApplication::create([
            'user_id' => auth()->id(),
            'terms_agreed' => true,
            'identity_document_verified' => IdentityDocument::where('user_id', auth()->id())->where('verified_at', '!=', null)->first() ? true : false,
            'driver_license_verified' => DriverLicense::where('user_id', auth()->id())->where('verified_at', '!=', null)->first() ? true : false,
        ]);

        return response()->json([
            'message' => __('messages.success.agreement_signed'),
            'data' => RenterApplication::find($application->id),
            'error' => null,
        ]);
    }

    public function submit()
    {
        $userId = auth()->user()->id;
        // find on going application
        $application =
            RenterApplication::where('user_id', $userId)
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
            if (in_array($application->status, ['approved', 'in-review'])) {
                // return 400 response user already has ongoing or approved request
                return response()->json([
                    'message' => $application->status === 'approved' ? __('messages.error.approved') : __('messages.error.request_ongoing'),
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

        $unverifiedLicenese = $this->getDriverLincese();

        $application->update([
            'identity_document_id' => $identityDocument->id,
            'driver_license_id' => $unverifiedLicenese->id,
            'status' => 'in-review',
            'reason' => null,
        ]);

        // TODO: send email to admin

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
            return RenterApplication::where('user_id', $userId)->delete();
        }
    }

    public function dev()
    {
        $user = auth()->user();

        // delete applications
        $application =
            RenterApplication::where('user_id', $user->id)
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
                        'driver_license_verified' => request('verify_driver_license') ?? false,
                    ]);

                    // update identity document verified at 
                    if (request()->has('verify_identity_document') && request('verify_identity_document')) {
                        // update identity document verified at
                        $verifyIdentity = IdentityDocument::whereVerifiedAt(null)->where('id', $application->identity_document_id)->update([
                            'verified_at' => now()
                        ]);

                        if ($verifyIdentity) {
                            // grant the corosponding privilege to user
                            Auth::user()->grantPrivilege('book_car');

                            // assign renter role
                            Auth::user()->assignRole('renter');
                        }
                    }

                    // update driver license verified at
                    if (request()->has('verify_driver_license') && request('verify_driver_license')) {
                        // update driver license verified at
                        $verifyLicense = DriverLicense::whereVerifiedAt(null)->where('id', $application->driver_license_id)->update([
                            'verified_at' => now()
                        ]);

                        if ($verifyLicense) {
                            // grant the corosponding privilege to user
                            Auth::user()->grantPrivilege('rent_without_driver');
                        }
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

    private function getDriverLincese()
    {
        return DriverLicense::where('user_id', auth()->user()->id)
            ->first();
    }
}
