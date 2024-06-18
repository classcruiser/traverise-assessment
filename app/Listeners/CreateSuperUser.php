<?php

namespace App\Listeners;

use App\Events\TenantCreated;
use App\Models\Booking\Document;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Role;
use App\Models\Booking\User;
use App\Models\Booking\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateSuperUser
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
        $super = $event->super;
        $tenant = $event->tenant;

        $user = User::create(['tenant_id' => $tenant->id, 'email' => $super['email'], 'password' => $super['password'], 'name' => 'Super Admin', 'verify_key' => Str::random(64), 'is_super' => 1, 'is_active' => 1, 'tax_setting' => '1,1', 'allowed_camps' => '[1]', 'email_verified_at' => now(), 'expired_at' => now()->addYears(10)]);

        // create super admin role permission here
        $super_role = Role::create(['name' => 'Super Admin', 'tenant_id' => $tenant->id, 'guard_name' => 'tenant']);

        // fill out profiles
        Profile::create([
            'title' => $tenant->id,
            'tenant_id' => $tenant->id,
            'bg_color' => '#F4F4F4',
            'bg_pos_horizontal' => 'center',
            'bg_pos_vertical' => 'center',
            'primary_color' => '#F4F4F4',
            'secondary_color' => '#F4F4F4',
            'accent_color' => '#F4F4F4',
            'test_mode' => 1
        ]);

        // assign super admin role
        $user->assignRole($super_role);

        // create T&C document
        Document::create([
            'tenant_id' => $tenant->id,
            'position' => 'terms-and-conditions',
            'popup' => 1,
            'target' => '_self',
            'name' => 'Terms & Conditions (AGB)',
            'slug' => 'terms-and-conditions-agb',
            'title' => 'Terms & Conditions (AGB)',
            'content' => '<p><b>Terms and Conditions</b></p>',
            'sort' => 1
        ]);
    }
}
