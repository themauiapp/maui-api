<?php

namespace App\Providers;

use App\Events\VerifyResetEmail;
use App\Events\ChangeEmail;
use App\Events\EmailChanged;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendVerifyResetEmailNotification;
use App\Listeners\SendChangeEmailNotification;
use App\Listeners\SendEmailChangedNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Event;

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
        VerifyResetEmail::class => [
            SendVerifyResetEmailNotification::class,
        ],
        ChangeEmail::class => [
            SendChangeEmailNotification::class,
        ],
        EmailChanged::class => [
            SendEmailChangedNotification::class,
        ],
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
