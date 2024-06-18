<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class OccupancyPricing extends Model
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
