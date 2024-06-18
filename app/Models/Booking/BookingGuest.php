<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BookingGuest extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'check_in_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function details()
    {
        return $this->hasOne(Guest::class, 'id', 'guest_id');
    }

    public function groups()
    {
        return $this->hasMany(BookingGuest::class, 'group_id')->where('group_id', $this->id);
    }

    public function rooms()
    {
        return $this->hasMany(BookingRoomGuest::class)->withTrashed();
    }

    public function passport()
    {
        return $this->hasOne(BookingPassport::class);
    }

    public function driver()
    {
        return $this->hasOne(BookingDriver::class);
    }
}
