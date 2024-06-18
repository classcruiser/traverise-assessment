<?php

namespace App\Listeners;

use App\Events\TenantCreated;
use App\Mail\TenantVerificationLink;
use App\Models\Booking\User as UserTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendTenantCreatedEmail implements ShouldQueue
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
    public function handle(TenantCreated $event)
    {
        $tenant = $event->tenant;

        $super = UserTenant::where('is_super', 1)->where('tenant_id', $tenant->id)->firstOrFail();

        Mail::to($super->email)->send(new TenantVerificationLink($tenant, $super));
    }
}
