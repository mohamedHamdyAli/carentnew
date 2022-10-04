<?php

// Admin Controllers
use App\Models\Role;
use App\Models\Privilege;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\RenterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\FuelTypeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\VehicleApprovalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Models\AppSetting;

/**
 * @Admin routes
 */
Route::group(
    [
        'namespace' => 'App\Http\Controllers\Admin',
    ],
    function ($router) {
        /**
         * * @Users routes
         */
        Route::prefix('users')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::get('/{id}', [AdminUserController::class, 'show']);
            Route::post('/', [AdminUserController::class, 'store']);
        });

        /**
         * * @Roles routes
         */
        Route::get('roles', function () {
            return Cache::remember('roles-' . app()->getLocale(), 600, function () {
                return response()->json(Role::where('key', '!=', 'superadmin')->get()->makeHidden('key'));
            });
        });

        /**
         * * @Privileges routes
         */
        Route::get('privileges', function () {
            return Cache::remember('privileges-' . app()->getLocale(), 600, function () {
                return response()->json(Privilege::where('role_group', 'admin')->get()->makeHidden(['key', 'role_group']));
            });
        });

        /**
         * * @Approvals routes
         */
        Route::prefix('approvals')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            // * Renter Application
            Route::prefix('renters')->group(function () {
                Route::get('/', [RenterController::class, 'index']);
                Route::get('/{id}', [RenterController::class, 'show']);
                Route::patch('/in-review/{id}', [RenterController::class, 'inReview']);
                Route::put('/approve/{id}', [RenterController::class, 'approve']);
                Route::put('/reject/{id}', [RenterController::class, 'reject']);
            });

            // * Owner Application
            Route::prefix('owners')->group(function () {
                Route::get('/', [OwnerController::class, 'index']);
                Route::get('/{id}', [OwnerController::class, 'show']);
                Route::patch('/in-review/{id}', [OwnerController::class, 'inReview']);
                Route::put('/approve/{id}', [OwnerController::class, 'approve']);
                Route::put('/reject/{id}', [OwnerController::class, 'reject']);
            });

            // * Agency Application
            Route::prefix('agencies')->group(function () {
                Route::get('/', [AgencyController::class, 'index']);
                Route::get('/{id}', [AgencyController::class, 'show']);
                Route::patch('/in-review/{id}', [AgencyController::class, 'inReview']);
                Route::put('/approve/{id}', [AgencyController::class, 'approve']);
                Route::put('/reject/{id}', [AgencyController::class, 'reject']);
            });

            // * Vehicles Application
            Route::prefix('vehicles')->group(function () {
                Route::get('/', [VehicleApprovalController::class, 'index']);
                Route::get('/{id}', [VehicleApprovalController::class, 'show']);
                Route::patch('/in-review/{id}', [VehicleApprovalController::class, 'inReview']);
                Route::put('/approve/{id}', [VehicleApprovalController::class, 'approve']);
                Route::put('/reject/{id}', [VehicleApprovalController::class, 'reject']);
                Route::put('/block/{id}', [VehicleApprovalController::class, 'block']);
                Route::put('/unblock/{id}', [VehicleApprovalController::class, 'unblock']);
            });
        });

        /**
         * * @Accounting routes
         */
        Route::prefix('accounting')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            // Payments
            Route::prefix('payments')->group(function () {
                Route::get('/', [AdminPaymentController::class, 'index']);
                Route::get('/{id}', [AdminPaymentController::class, 'show']);
            });
        });

        /**
         * * @Orders routes
         */
        Route::prefix('orders')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/', [AdminOrderController::class, 'index']);
            Route::get('/{id}', [AdminOrderController::class, 'show']);
        });

        /**
         * * @Tools routes
         */
        Route::prefix('tools')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            // Payments
            Route::prefix('notifications')->group(function () {
                Route::post('/', [NotificationController::class, 'send']);
            });
        });

        /**
         * * @Counters routes
         */
        Route::prefix('alerts')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/counters', [AlertController::class, 'counters']);
        });

        /**
         * * @Statistics routes
         */
        Route::prefix('statistics')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/', [StatisticController::class, 'index']);
        });

        /**
         * * @Reports routes
         */
        Route::prefix('reports')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/payments-aggregator', [ReportController::class, 'paymentsAggregator']);
            Route::get('/payments-wallet', [ReportController::class, 'paymentsWallet']);
            Route::get('/cancellation-renter', [ReportController::class, 'cancellationRenter']);
            Route::get('/cancellation-owner', [ReportController::class, 'cancellationOwner']);
            Route::get('/early-returns', [ReportController::class, 'earlyReturns']);
            Route::get('/late-returns', [ReportController::class, 'lateReturns']);
            Route::get('/accident-fees', [ReportController::class, 'accidentFees']);
        });

        /**
         * * @Setting routes
         */
        Route::prefix('settings')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])
            ->group(function () {
                Route::get('/groups/{group}', [SettingController::class, 'group']);
                Route::get('/single/{id}', [SettingController::class, 'single']);
                Route::put('/single/{id}', [SettingController::class, 'update']);
            });

        /**
         * * @State routes
         **/
        Route::prefix('states')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [StateController::class, 'createState']);
            Route::get('/{id}', [StateController::class, 'getSingleState']);
            Route::put('/{id}', [StateController::class, 'updateState']);
        });

        /**
         * * @Brands routes
         **/
        Route::prefix('brands')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [BrandController::class, 'createBrand']);
            Route::get('/{id}', [BrandController::class, 'getSingleBrand']);
            Route::put('/{id}', [BrandController::class, 'updateBrand']);
        });

        /**
         * * @Models routes
         **/
        Route::prefix('models')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [ModelController::class, 'createModel']);
            Route::get('/{id}', [ModelController::class, 'getSingleModel']);
            Route::put('/{id}', [ModelController::class, 'updateModel']);
        });

        /**
         * * @Categories routes
         **/
        Route::prefix('categories')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [CategoryController::class, 'createCategory']);
            Route::get('/{id}', [CategoryController::class, 'getSingleCategory']);
            Route::put('/{id}', [CategoryController::class, 'updateCategory']);
        });
        
        /**
         * * @Features routes
         **/
        Route::prefix('features')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [FeatureController::class, 'createFeature']);
            Route::get('/{id}', [FeatureController::class, 'getSingleFeature']);
            Route::put('/{id}', [FeatureController::class, 'updateFeature']);
        });

        /**
         * * @Fuel-Types routes
         **/
        Route::prefix('fuel-types')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::post('/', [FuelTypeController::class, 'createFuelType']);
            Route::get('/{id}', [FuelTypeController::class, 'getSingleFuelType']);
            Route::put('/{id}', [FuelTypeController::class, 'updateFuelType']);
        });
        
        /**
         * * @App-Settings routes
         **/
        Route::prefix('app-settings')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::put('/', [AppSettingController::class, 'create']);
            Route::get('/', [AppSettingController::class, 'getLatestVersion']);
        });
    }
);
