<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingTransfer extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'flight_time'];

    protected $appends = ['flight_detail'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function details()
    {
        return $this->belongsTo(TransferExtra::class, 'transfer_extra_id', 'id');
    }

    public function getFlightDetailAttribute()
    {
        if ($this->flight_number == 'TBC' || $this->flight_time == '') {
            return '(TBC)';
        }

        $type = $this->details->direction == 'Inbound' ? 'arrival' : 'departure';

        return '(Flight No. <b>'. $this->flight_number .'</b>, '. $type .' at <b>'. $this->flight_time->format('d.m.Y H:i') .'</b>)';
    }
}
