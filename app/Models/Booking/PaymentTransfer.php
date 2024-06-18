<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransfer extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'verified_at', 'paid_at'];

    protected $casts = [
        'data' => 'array'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function stripe()
    {
        return $this->hasOne(PaymentStripe::class);
    }

    public function paypal()
    {
        return $this->hasOne(PaypalTransaction::class, 'order_id', 'unique_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Booking\User', 'verify_by', 'id')->withDefault([
            'id' => null,
            'name' => '---'
        ]);
    }
}
