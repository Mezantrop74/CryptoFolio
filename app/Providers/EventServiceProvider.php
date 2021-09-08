<?php

namespace App\Providers;

use App\Events\NewsFeedPostCreated;
use App\Events\WalletBalanceChanged;
use App\Listeners\NewsFeedPostNotification;
use App\Listeners\SendWalletBalanceNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        WalletBalanceChanged::class => [
            SendWalletBalanceNotification::class,
        ],
        NewsFeedPostCreated::class => [
            NewsFeedPostNotification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
