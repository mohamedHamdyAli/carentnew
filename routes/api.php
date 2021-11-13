<?php

use App\Http\Controllers\AuthController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\PasswordController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        Route::get('/test', function () {
            // return [
            //    'data' => DB::select('select * from personal_access_tokens where (last_used_at < ?) or (last_used_at is null and created_at < ?)', [Carbon::now()->subDays(30), Carbon::now()->subDays(30)]),
            //    'monthAgo' => Carbon::now()->subDays(30),
            //];
            //return User::factory(1)->hasBalance()->isActive()->create();
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
            @Features routes
        */
        Route::apiResource('features', 'FeatureController');

        /* 
            @FuelType routes
        */
        Route::apiResource('fuel-types', 'FuelTypeController');

        /* 
            @Authentication routes
        */
        Route::prefix('auth')->group(function () {
            Route::post('/login/email', [AuthController::class, 'loginWithEmailAndPassword']);
            Route::post('/login/phone', [AuthController::class, 'loginWithPhoneAndPassword']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
            Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
            Route::post('/send-email-otp', [AuthController::class, 'sendEmailOtp'])->middleware(['auth:sanctum', 'throttle:email-otp']);
            Route::post('/verify/{type}', [AuthController::class, 'verify'])->middleware('auth:sanctum');
            Route::post('/send-phone-otp', [AuthController::class, 'sendPhoneOtp'])->middleware(['auth:sanctum', 'throttle:phone-otp']);
        });
        /* 
            @User reset password routes
        */
        Route::prefix('password')->group(function () {
            Route::post('/reset', [PasswordController::class, 'reset'])->middleware('throttle:reset-otp');
            Route::post('/verify-otp', [PasswordController::class, 'verify']);
            Route::post('/change', [PasswordController::class, 'change'])->middleware(['auth:sanctum', 'ability:password:change']);
        });
    }
);
