<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class SpecialPackage extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'check_in', 'check_out'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function addons()
    {
        return $this->hasMany(SpecialPackageAddon::class);
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
