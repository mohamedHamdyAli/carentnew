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
        
        /* 
            @Brand routes
        */
        Route::apiResource('brands', 'BrandController');
        Route::apiResource('models', 'BrandModelController');

        /* 
            @Location routes
        */
        Route::apiResource('countries', 'CountryController');
        Route::apiResource('states', 'StateController');

        /* 
            @Category routes
        */
        Route::apiResource('categories', 'CategoryController');

        /* 
            @Authentication routes
        */
        Route::prefix('auth')->group(function () {
            Route::post('/login', 'AuthController@login');
            Route::post('/register', 'AuthController@register');
            Route::post('/logout', 'AuthController@logout');
            Route::post('/refresh', 'AuthController@refresh');
            Route::post('/me', 'AuthController@me');
            Route::post('/send-email-otp', 'AuthController@sendEmailOtp');
            Route::post('/verify-email-otp', 'AuthController@verifyEmailOtp');
            Route::post('/send-phone-otp', 'AuthController@sendPhoneOtp');
            Route::post('/verify-phone-otp', 'AuthController@verifyPhoneOtp');
            Route::post('/reset-password', 'AuthController@resetPassword');
            Route::post('/change-password', 'AuthController@changePassword');
        });
    }
);
