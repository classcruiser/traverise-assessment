<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BookingHistory extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['contains_amount'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function booking_room()
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getContainsAmountAttribute()
    {
        return $this->action == 'Add manual payment' ||
            $this->action == 'Update payment' ||
            $this->action == 'Update booking room price' ||
            $this->action == 'Update booking overview price' ||
            $this->action == 'Add transfer' ||
            $this->action == 'Edit transfer' ||
            $this->action == 'Add discount' ||
            $this->action == 'Edit discount' ||
            $this->action == 'Add addon';
    }
}
