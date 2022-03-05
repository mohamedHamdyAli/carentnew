<?php

use App\Http\Controllers\AgencyApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BusinessDocumentController;
use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\IdentityDocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerApplicationController;
use App\Http\Controllers\OwnerOrderController;
use App\Http\Controllers\OwnerOrderExtendController;
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
use App\Http\Controllers\RenterOrderEarlyReturnController;
use App\Http\Controllers\RenterOrderExtendController;
use App\Http\Controllers\RewardPointController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserCardController;
use App\Http\Controllers\UserController;
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


Route::group(
    [
        'prefix' => 'v1',
        'middleware' => ['api'],
        'namespace' => 'App\Http\Controllers',
    ],
    function ($router) {
        // default
        Route::get('/', function () {
            return response()->json([
                'message' => 'CARENET API v1.0',
                'data' => null,
                'error' => null
            ], 200);
        });

        // test email
        Route::get('/test-email', function () {
            // return Mail::to('mahmoud.ali.kassem@gmail.com')->send(new \App\Mail\EmailOtp(123456));
            if (app()->environment('local')) {
                return new \App\Mail\EmailOtp(123456);
            }
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
            @Banks routes
        */
        Route::get('banks', 'BankController@index')->middleware(['auth:sanctum', 'country']);

        /* 
            @Authentication routes
        */
        Route::prefix('auth')->middleware('country')->group(function () {
            Route::post('/login/email', [AuthController::class, 'loginWithEmailAndPassword']);
            Route::post('/login/phone', [AuthController::class, 'loginWithPhoneAndPassword']);
            Route::post('/login/social', [AuthController::class, 'loginWithSocial']);
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
            Route::get('/', [VehicleController::class, 'index']);
            Route::get('/{id}', [VehicleController::class, 'view']);
            Route::get('/pricing', [OrderController::class, 'getTotals']);
        });

        /**
         *   @Order routes
         */
        Route::prefix('orders')->middleware(['auth:sanctum', 'country'])->group(function () {
            Route::get('/pricing', [OrderController::class, 'getTotals'])->middleware(['privilege:book_car']);
            Route::post('/create', [OrderController::class, 'create'])->middleware(['privilege:book_car']);
            Route::get('statuses', [OrderController::class, 'getStatuses']);

            // will be removed
            Route::get('/my-orders', [OrderController::class, 'myOrders'])->middleware(['privilege:book_car']);
            Route::get('/by-number/{number}', [OrderController::class, 'byNumber'])->middleware(['privilege:book_car']);
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

                // receive car by renter
                Route::patch('/receive/{id}', [RenterOrderController::class, 'receive']);


                // extend order by renter
                Route::prefix('extend')->group(function () {
                    Route::post('/', [RenterOrderExtendController::class, 'extend']);
                });
                // return early order by renter
                Route::prefix('return-early')->group(function () {
                    Route::post('/{id}', [RenterOrderEarlyReturnController::class, 'returnEarly']);
                });
            });
        });

        /**
         *   @Payment routes
         */
        Route::prefix('payments')->middleware(['auth:sanctum', 'verified', 'role:renter'])->group(function () {
            Route::post('/pay', [PaymentController::class, 'pay'])->middleware(['country']);
            Route::post('/extend/pay', [PaymentController::class, 'payExtend'])->middleware(['country']);
        });

        /**
         *   @User routes
         */
        Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::post('/profile', [UserController::class, 'update']);
            Route::post('/fcm', [UserController::class, 'fcm']);
            Route::post('/password', [UserController::class, 'password']);

            Route::prefix('notifications')->group(function () {
                Route::get('/', [NotificationController::class, 'index']);
                Route::get('/unread', [NotificationController::class, 'unread']);
                Route::patch('/read/all', [NotificationController::class, 'readAll']);
                Route::patch('/read/{id}', [NotificationController::class, 'read']);
            });

            Route::prefix('cards')->group(function () {
                Route::get('/', [UserCardController::class, 'index']);
                // Route::post('/', [UserCardController::class, 'add']);
                Route::post('/', function () {
                    return response()->json([
                        'message' => 'This route is not available, please use fawey web tokenization',
                        'data' => null,
                        'error' => null
                    ], 404);
                });
                Route::delete('/{token}', [UserCardController::class, 'delete']);
            });
            Route::prefix('banks')->group(function () {
                Route::get('/', [BankAccountController::class, 'show']);
                Route::post('/', [BankAccountController::class, 'store']);
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
                // reject order
                Route::patch('/reject/{id}', [OwnerOrderController::class, 'reject']);
                // cancel order
                Route::patch('/cancel/{id}', [OwnerOrderController::class, 'cancel']);
                // deliver car by owner
                Route::patch('/deliver/{id}', [OwnerOrderController::class, 'deliver']);
                // complete order
                Route::patch('/complete/{id}', [OwnerOrderController::class, 'complete']);

                // handle order extend request
                Route::prefix('extend')->group(function () {
                    Route::patch('/accept/{id}', [OwnerOrderExtendController::class, 'accept']);
                    Route::patch('/reject/{id}', [OwnerOrderExtendController::class, 'reject']);
                });
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
                return response()->json(AppSetting::orderBy('version', 'desc')->first());
            });
        });
    }
);
