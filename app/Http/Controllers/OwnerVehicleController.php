<?php

namespace App\Http\Controllers;

use App\Models\TempFile;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\VehicleInsurance;
use App\Models\VehicleLicense;
use App\Models\VehiclePricing;
use App\Models\VehicleVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class OwnerVehicleController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        $key = 'owner-vehicles-' . app()->getLocale() . '-' . $userId . '-' . request('page');
        $data = Cache::tags(['vehicles'])->remember($key, 3600, function () use ($userId) {
            $results = Vehicle::where('user_id', $userId)->simplePaginate();
            return $results->setCollection($results->getCollection()->makeVisible(['verified', 'thumbnail_url', 'status']));
        });

        return response()->json([
            'status' => __('messages.r_success'),
            'data' => $data,
            'error' => null,
        ]);
    }

    public function store()
    {
        // validate the request
        $country = request()->header('Country');
        $isUpdate = false;
        if (request()->has('id') && request('id') !== null && request('id') !== "" && Vehicle::find(request('id')) !== null) {
            $isUpdate = true;
        }

        $uniquePlate = $isUpdate ? ',id,' . request('id') : '';
        
        $this->validate(request(), [
            'state_id' => ['required', 'exists:states,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['sometimes', 'exists:brand_models,id'],
            'thumbnail' => ['nullable', 'sometimes', 'exists:temp_files,id'],
            'plate_number' => ['required', 'unique:vehicles' . $uniquePlate],
            'manufacture_year' => ['required', 'integer', 'min:' . (Carbon::now()->year - env('VEHICLE_YEAR_MAX')), 'max:' . (Carbon::now()->year + 1)],
            'color' => ['required', 'string', 'max:255'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'seat_count' => ['required', 'integer', 'min:1', 'max:20'],
            'features' => ['nullable', 'sometimes', 'array'],
            'features.*' => ['sometimes', 'exists:features,id'],
            'images' => ['nullable', 'sometimes', 'array', 'max:10'],
            'images.*' => ['required', 'exists:temp_files,id'],
            'license' => ['nullable', 'sometimes', 'array'],
            'license.front_image' => ['required_with:license', 'exists:temp_files,id'],
            'license.back_image' => ['required_with:license', 'exists:temp_files,id'],
            'license.expire_at' => ['required_with:license', 'date:Y-m-d', 'after:' . Carbon::now()->addMonths(3)->format('Y-m-d')],
            'insurance' => ['nullable', 'sometimes', 'array'],
            'insurance.image' => ['required_with:insurance', 'exists:temp_files,id'],
            'insurance.expire_at' => ['required_with:insurance', 'date:Y-m-d', 'after:' . Carbon::now()->addMonths(3)->format('Y-m-d')],
            'pricing.daily_price' => ['required', 'integer', 'min:1'],
            'pricing.week_to_month' => ['sometimes', 'nullable', 'integer', 'max:' . request('pricing.daily_price')],
            'pricing.month_or_more' => ['sometimes', 'nullable', 'integer', 'max:' . request('pricing.week_to_month')],
            'pricing.has_driver' => ['required_with:driver_daily_price', 'boolean'],
            'pricing.driver_daily_price' => ['nullable', 'required_if:pricing.has_driver,true', 'integer', 'min:1'],
            'pricing.is_driver_required' => ['sometimes', 'boolean'],
        ]);

        $vehicleData = array_merge(
            [
                'user_id' => auth()->user()->id,
                'country_id' => $country,
            ],
            request()->only([
                'state_id',
                'category_id',
                'brand_id',
                'model_id',
                'plate_number',
                'manufacture_year',
                'color',
                'fuel_type_id',
                'seat_count',
            ])
        );

        if ($isUpdate) {
            $vehicleData['verified_at'] = null;
            VehicleVerification::where('vehicle_id', request('id'))->update(['status' => 'created']);
            $vehicle = Vehicle::find(request('id'));
            $vehicle->update($vehicleData);
        } else {
            $vehicle = Vehicle::create($vehicleData);
        }


        if ($isUpdate) {
            VehicleVerification::where('vehicle_id', request('id'))->update(['status' => 'created']);
        } else {
            VehicleVerification::create([
                'vehicle_id' => $vehicle->id,
                'status' => 'created',
            ]);
        }

        if (request()->has('thumbnail') && request('thumbnail') !== null) {
            $vehicle->updateThumbnail(request('thumbnail'));
        }

        if (request()->has('features') && request('features') !== null) {
            $vehicle->syncVehicleFeatures(request('features'));
        }

        if (request()->has('images') && request('images') !== null) {
            $vehicle->addVehicleImages(request('images'));
        }

        if (request()->has('license') && request('license') !== null) {
            $frontImage = TempFile::where('id', request('license.front_image'))->first();
            $backImage = TempFile::where('id', request('license.back_image'))->first();

            // move the file secure vehicles folder
            $newFrontImagePath = 'secure/vehicles/' . $vehicle->id . '/license/' . Carbon::now()->timestamp . '_' . $frontImage->name;
            Storage::copy($frontImage->path, $newFrontImagePath);

            // move the file secure vehicles folder
            $newBackImagePath = 'secure/vehicles/' . $vehicle->id . '/license/' . Carbon::now()->timestamp . '_' . $backImage->name;
            Storage::copy($backImage->path, $newBackImagePath);

            // make license data object
            $licenseData = [
                'vehicle_id' => $vehicle->id,
                'front_image' => $newFrontImagePath,
                'back_image' => $newBackImagePath,
                'expire_at' => request('license.expire_at'),
            ];

            // check if the vehicle has license
            $licenseExist = VehicleLicense::whereVehicleId($vehicle->id)->orderBy('created_at', 'desc')->first();
            if ($licenseExist) {
                $licenseData['id'] = $licenseExist->id;
            }

            // add the image to the vehicle
            VehicleLicense::updateOrCreate($licenseData);
        }

        if (request()->has('insurance') && request('insurance') !== null) {
            $image = TempFile::where('id', request('insurance.image'))->first();

            // move the file public vehicles folder
            $newImagePath = 'secure/vehicles/' . $vehicle->id . '/insurance/' . Carbon::now()->timestamp . '_' . $image->name;
            Storage::copy($image->path, $newImagePath);

            // check if the vehicle has insurance
            $insuranceExist = VehicleInsurance::whereVehicleId($vehicle->id)->orderBy('created_at', 'desc')->first();

            // make insurance data object
            $insuranceData = [
                'vehicle_id' => $vehicle->id,
                'image' => $newImagePath,
                'expire_at' => request('insurance.expire_at'),
            ];

            // check if the vehicle has insurance
            if ($insuranceExist) {
                $insuranceData['id'] = $insuranceExist->id;
            }

            // update vehicle insurance
            VehicleInsurance::updateOrCreate($insuranceData);
        }

        // make pricing data object
        $pricingData = [
            'vehicle_id' => $vehicle->id,
            'daily_price' => request('pricing.daily_price'),
            'week_to_month' => request('pricing.week_to_month'),
            'month_or_more' => request('pricing.month_or_more'),
            'has_driver' => request('pricing.has_driver'),
            'driver_daily_price' => request('pricing.driver_daily_price'),
            'is_driver_required' => request('pricing.is_driver_required'),
        ];

        // check if the vehicle has pricing
        $pricingExist = VehiclePricing::whereVehicleId($vehicle->id)->orderBy('created_at', 'desc')->first();
        if ($pricingExist) {
            VehiclePricing::find($pricingExist->id)->update($pricingData);
        } else {
            VehiclePricing::create($pricingData);
        }

        // if vehicle created successfully clear all vehicles cache
        Cache::tags(['vehicles'])->flush();

        return $this->vehicle($vehicle->id);
    }

    /**
     * @param $id
     * @return Vehicle
     */
    public function vehicle($id)
    {
        $vehicle = Vehicle::whereUserId(auth()->user()->id)
            ->with([
                'features',
                'VehicleImages',
                'VehicleLicense',
                'VehicleInsurance',
                'VehiclePricing',
                'VehicleVerification'
            ])->findOrFail($id)
            ->makeVisible([
                'state_id',
                'category_id',
                'brand_id',
                'model_id',
                'thumbnail_url',
                'fuel_type_id',
                'manufacture_year',
                'plate_number',
                'color',
                'verified',
            ])
            ->makeHidden([
                'state',
                'category',
                'daily_price',
                'brand',
                'model',
                'rating',
            ]);

        foreach ($vehicle->VehicleImages as $image) {
            $image->image = url(Storage::url($image->image));
        };

        return $vehicle;
    }

    public function verification($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // check if the vehicle is already verified
        if ($vehicle->isVerified()) {
            return response()->json([
                'message' => __('messages.error.vehicle_verified'),
                'data' => null,
                'error' => true,
            ], 400);
        }

        // if vehicle has ongoin verification request
        $ongoingVerification = VehicleVerification::whereVehicleId($vehicle->id)->whereStatus('in-review')->first();
        if ($ongoingVerification) {
            return response()->json([
                'message' => __('messages.error.request_ongoing'),
                'data' => null,
                'error' => true,
            ], 400);
        }

        // check if the vehicle has license and insurance
        if (!$vehicle->hasLicense() || !$vehicle->hasInsurance()) {
            return response()->json([
                'message' => __('messages.error.data_missing'),
                'data' => null,
                'error' => true,
            ], 400);
        }

        // update or create vehicle verification if not exist
        $vehicleVerification = VehicleVerification::updateOrCreate([
            'vehicle_id' => $vehicle->id,
        ], [
            'vehicle_license_id' => $vehicle->VehicleLicense->id,
            'vehicle_insurance_id' => $vehicle->VehicleInsurance->id,
            'status' => 'in-review',
        ]);

        // send email to admin
        // TODO: send email to admin

        return response()->json([
            'message' => __('messages.success.vehicle_submitted'),
            'data' => $vehicleVerification,
            'error' => false,
        ], 200);
    }

    public function dev($id)
    {
        // delete applications

        $application =
            VehicleVerification::where('vehicle_id', $id)
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
            $vehicle = Vehicle::find($id);
            if (request('status') && in_array(request('status'), ['approved', 'rejected', 'created', 'in-review'])) {
                $application->update([
                    'status' => request('status'),
                    'reason' => request('reason') ?? null
                ]);

                if (request('status') === 'approved') {
                    $application->update([
                        'vehicle_insurance_verified' => true,
                        'vehicle_license_verified' => true,
                    ]);
                    VehicleInsurance::whereVerifiedAt(null)->where('id', $application->vehicle_insurance_id)->update([
                        'verified_at' => now()
                    ]);
                    VehicleLicense::whereVerifiedAt(null)->where('id', $application->vehicle_license_id)->update([
                        'verified_at' => now()
                    ]);
                    $vehicle->update([
                        'verified_at' => now()
                    ]);
                } else {
                    $vehicle->update([
                        'verified_at' => null
                    ]);
                }
            }
        }
    }

    public function deleteImage($id)
    {
        try {
            $image = VehicleImage::findOrFail($id);
            // delete the image from the storage
            Storage::delete($image->image);
            return $image->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Bad Request',
                'data' => null,
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    public function destroy()
    {

        // if vehicle deleted successfully clear all vehicles cache
        Cache::tags(['vehicles'])->flush();
    }
}
