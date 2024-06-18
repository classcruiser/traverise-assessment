<?php

namespace App\Services\Booking;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingGuest;
use App\Models\Booking\BookingRoom;
use App\Models\Booking\BookingRoomDiscount;
use App\Models\Booking\BookingRoomGuest;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\Guest;
use App\Models\Booking\Location;
use App\Models\Booking\RoomInfo;
use App\Services\Booking\UserService;
use Cache;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;

class SmoobuService
{
  protected $api_key = 'xltbUl0LgikHz2-7AkrwA07RAjKgv7rw';

  protected $setting_channel_id = '157862';

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function getBookings($data)
  {
    $result = $this->getMultipleBookings($data);

    $result = $this->decodeJson($result);

    $result = $this->filterBlocks($result);

    $result = $this->removeEmptyBookings($result);

    return $result;
  }

  public function getMultipleBookings($data)
  {
    $curly = [];
    $result = [];
    $mh = curl_multi_init();
    $base_url = "https://login.smoobu.com/api/apartment/";

    foreach ($data as $room_id => $room) {
      $curly[$room_id] = curl_init();

      $url = "{$base_url}{$room['id']}/booking";

      curl_setopt($curly[$room_id], CURLOPT_URL, $url);
      curl_setopt($curly[$room_id], CURLOPT_HEADER, 0);
      curl_setopt($curly[$room_id], CURLOPT_HTTPHEADER, [
        "Api-key: {$this->api_key}",
        "Cache-Control: no-cache"
      ]);
      curl_setopt($curly[$room_id], CURLOPT_RETURNTRANSFER, 1);

      curl_multi_add_handle($mh, $curly[$room_id]);
    }

    $running = null;
    do {
      curl_multi_exec($mh, $running);
    } while ($running > 0);

    foreach ($curly as $room_id => $c) {
      $result[$room_id] = curl_multi_getcontent($c);
      curl_multi_remove_handle($mh, $c);
    }

    curl_multi_close($mh);

    return $result;
  }

  public function decodeJson($data)
  {
    return array_map(function ($json) {
      return json_decode($json, true);
    }, $data);
  }

  public function filterBlocks($data)
  {
    $result = [];
    foreach ($data as $room_id => $room) {

      if (!isset($room['bookings'])) {
        return $data;
      }
      
      $bookings = $room['bookings'];
      $result[$room_id] = $room;
      
      if ($bookings) {
        // filter here
        $result[$room_id]['bookings'] = array_filter($bookings, function ($booking) {
          $channel_name = $booking['channel']['name'];
          
          return $channel_name != 'Blocked channel';
        });
      }
    }

    return $result;
  }

  public function removeEmptyBookings($data)
  {
    return array_filter($data, function ($booking) {
      return isset($booking['bookings']) && count($booking['bookings']) > 0;
    });
  }

  public function cancelBooking($booking_id, $listing_id)
  {
    $url = "https://login.smoobu.com/api/apartment/{$listing_id}/booking/{$booking_id}";

    $response = Curl::to($url)
      ->withHeader("Api-key: {$this->api_key}")
      ->withHeader("Cache-Control: no-cache")
      ->asJson()
      ->delete();
    
    return $response;
  }

  public function registerSmoobuBooking($data, $listing_id)
  {
    $url = "https://login.smoobu.com/api/apartment/{$listing_id}/booking";

    $response = Curl::to($url)
      ->withHeader("Api-key: {$this->api_key}")
      ->withHeader("Cache-Control: no-cache")
      ->withData($data)
      ->post();

    return $response;
  }

  public function _get($url)
  {
    return Curl::to($url)
      ->withHeader("Api-key: {$this->api_key}")
      ->withHeader("Cache-Control: no-cache")
      ->get();
  }
}
