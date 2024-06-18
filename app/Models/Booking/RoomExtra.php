<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class RoomExtra extends Model
{
  protected $guarded = [];

  public function extra()
  {
    return $this->belongsTo(Extra::class);
  }

  public function room()
  {
    return $this->belongsTo(Room::class);
  }
}
