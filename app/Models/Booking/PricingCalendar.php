<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class PricingCalendar extends Model
{
  protected $guarded = [];

  protected $dates = ['created_at', 'updated_at', 'date_from', 'date_to'];

  public function room()
  {
    return $this->belongsTo('App\Models\Room');
  }
}
