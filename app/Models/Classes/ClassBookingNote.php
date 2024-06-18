<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassBookingNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(ClassesBooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
