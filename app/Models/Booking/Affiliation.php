<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Affiliation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'is_active',
        'received_email',
        'code',
        'hash'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'affiliation_id', 'id');
    }

    public function confirmedBookings()
    {
        return $this->hasMany(Booking::class, 'affiliation_id', 'id')->where('status', 'CONFIRMED');   
    }
}
