<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BookingRoom extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['nights', 'days', 'created_at_simple',];

    protected $dates = ['from', 'to', 'created_at', 'updated_at'];

    protected $casts = [
        'from' => 'date',
        'to' => 'date'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function subroom()
    {
        return $this->belongsTo(RoomInfo::class, 'subroom_id');
    }

    public function discounts()
    {
        return $this->hasMany(BookingRoomDiscount::class);
    }

    public function guests()
    {
        return $this->hasMany(BookingRoomGuest::class);
    }

    public function checklist()
    {
        return $this->hasOne(BookingChecklist::class);
    }

    public function guestDetails()
    {
        return $this->hasOne(BookingRoomGuest::class, 'booking_room_id', 'id');
    }

    public function mainGuest()
    {
        return $this->hasOne(BookingRoomGuest::class);
    }

    public function addons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function getCreatedAtSimpleAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    /*
    public function assignedGuests()
    {
      $guests = $this->guests;
      $total = count($guests);
      $names = [];
      if ($total > 0) {
        foreach ($guests as $guest) {
          array_push($names, $guest->details->details->full_name);
        }
      }

      return implode(", ", $names);
    }
    */

    public function getNightsAttribute()
    {
        return $this->length();
    }

    public function getDaysAttribute()
    {
        return intVal($this->length()) + 1;
    }

    public function length()
    {
        $start = new Carbon($this->from);
        $finish = new Carbon($this->to);

        return $start->diffInDays($finish);
    }
}
