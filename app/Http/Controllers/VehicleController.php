<?php

namespace App\Http\Controllers;

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
            $result = Vehicle::where('country_id', $country)
                ->when(request('state_id'), function ($query) {
                    return $query->where('state_id', request('state_id'));
                })
                ->when(request('brand_id'), function ($query) {
                    return $query->where('brand_id', request('brand_id'));
                })
                ->when(request('model_id'), function ($query) {
                    return $query->where('model_id', request('model_id'));
                })
                ->when(request('category_id'), function ($query) {
                    return $query->where('category_id', request('category_id'));
                })
                ->when(request('fuel_type_id'), function ($query) {
                    return $query->where('fuel_type_id', request('fuel_type_id'));
                })
                ->when(request('doors_count'), function ($query) {
                    return $query->where('doors_count', request('doors_count'));
                })
                ->when(request('min_year'), function ($query) {
                    return $query->where('manufacture_year', '>=', request('min_year'));
                })
                ->when(request('min_rating'), function ($query) {
                    return $query->where('rating', '>=', request('min_rating'));
                })
                ->when(request('rented_before'), function ($query) {
                    return $query->where('rented', '>', 0);
                })
                ->where('active', true)
                ->whereNotNull('verified_at')
                ->paginate(15);

            return $result->setCollection($result->getCollection()->makeVisible('thumbnail_url'));
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
        return Vehicle::whereActive(true)->where('Verified_at', '!=', null)->findOrFail($id)->makeVisible(['images', 'features', 'thumbnail_url', 'pricing']);
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
