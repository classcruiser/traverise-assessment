<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateRoom extends Model
{
    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
