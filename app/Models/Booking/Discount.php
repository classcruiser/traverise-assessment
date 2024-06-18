<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
