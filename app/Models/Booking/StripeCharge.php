<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class StripeCharge extends Model
{
    protected $guarded = [];

    protected $table = 'stripe_charges';

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }
}
