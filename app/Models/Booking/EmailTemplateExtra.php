<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateExtra extends Model
{
    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function addon()
    {
        return $this->belongsTo(Extra::class);
    }
}
