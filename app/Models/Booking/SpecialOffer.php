<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

use Carbon\Carbon;

class SpecialOffer extends Model
{
  use BelongsToTenant;
  
  protected $guarded = [];

  protected $dates = ['created_at', 'updated_at'];

  protected $appends = ['stay_value_readable', 'booked_between_readable', 'stay_value_formatted', 'booked_between_formatted'];

  public function rooms()
  {
    return $this->hasMany(SpecialOfferRoom::class);
  }

  public function getStayValueReadableAttribute()
  {
    if ($this->stay_value) {
      $tmp = explode(' - ', $this->stay_value);
      $date_start = new Carbon($tmp[0]);
      $date_end = new Carbon($tmp[1]);

      return $date_start->format('F d, Y') . ' and ' . $date_end->format('F d, Y');
    }

    return;
  }

  public function getBookedBetweenReadableAttribute()
  {
    if ($this->booked_between) {
      $tmp = explode(' - ', $this->booked_between);
      $date_start = new Carbon($tmp[0]);
      $date_end = new Carbon($tmp[1]);

      return $date_start->format('F d, Y') . ' and ' . $date_end->format('F d, Y');
    }

    return;
  }

  public function getStayValueFormattedAttribute()
  {
    if ($this->stay_value) {
      $tmp = explode(' - ', $this->stay_value);
      $date_start = new Carbon($tmp[0]);
      $date_end = new Carbon($tmp[1]);

      return $date_start->format('d.m.Y') . ' - ' . $date_end->format('d.m.Y');
    }

    return;
  }

  public function getBookedBetweenFormattedAttribute()
  {
    if ($this->booked_between) {
      $tmp = explode(' - ', $this->booked_between);
      $date_start = new Carbon($tmp[0]);
      $date_end = new Carbon($tmp[1]);

      return $date_start->format('d.m.Y') . ' - ' . $date_end->format('d.m.Y');
    }

    return;
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
        $html .= '<span class="btn bg-grey tippy font-size-sm py-1 px-2" data-tippy-content="' . $rooms . '">' . $data['name'] . '</span> ';
      }

      return $html;
    }

    return '--';
  }
}
