<?php

namespace App\Listeners;

use App\Events\TenantCreated;
use App\Models\Booking\EmailTemplate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateDefaultAutomatedEmails
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

        EmailTemplate::updateOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Booking Confirmation',
            'slug' => 'booking-confirmation-email',
        ], [
            'subject' => 'Booking Confirmation',
            'is_scheduled' => 0,
            'send_time' => null,
            'time_unit' => 'days',
            'send_timing' => 'AFTER',
            'send_date_column' => 'check_in',
            'resource' => 'automated_1.blade.php',
            'template' => 'automated_1',
            'attachment' => null,
        ]);

        EmailTemplate::updateOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Pending Booking',
            'slug' => 'booking-pending-email',
        ], [
            'subject' => 'Pending Booking',
            'is_scheduled' => 0,
            'send_time' => null,
            'time_unit' => 'days',
            'send_timing' => 'AFTER',
            'send_date_column' => 'check_in',
            'resource' => 'automated_2.blade.php',
            'template' => 'automated_2',
            'attachment' => null,
        ]);

        // create email template
        if (Storage::disk('resource')->put('templates/'. $tenant->id .'/automated_1.blade.php', '<p>Dear <strong>{guest_name}.</strong></p><p>This is your confirmation email</p>')) {
            Log::error('Cannot create automated template 1 for '. $tenant->id);
        }
        
        if (Storage::disk('resource')->put('templates/'. $tenant->id .'/automated_2.blade.php', '<p>Dear <strong>{guest_name}.</strong></p><p>This is your confirmation email</p>')) {
            Log::error('Cannot create automated template 2 for '. $tenant->id);
        }
    }
}
