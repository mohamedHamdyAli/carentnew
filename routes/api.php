<?php

use App\Http\Controllers\AgencyApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BusinessDocumentController;
use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\IdentityDocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerApplicationController;
use App\Http\Controllers\OwnerOrderController;
use App\Http\Controllers\OwnerVehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Development & Testing
use App\Models\User;

// Controllers
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RenterApplicationController;
use App\Http\Controllers\RenterOrderController;
use App\Http\Controllers\RewardPointController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserCardController;
use App\Http\Controllers\VehicleController;
use App\Models\AppSetting;
use App\Models\Order;
use App\Models\Vehicle;

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

Route::post('/test', function () {
    // 
    // try {
    //     $orders = Order::all();
    //     foreach ($orders as $order) {
    //         OrderStatusHistory::create([
    //             'order_id' => $order->id,
    //             'order_status_id' => $order->order_status_id,
    //             'created_at' => $order->created_at,
    //             'updated_at' => $order->updated_at
    //         ]);
    //     }
    //     return response()->json([
    //         'message' => __('messages.r_success'),
    //         'data' => null,
    //         'error' => null
    //     ], 200);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'message' => __('messages.r_error'),
    //         'data' => null,
    //         'error' => $e->getMessage()
    //     ], 500);
    // }
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
        });

        // Payment Notification
        Route::post('/payment-notifications', function (Request $request) {
            return response()->json([
                'message' => 'Payment notification received',
                'data' => $request->all(),
                'error' => null
            ], 200);
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
        Route::prefix('auth')->middleware('country')->group(function () {
            Route::post('/login/email', [AuthController::class, 'loginWithEmailAndPassword']);
            Route::post('/login/phone', [AuthController::class, 'loginWithPhoneAndPassword']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
            Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
            Route::post('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
            Route::post('/send-email-otp', [AuthController::class, 'sendEmailOtp'])->middleware(['auth:sanctum', 'throttle:email-otp']);
            Route::post('/verify/{type}', [AuthController::class, 'verify'])->middleware('auth:sanctum');
            Route::post('/send-phone-otp', [AuthController::class, 'sendPhoneOtp'])->middleware(['auth:sanctum', 'throttle:phone-otp']);
        });

        /* 
            @Password routes
        */
        Route::prefix('password')->middleware('country')->group(function () {
            Route::post('/reset', [PasswordController::class, 'reset'])->middleware('throttle:reset-otp');
            Route::post('/verify-otp', [PasswordController::class, 'verify']);
            Route::post('/change', [PasswordController::class, 'change'])->middleware(['auth:sanctum', 'ability:password:change']);
        });

        /**
         *   @Vehicle routes
         */
        Route::prefix('vehicles')->middleware('country')->group(function () {
            Route::post('/', [VehicleController::class, 'index']);
            Route::get('/{id}', [VehicleController::class, 'view']);
            Route::get('/pricing', [OrderController::class, 'getTotals']);
        });

        /**
         *   @Order routes
         */
        Route::prefix('orders')->middleware(['auth:sanctum', 'country', 'privilege:book_car'])->group(function () {
            Route::get('/pricing', [OrderController::class, 'getTotals']);
            Route::post('/create', [OrderController::class, 'create']);

            // will be removed
            Route::get('/my-orders', [OrderController::class, 'myOrders']);
            Route::get('/by-number/{number}', [OrderController::class, 'byNumber']);
        });


        /**
         *   @Renter routes
         */
        Route::prefix('renter')->middleware(['auth:sanctum', 'verified', 'role:renter'])->group(function () {
            // Renter Orders routes
            Route::prefix('orders')->group(function () {
                Route::get('/', [RenterOrderController::class, 'index']);
                Route::get('/{id}', [RenterOrderController::class, 'view']);

                // cancel order by renter
                Route::patch('/cancel/{id}', [RenterOrderController::class, 'cancel']);
            });
        });

        /**
         *   @Payment routes
         */
        Route::prefix('payments')->middleware(['auth:sanctum', 'verified', 'role:renter'])->group(function () {
            Route::post('/pay', [PaymentController::class, 'pay'])->middleware(['country']);
        });

        /**
         *   @User routes
         */
        Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {
            Route::prefix('notifications')->group(function () {
                Route::get('/', [NotificationController::class, 'index']);
                Route::get('/unread', [NotificationController::class, 'unread']);
                Route::patch('/read/all', [NotificationController::class, 'readAll']);
                Route::patch('/read/{id}', [NotificationController::class, 'read']);
            });
            Route::prefix('cards')->group(function () {
                Route::get('/', [UserCardController::class, 'index']);
                Route::post('/', [UserCardController::class, 'add']);
                Route::delete('/{token}', [UserCardController::class, 'delete']);
            });
            Route::prefix('balance')->group(function () {
                Route::get('/transactions', [BalanceController::class, 'transactions']);
            });
            Route::prefix('reward-points')->group(function () {
                Route::get('/transactions', [RewardPointController::class, 'transactions']);
            });
        });

        /**
         *   @Identity Document routes
         */
        Route::prefix('identity-documents')->middleware('auth:sanctum')->group(function () {
            Route::post('/', [IdentityDocumentController::class, 'store']);
            Route::get('/', [IdentityDocumentController::class, 'show']);
            Route::delete('/delete', [IdentityDocumentController::class, 'devDelete']);
        });

        /**
         *   @Business Document routes
         */
        Route::prefix('business-documents')->middleware('auth:sanctum')->group(function () {
            Route::post('/', [BusinessDocumentController::class, 'store']);
            Route::get('/', [BusinessDocumentController::class, 'show']);
            Route::delete('/delete', [BusinessDocumentController::class, 'devDelete']);
        });

        /**
         *   @Driver License routes
         */
        Route::prefix('driver-licenses')->middleware('auth:sanctum')->group(function () {
            Route::post('/', [DriverLicenseController::class, 'store']);
            Route::get('/', [DriverLicenseController::class, 'show']);
            Route::delete('/delete', [DriverLicenseController::class, 'devDelete']);
        });

        /**
         *   @Secure File routes
         */
        Route::post('/secure', [SecureFileController::class, 'file']);

        /**
         *   @Settings routes
         */
        Route::prefix('settings')->group(function () {
            Route::get('/{key}', [SettingController::class, 'settings']);
        });

        /**
         *   @Owner Application routes
         */
        Route::prefix('owner-application')->middleware(['auth:sanctum', 'verified'])->group(function () {
            Route::get('/status', [OwnerApplicationController::class, 'status']);
            Route::post('/sign-agreement', [OwnerApplicationController::class, 'signAgreement']);
            Route::post('/submit', [OwnerApplicationController::class, 'submit']);

            // Development Routes
            Route::put('/status', [OwnerApplicationController::class, 'dev']);
            Route::delete('/delete', [OwnerApplicationController::class, 'devDelete']);
        });

        /**
         *   @Agency Application routes
         */
        Route::prefix('agency-application')->middleware(['auth:sanctum', 'verified'])->group(function () {
            Route::get('/status', [AgencyApplicationController::class, 'status']);
            Route::post('/sign-agreement', [AgencyApplicationController::class, 'signAgreement']);
            Route::post('/submit', [AgencyApplicationController::class, 'submit']);

            // Development Routes
            Route::put('/status', [AgencyApplicationController::class, 'dev']);
            Route::delete('/delete', [AgencyApplicationController::class, 'devDelete']);
        });

        /**
         *   @Renter Application routes
         */
        Route::prefix('renter-application')->middleware(['auth:sanctum', 'verified'])->group(function () {
            Route::get('/status', [RenterApplicationController::class, 'status']);
            Route::post('/sign-agreement', [RenterApplicationController::class, 'signAgreement']);
            Route::post('/submit', [RenterApplicationController::class, 'submit']);

            // Development Routes
            Route::put('/status', [RenterApplicationController::class, 'dev']);
            Route::delete('/delete', [RenterApplicationController::class, 'devDelete']);
        });

        /**
         *   @Owner routes
         */
        Route::prefix('owner')->middleware(['auth:sanctum', 'verified', 'anyrole:owner|agency'])->group(function () {
            // Owner Vehicles Routes
            Route::prefix('vehicles')->group(function () {
                Route::get('/', [OwnerVehicleController::class, 'index']);
                Route::post('/', [OwnerVehicleController::class, 'store'])->middleware('country');
                Route::get('/{id}', [OwnerVehicleController::class, 'vehicle']);
                // submit for verification
                Route::post('/verification/{id}', [OwnerVehicleController::class, 'verification']);
                Route::delete('/images/{id}', [OwnerVehicleController::class, 'deleteImage']);
                // development routes
                Route::put('/status/{id}', [OwnerVehicleController::class, 'dev']);
                // activation routes
                Route::post('/activate/{id}', [OwnerVehicleController::class, 'activate']);
            });

            // Owner Orders routes
            Route::prefix('orders')->group(function () {
                Route::get('/', [OwnerOrderController::class, 'index']);
                Route::get('/{id}', [OwnerOrderController::class, 'view']);

                // accept order
                Route::patch('/accept/{id}', [OwnerOrderController::class, 'accept']);
                // cancel order
                Route::patch('/cancel/{id}', [OwnerOrderController::class, 'cancel']);
            });
        });

        /**
         *   @Upload routes
         */
        Route::prefix('upload')->middleware('auth:sanctum')->group(function () {
            Route::post('/image', [UploadController::class, 'image']);
        });

        /**
         *   @App Setting routes
         */
        Route::prefix('app')->group(function () {
            Route::get('settings', function () {
                return response()->json(AppSetting::findOrFail(1));
            });
        });
    }
);
