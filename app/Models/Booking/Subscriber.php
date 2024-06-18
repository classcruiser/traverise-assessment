<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $guarded = [];

    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
    	return $this->firstname .' '. $this->lastname;
    }
}
