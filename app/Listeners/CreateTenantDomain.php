<?php

namespace App\Listeners;

use App\Events\TenantCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTenantDomain
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TenantCreated  $event
     * @return void
     */
    public function handle(TenantCreated $event)
    {
        $tenant = $event->tenant;

        // create domain
        $tenant->domains()->create([
            'domain' => $tenant->id .'.'. config('app.uri')
        ]);
    }
}
