<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Carbon\Carbon;

class EmailTemplate extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    public function addons()
    {
        return $this->hasMany(EmailTemplateExtra::class);
    }

    public function rooms()
    {
        return $this->hasMany(EmailTemplateRoom::class);
    }

    public function documents()
    {
        return $this->hasMany(EmailTemplateDocument::class);
    }

    public function showLocationDetails()
    {
        if ($this->rooms->count() > 0) {
            $loc = [];
            $html = '';

            foreach ($this->rooms as $room) {
                $location = $room->room->location;
                if (!array_key_exists($location->id, $loc)) {
                    $loc[$location->id] = [
                        'id' => $location->id,
                        'name' => $location->abbr,
                        'rooms' => []
                    ];
                }

                array_push($loc[$location->id]['rooms'], $room->room->name);
            }

            foreach ($loc as $location_id => $data) {
                $rooms = implode(', ', $data['rooms']);
                $html .= '<span class="btn bg-grey tippy font-size-sm py-1 px-2" data-tippy-content="'. $rooms .'">'. $data['name'] .'</span> ';
            }

            return $html;
        }

        return '--';
    }

    public function condition()
    {
        return $this->hasOne(EmailTemplateCondition::class);
    }
}
