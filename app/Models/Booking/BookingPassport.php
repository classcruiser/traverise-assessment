<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class BookingPassport extends Model
{
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest()
    {
        return $this->hasMany(BookingGuest::class);
    }
}
