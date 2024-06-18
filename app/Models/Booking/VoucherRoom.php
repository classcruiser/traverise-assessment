<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class VoucherRoom extends Model
{
  protected $guarded = [];

  public function voucher()
  {
    return $this->belongsTo('App\Models\Voucher');
  }
}
