<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Carbon\Carbon;

class BookingChecklist extends Model
{
    protected $guarded = [];

    protected $appends = ['in_list_parsed', 'out_list_parsed'];

    protected $dates = ['check_in', 'check_out', 'created_at', 'updated_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function getInListParsedAttribute()
    {
        return json_decode($this->in_list, true);
    }

    public function getOutListParsedAttribute()
    {
        return json_decode($this->out_list, true);
    }
}
