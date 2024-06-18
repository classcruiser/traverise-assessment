<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCancellation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function calculateRefundedAmount(float $amount): float
    {
        return number_format(floatVal($amount * $this->cancellation_fee / 100), 2);
    }

    public function calculateTaxAmount(float $amount): float
    {
        $add = floatVal(100 + 19);

        return (($amount / $add) * 19);
    }
}
