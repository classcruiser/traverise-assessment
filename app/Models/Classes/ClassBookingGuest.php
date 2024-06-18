<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking\Guest;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassBookingGuest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(ClassBooking::class, 'class_booking_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(Guest::class, 'id', 'guest_id');
    }
}
