<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationPaymentMethodDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function method()
    {
        return $this->belongsTo(LocationPaymentMethod::class);
    }
}
