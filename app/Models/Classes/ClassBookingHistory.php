<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassBookingHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function booking() : BelongsTo
    {
        return $this->belongsTo(ClassBooking::class);
    }
}
