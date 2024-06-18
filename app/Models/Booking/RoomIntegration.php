<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomIntegration extends Model
{
    use HasFactory;

    protected $table = 'room_integrations';

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function subroom()
    {
        return $this->belongsTo(RoomInfo::class, 'room_info_id');
    }
}
