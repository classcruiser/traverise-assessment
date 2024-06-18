<?php

namespace App\Models\Booking;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use BelongsToTenant, HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\LocationFactory::new();
    }

    protected $guarded = [];

    protected $appends = ['permission_name'];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'location_id')->orderBy('sort', 'asc');
    }

    public function GetPermissionNameAttribute()
    {
        return 'camp_'. Str::slug($this->short_name);
    }

    public function rule()
    {
        return $this->hasOne(BookingRule::class);
    }

    public function taxes()
    {
        return $this
            ->hasMany(CustomTaxSetting::class, 'model_id', 'id')
            ->where('model_path', 'App\Models\Booking\Location');
    }

    public function scopeAllowedCamps($query)
    {
        return $query->whereIn('location_id', json_decode(auth()->user()->allowed_camps, true));
    }
}
