<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class TransferExtraPrice extends Model
{
  protected $guarded = [];

  public function extra()
  {
    return $this->belongsTo(TransferExtra::class);
  }
}
