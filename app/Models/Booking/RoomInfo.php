<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomInfo extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\RoomInfoFactory::new();
    }
    
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getAgentNameAttribute()
    {
        $pattern = '/([a-zA-z\s]+)([0-9]{1,2})/';
        preg_match($pattern, $this->name, $matches);

        if (count($matches) > 1 && $matches[2] != '') {
            return str_replace(' '. $matches[2], '', $this->name);
        }

        return $this->name;
    }
}
