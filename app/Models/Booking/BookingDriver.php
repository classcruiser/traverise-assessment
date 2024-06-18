<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BookingDriver extends Model
{
    protected $guarded = [];

    public function guest()
    {
        return $this->belongsTo(BookingGuest::class);
    }

    public function details()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }
}
