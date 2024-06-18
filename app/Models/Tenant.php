<?php

namespace App\Models;

use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Services\Booking\MailService;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;

    protected $guarded = [];

    protected $appends = ['full_name'];

    protected static function newFactory()
    {
        return TenantFactory::new();
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'plan',
            'first_name',
            'last_name',
            'company',
            'address',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'zip',
            'is_active',
            'subscription_start',
            'subscription_end',
            'stripe_account_id',
            'stripe_onboarding_process',
        ];
    }

    public function getFullNameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected static function booted()
    {
        static::deleting(function ($tenant) {
            $tenant->domains()->update(['deleted_at' => now()]);
            $tenant->bookings()->delete();
            // what else ?
        });
        
        static::created(function ($tenant) {
            // create default email templates
            EmailTemplate::create([
                'tenant_id' => $tenant->id,
                'name' => 'Booking Confirmation',
                'slug' => 'booking-confirmation-email',
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

            EmailTemplate::create([
                'tenant_id' => $tenant->id,
                'name' => 'Pending Booking',
                'slug' => 'booking-pending-email',
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
        });
    }
}
