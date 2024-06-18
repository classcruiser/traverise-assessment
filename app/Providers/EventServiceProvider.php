<?php

namespace App\Providers;

use App\Events\TenantCreated;
use App\Events\TenantDeleted;
use App\Listeners\CreateDefaultAutomatedEmails;
use App\Listeners\CreateSuperUser;
use App\Listeners\CreateTenantDomain;
use App\Listeners\DeleteSuperUser;
use App\Listeners\SendTenantCreatedEmail;
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
        TenantCreated::class => [
            CreateTenantDomain::class,
            CreateSuperUser::class,
            SendTenantCreatedEmail::class,
            CreateDefaultAutomatedEmails::class,
        ],
        TenantDeleted::class => [
            DeleteSuperUser::class,
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
