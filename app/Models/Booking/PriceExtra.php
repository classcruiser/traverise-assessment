<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class PriceExtra extends Model
{
  protected $guarded = [];

  public function extra()
  {
    return $this->belongsTo(Extra::class);
  }
}
