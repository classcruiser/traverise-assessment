<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentHistory extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo('App\Models\Booking\Payment');
    }

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking\Booking');
    }
}
