<?php

namespace App\Http\Controllers;

use App\Models\TempFile;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\VehicleInsurance;
use App\Models\VehicleLicense;
use App\Models\VehiclePricing;
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
            return $results->setCollection($results->getCollection()->makeVisible(['verified', 'thumbnail_url']));
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
        if (request()->has('id') && Vehicle::find(request('id')) !== null) {
            $isUpdate = true;
        }
        $uniquePlate = $isUpdate ? ',id,' . request('id') : '';
        $this->validate(request(), [
            'state_id' => ['required', 'exists:states,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['sometimes', 'exists:brand_models,id'],
            'thumbnail' => ['sometimes', 'exists:temp_files,id'],
            'plate_number' => ['required', 'unique:vehicles' . $uniquePlate],
            'manufacture_year' => ['required', 'integer', 'min:' . (Carbon::now()->year - env('VEHICLE_YEAR_MAX')), 'max:' . (Carbon::now()->year + 1)],
            'color' => ['required', 'string', 'max:255'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'seat_count' => ['required', 'integer', 'min:1', 'max:20'],
            'features' => ['sometimes', 'array'],
            'features.*' => ['sometimes', 'exists:features,id'],
            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => ['required', 'exists:temp_files,id'],
            'license' => ['sometimes', 'array'],
            'license.front_image' => ['required_with:license', 'exists:temp_files,id'],
            'license.back_image' => ['required_with:license', 'exists:temp_files,id'],
            'license.expire_at' => ['required_with:license', 'date:Y-m-d', 'after:' . Carbon::now()->addMonths(3)->format('Y-m-d')],
            'insurance' => ['sometimes', 'array'],
            'insurance.image' => ['required_with:insurance', 'exists:temp_files,id'],
            'insurance.expire_at' => ['required_with:insurance', 'date:Y-m-d', 'after:' . Carbon::now()->addMonths(3)->format('Y-m-d')],
            'pricing.daily_price' => ['required', 'integer', 'min:1'],
            'pricing.week_to_month' => ['sometimes', 'nullable', 'integer', 'max:' . request('pricing.daily_price')],
            'pricing.month_or_more' => ['sometimes', 'nullable', 'integer', 'max:' . request('pricing.week_to_month')],
            'pricing.has_driver' => ['required_with:driver_daily_price', 'boolean'],
            'pricing.driver_daily_price' => ['required_if:pricing.has_driver,true', 'integer', 'min:1'],
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
            $vehicleData['id'] = request('id');
        }

        $vehicle = Vehicle::updateOrCreate($vehicleData);

        if (request()->has('thumbnail') && request('thumbnail') != null) {
            $vehicle->updateThumbnail(request('thumbnail'));
        }

        if (request()->has('features')) {
            $vehicle->syncVehicleFeatures(request('features'));
        }

        if (request()->has('images')) {
            $vehicle->addVehicleImages(request('images'));
        }

        if (request()->has('license')) {
            $frontImage = TempFile::where('id', request('license.front_image'))->first();
            $backImage = TempFile::where('id', request('license.back_image'))->first();

            // move the file secure vehicles folder
            $newFrontImagePath = 'secure/vehicles/' . $vehicle->id . '/license/' . Carbon::now()->timestamp . '_' . $frontImage->name;
            Storage::move($frontImage->path, $newFrontImagePath);

            // move the file secure vehicles folder
            $newBackImagePath = 'secure/vehicles/' . $vehicle->id . '/license/' . Carbon::now()->timestamp . '_' . $backImage->name;
            Storage::move($backImage->path, $newBackImagePath);

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

        if (request()->has('insurance')) {
            $image = TempFile::where('id', request('insurance.image'))->first();

            // move the file public vehicles folder
            $newImagePath = 'secure/vehicles/' . $vehicle->id . '/insurance/' . Carbon::now()->timestamp . '_' . $image->name;
            Storage::move($image->path, $newImagePath);

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
            $pricingData['id'] = $pricingExist->id;
        }

        // update or create vehicle pricing
        VehiclePricing::updateOrCreate($pricingData);

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
            ])->findOrFail($id)
            ->makeVisible([
                'state_id',
                'category_id',
                'brand_id',
                'model_id',
                'fuel_type_id',
                'manufacture_year',
                'plate_number',
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

    public function update()
    {

        // if vehicle updated successfully clear all vehicles cache
        Cache::tags(['vehicles'])->flush();
    }


    public function destroy()
    {

        // if vehicle deleted successfully clear all vehicles cache
        Cache::tags(['vehicles'])->flush();
    }

    private function syncFeatures($vehicle, $features)
    {
        $vehicle->VehicleFeatures()->sync($features);
    }
}
