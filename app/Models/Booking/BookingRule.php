<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class BookingRule extends Model
{
    protected $guarded = [];

    protected $append = ['disable_check_ins', 'disable_check_outs'];

    public function getDisableCheckInsAttribute()
    {
        if (!$this->disable_check_in_days) {
            return [];
        }

        return json_decode($this->disable_check_in_days, true);
    }

    public function getDisableCheckOutsAttribute()
    {
        if (!$this->disable_check_out_days) {
            return [];
        }

        return json_decode($this->disable_check_out_days, true);
    }
}
