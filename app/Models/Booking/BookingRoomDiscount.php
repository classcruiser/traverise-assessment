<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRoomDiscount extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function booking_room()
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function offer()
    {
        return $this->belongsTo(SpecialOffer::class, 'special_offer_id', 'id');
    }
}
