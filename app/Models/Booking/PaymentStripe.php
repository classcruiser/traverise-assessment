<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class PaymentStripe extends Model
{
    protected $table = 'payment_stripe';

    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_link', 'link');
    }
}
