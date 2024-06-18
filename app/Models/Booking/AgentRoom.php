<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class AgentRoom extends Model
{
  protected $guarded = [];

  public function agent()
  {
    return $this->belongsTo('App\Models\Booking\User');
  }

  public function rooms()
  {
    return $this->hasMany('App\Models\Booking\RoomInfo');
  }
}
