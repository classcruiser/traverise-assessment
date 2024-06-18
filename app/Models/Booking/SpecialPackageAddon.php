<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SpecialPackageAddon extends Model
{
  protected $guarded = [];

  public function package()
  {
    return $this->belongsToMany(SpecialPackage::class);
  }

  public function details()
  {
    return $this->belongsTo(Extra::class, 'extra_id', 'id');
  }
}
