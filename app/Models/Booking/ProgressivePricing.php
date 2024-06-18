<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class ProgressivePricing extends Model
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }
}
