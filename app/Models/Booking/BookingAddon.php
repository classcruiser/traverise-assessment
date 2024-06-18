<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingAddon extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['check_in', 'check_out', 'created_at', 'updated_at', 'check_in_at'];

    protected $casts = [
        'questionnaire_answers' => 'array'
    ];

    public function details()
    {
        return $this->belongsTo(Extra::class, 'extra_id', 'id');
    }

    public function booking_room()
    {
        return $this->belongsTo(BookingRoom::class, 'booking_room_id', 'id');
    }
}
