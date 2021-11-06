<?php

use App\Http\Controllers\BrandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        'prefix' => 'v1',
        'middleware' => ['api'],
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        // test email
        Route::get('/test-email', function () {
            // return Mail::to('mahmoud.ali.kassem@gmail.com')->send(new \App\Mail\EmailOtp(123456));
            return new \App\Mail\EmailOtp(123456);
        });

        // Brand routes
        Route::apiResource('brands', 'BrandController');
        Route::apiResource('models', 'BrandModelController');
        // Location Routes
        Route::apiResource('countries', 'CountryController');
        Route::apiResource('states', 'StateController');
    }
);
