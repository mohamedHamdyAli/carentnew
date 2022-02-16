<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * get vehicles paginatied and filtered
         * mandatory filter fields: country_id
         * optional filter fields: state_id,
         * brand_id, model_id, category_id,
         * fuel_type_id, doors_count, min_year,
         * min_rating, rented_before
         * where active = true and verified_at is not null
         */

        // get Country from request header
        $country = request()->header('Country');

        $data = Cache::tags(['vehicles'])->remember("vehicles:" . json_encode(request()->all()), 3600, function () use ($country) {
            $result = Vehicle::where('country_id', $country);
            if (request()->has('search')) {
                $result = $result->whereHas('user', function ($query) {
                    return $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%')
                        ->orWhere('phone', 'like', '%' . request('search') . '%');
                })->orWhere('plate_number', 'like', '%' . request()->get('search') . '%');
            }

            if (request()->has('state_id')) {
                $result = $result->where('state_id', request('state_id'));
            }

            if (request()->has('brand_id')) {
                $result = $result->where('brand_id', request('brand_id'));
            }

            if (request()->has('model_id')) {
                $result = $result->where('model_id', request('model_id'));
            }

            if (request()->has('category_id')) {
                $result = $result->where('category_id', request('category_id'));
            }

            if (request()->has('fuel_type_id')) {
                $result = $result->where('fuel_type_id', request('fuel_type_id'));
            }

            if (request()->has('seat_count')) {
                $result = $result->where('seat_count', request('seat_count'));
            }

            if (request()->has('min_year')) {
                $result = $result->where('manufacture_year', '>=', request('min_year'));
            }

            if (request()->has('min_rating')) {
                $result = $result->where('rating', '>=', request('min_rating'));
            }

            if (request()->has('min_daily_price')) {
                $result = $result->whereHas('VehiclePricing', function ($query) {
                    $query->where('daily_price', '>=', request('min_daily_price'));
                });
            }

            if (request()->has('max_daily_price')) {
                $result = $result->whereHas('VehiclePricing', function ($query) {
                    $query->where('daily_price', '<=', request('max_daily_price'));
                });
            }
            if (request()->has('has_driver')) {
                $result = $result->whereHas('VehiclePricing', function ($query) {
                    $query->where('has_driver', request('has_driver'));
                });
            }
            if (request()->has('with_features')) {
                $result = $result->whereHas('VehicleFeatures', function ($query) {
                    $query->whereIn('feature_id', request('with_features'));
                });
            }
            if (request()->has('rented_before')) {
                $result = $result->where('rented', '>', 0);
            }
            // TODO: add filter for features
            if (request()->has('from_date')) {
                $result = $result->whereDoesntHave('orders', function ($query) {
                    $query->Overlaps(request('from_date'), request('to_date'));
                });
            }

            if (!request()->has('with_inactive')) {
                $result = $result->where('active', true);
            }

            $result =
                $result->whereNotNull('verified_at')
                ->paginate(request('per_page'));

            return $result->setCollection($result->getCollection()->makeVisible(['thumbnail_url', 'created_at']));
        });

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        //
        return Vehicle::whereActive(true)
            ->where('Verified_at', '!=', null)
            ->findOrFail($id)
            ->makeVisible([
                'images',
                'features',
                'thumbnail_url',
                'pricing',
                'fuel_type',
                'rating',
                'rating_count', 'color'
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }
}
