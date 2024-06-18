<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRoomGuest extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function details()
    {
        return $this->hasOne(BookingGuest::class, 'id', 'booking_guest_id');
    }

    public function room()
    {
        return $this->hasOne(BookingRoom::class, 'id', 'booking_room_id')->withTrashed();
    }
}
