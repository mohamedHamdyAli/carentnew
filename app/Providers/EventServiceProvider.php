<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\UserObserver;
use App\Observers\VehicleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Apple\AppleExtendSocialite::class . '@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        Payment::observe(PaymentObserver::class);
        Vehicle::observe(VehicleObserver::class);
    }
}
