<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassBookingAddon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(ClassBooking::class, 'class_booking_id');
    }

    public function addon()
    {
        return $this->belongsTo(ClassAddon::class, 'class_addon_id');
    }
}
