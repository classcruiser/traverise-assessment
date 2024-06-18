<?php

namespace App\Listeners;

use App\Events\TenantDeleted;
use App\Models\Booking\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteSuperUser
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
     * @param  object  $event
     * @return void
     */
    public function handle(TenantDeleted $event)
    {
        $tenant = $event->tenant;

        // delete domain
        $tenant->domains()->delete();

        // delete super user
        //Role::where('tenant_id', $tenant->id)->delete();

        // delete custom roles

        // delete 
    }
}
