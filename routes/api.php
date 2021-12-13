<?php

use App\Http\Controllers\AgencyApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessDocumentController;
use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\IdentityDocumentController;
use App\Http\Controllers\OwnerApplicationController;
use App\Http\Controllers\OwnerVehicleController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Development & Testing
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Controllers
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\RenterApplicationController;
use App\Http\Controllers\SecureFileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\VehicleController;
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

Route::get('/test', function () {
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
            // $country = DB::table('countries')->inRandomOrder()->first();
            // $state = DB::table('states')->where('country_id', $country->id)->inRandomOrder()->first();
            // $country->state = (object) $state;
            // return (object) collect($country);
            // return $country;
            return response()->json([
                ['name_en' => 'Created', 'name_ar' => 'تم الإنشاء', 'terminate' => false, 'notify' => false, 'message_en' => 'Order created', 'message_ar' => 'تم إنشاء طلبك'],
                ['name_en' => 'Paid', 'name_ar' => 'تم الدفع', 'terminate' => false, 'notify' => false, 'message_en' => 'Order payment is complete', 'message_ar' => 'تم دفع الطلب'],
                ['name_en' => 'Pending', 'name_ar' => 'قيد الإنتظار', 'terminate' => false, 'notify' => false, 'message_en' => 'The order is pending confirmation', 'message_ar' => 'طلبك في إنتظار التأكيد'],
                ['name_en' => 'Confirmed', 'name_ar' => 'تم التأكيد', 'terminate' => false, 'notify' => true, 'message_en' => 'The order is confirmed', 'message_ar' => 'تم تأكيد طلبك'],
                ['name_en' => 'Preparing', 'name_ar' => 'قيد التنفيذ', 'terminate' => false, 'notify' => false, 'message_en' => 'The order is beeing prepared', 'message_ar' => 'جاري تنفيذ طلبك'],
                ['name_en' => 'Ready', 'name_ar' => 'جاهز', 'terminate' => false, 'notify' => true, 'message_en' => 'Your order is ready for delivery', 'message_ar' => 'طلبك جاهز للتوصيل'],
                ['name_en' => 'On Delivery', 'name_ar' => 'جاري التوصيل', 'terminate' => false, 'notify' => true, 'message_en' => 'Your order is on the way to you', 'message_ar' => 'طلبك في الطريق إليك'],
                ['name_en' => 'Delivered', 'name_ar' => 'تم التوصيل', 'terminate' => true, 'notify' => true, 'message_en' => 'The order was delivered successfully', 'message_ar' => 'تم توصيل طلبك'],
                ['name_en' => 'Delivery Failed', 'name_ar' => 'فشل التوصيل', 'terminate' => true, 'notify' => true, 'message_en' => 'The order delivery has failed', 'message_ar' => 'تعذر توصيل الطلب إليك'],
                ['name_en' => 'Canceled', 'name_ar' => 'تم الإلغاء', 'terminate' => true, 'notify' => true, 'message_en' => 'The order was canceled', 'message_ar' => 'تم إلغاء طلبك'],
            ]);
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
        });

        /**
         *   @Upload routes
         */
        Route::prefix('upload')->middleware('auth:sanctum')->group(function () {
            Route::post('/image', [UploadController::class, 'image']);
        });
    }
);
