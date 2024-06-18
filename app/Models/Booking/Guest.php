<?php

namespace App\Models\Booking;

use Illuminate\Support\Str;
use App\Mail\Guest\ResetPassword;
use Database\Factories\GuestFactory;
use Illuminate\Support\Facades\Mail;
use App\Models\Classes\ClassBookingGuest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Classes\ClassMultiPassPayment;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guest extends Authenticatable
{
    use BelongsToTenant;
    use HasFactory;
    
    protected $guarded = [];
    protected $appends = ['full_name', 'client_number'];
    protected $withCount = ['bookings'];
    protected $hidden = ['password', 'remember_token'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($guest) {
            $clientId = Guest::whereNotNull('client_id')->orderByDesc('client_id')->value('client_id');
            if (blank($clientId)) {
                $clientId = 0;
            }

            do {
                $clientId++;
            } while (Guest::where('client_id', $clientId)->exists());

            $guest->client_id = $clientId;
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return GuestFactory::new();
    }

    public function credit()
    {
        return $this->hasMany(GuestCredit::class, 'guest_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function activeBookings()
    {
        return $this->hasMany(BookingGuest::class)->where(function (Builder $q) {
            $q->whereHas('booking', function ($q) {
                $q->where('status', 'CONFIRMED');
            })->select('id');
        });
    }

    public function agent()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'None'
        ]);
    }

    /**
     * Client ID with leading zero (7 number).
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function clientNumber(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attribute) => !blank($attribute['client_id']) ? Str::padLeft($attribute['client_id'], 7, '0') : null,
        );
    }

    /**
     * Send a password reset notification to the guest.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $name = $this->fname . ' ' . $this->lname;

        Mail::to($this->email, $name)->send(new ResetPassword(
            url: route('guest.password.reset', ['token' => $token, 'email' => $this->email]),
            guest: $this,
            subject: 'Password reset request',
        ));
    }

    public function passes()
    {
        return $this->hasMany(ClassMultiPassPayment::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassBookingGuest::class);
    }
}
