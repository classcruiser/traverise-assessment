<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SpecialOfferRoom extends Model
{
  protected $guarded = [];

  public function offer()
  {
    return $this->belongsTo(SpecialOffer::class);
  }

  public function room()
  {
    return $this->belongsTo(Room::class);
  }
}
