<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use BelongsToTenant, HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\RoomFactory::new();
    }

    protected $guarded = [];

    protected $appends = ['total_capacity', 'total_rooms', 'beds'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function prices()
    {
        return $this->hasMany(PricingCalendar::class);
    }

    public function rooms()
    {
        return $this->hasMany(RoomInfo::class);
    }

    public function progressive_prices()
    {
        return $this->hasMany(ProgressivePricing::class);
    }

    public function occupancy_prices()
    {
        return $this->hasMany(OccupancyPricing::class);
    }

    public function addons()
    {
        return $this->hasMany(RoomExtra::class);
    }

    public function transfers()
    {
        return $this->hasMany(RoomTransfer::class);
    }

    public function bed_types()
    {
        return collect(json_decode($this->bed_type, true));
    }

    public function getBedsAttribute()
    {
        return json_decode($this->bed_type, true);
    }

    public function getTotalRoomsAttribute()
    {
        $total = 0;
        return $this->rooms->count();
    }

    public function getTotalCapacityAttribute()
    {
        $total = 0;
        if ($this->rooms) {
            foreach ($this->rooms as $room) {
                $total += $room->beds;
            }
        }

        return $total;
    }

    public function taxes()
    {
        return $this
            ->hasMany(CustomTaxSetting::class, 'model_id', 'id')
            ->where('model_path', Room::class);
    }

    public function getInclusionsFormattedAttribute()
    {
        $inclusions = $this->inclusions;

        if ($inclusions == '' || is_null($inclusions)) {
            return '';
        }

        $inclusions = explode('--', $inclusions);

        $inclusions = array_map(function ($item) {
            $item = str_replace('++', '<br />', $item);
            $item = str_replace('[', '<b>', $item);
            $item = str_replace(']', '</b>', $item);
            return '<li>'. trim($item) .'</li>';
        }, $inclusions);

        return '<ul>'. implode('', $inclusions) .'</ul>';
    }
}
