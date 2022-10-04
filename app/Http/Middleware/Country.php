<?php

namespace App\Http\Middleware;

use App\Models\Country as ModelsCountry;
use Closure;
use Illuminate\Http\Request;

class Country
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // get Country from request header
        $country = $request->header('Country');
        // if country is not set, return error
        if (!$country) {
            return response()->json([
                'message' => 'Country is not set'
            ], 400);
        }
        // convert country code to id
        $iCountry = cache()->rememberForever("country-{$country}", function () use ($country) {
            return ModelsCountry::where('country_code', strtoupper($country))->first();
        });
        // if country_id is not set, return error
        if (!$iCountry) {
            return response()->json([
                'message' => 'Country is incorrect or unsupported'
            ], 400);
        }
        // set Country header with country_code
        $request->headers->set('CountryCode', $iCountry->country_code);
        // update Country header with country_id
        $request->headers->set('Country', $iCountry->id);
        return $next($request);
    }
}
