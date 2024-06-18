<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
  protected $guarded = [];

  public function camp()
  {
    return $this->belongsTo(Location::class);
  }
}
