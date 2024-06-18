<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestCredit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guest() : BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function pass() : BelongsTo
    {
        return $this->belongsTo(ClassMultiPass::class);
    }

    public function lastBooking() : BelongsTo
    {
        return $this->belongsTo(ClassBooking::class, 'last_class_booking_id');
    }
}
