<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('api/v1/admin')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/adminApi.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(50000)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('email-otp', function (Request $request) {
            return Limit::perMinute(3)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('phone-otp', function (Request $request) {
            return Limit::perMinute(3)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('reset-otp', function (Request $request) {
            return Limit::perMinute(3)->by(optional(User::where('email', $request->email)->first())->id ?: $request->ip());
        });
    }
}
