<?php

// Admin Controllers
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\RenterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VehicleApprovalController;
use App\Models\Privilege;
use App\Models\Role;

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
         * @Accounting routes
         */
        Route::prefix('accounting')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            // Payments
            Route::prefix('payments')->group(function () {
                Route::get('/', [AdminPaymentController::class, 'index']);
                Route::get('/{id}', [AdminPaymentController::class, 'show']);
            });
        });

        /**
         * * @Approvals routes
         */
        Route::prefix('orders')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/', [AdminOrderController::class, 'index']);
            Route::get('/{id}', [AdminOrderController::class, 'show']);
        });

        /**
         * * @Counters routes
         */
        Route::prefix('alerts')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/counters', [AlertController::class, 'counters']);
        });

        /**
         * * @Reports routes
         */
        Route::prefix('reports')->middleware(['auth:sanctum', 'anyrole:admin|superadmin'])->group(function () {
            Route::get('/', [ReportController::class, 'index']);
        });
    }
);
