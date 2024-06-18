<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Booking\Booking;
use App\Models\Booking\Location;
use App\Models\Booking\SpecialOffer;
use App\Models\Booking\SpecialOfferRoom;

class OfferController extends Controller
{
  public function __construct()
  {
    // ...
  }

  /**
   * SPECIAL OFFER INDEX
   * 
   * @param none
   * 
   * @return Illuminate\Http\View
   */
  public function index()
  {
    $offers = SpecialOffer::with(['rooms.room.location'])->latest()->get();

    $offers = $offers->map(function ($offer, $key) {
      $offer['location_details'] = '--';
      $html = '';
      if (count($offer->rooms) > 0) {
        $loc = [];
  
        foreach ($offer->rooms as $room) {
          $location = $room->room->location;
          if (!isset($loc[$location->id])) {
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
  
        $offer['location_details'] = $html;
      }
      
      return $offer;
    });

    return view('Booking.offers.index', compact('offers'));
  }

  /**
   * NEW OFFER
   * 
   * @param none
   * 
   * @return Illuminate\Http\View
   */
  public function create()
  {
    $locations = Location::with(['rooms'])->get();

    return view('Booking.offers.new', compact('locations'));
  }

  /**
   * INSERT NEW OFFER
   * 
   * @param Object $request
   * 
   * @return Illuminate\Http\Redirect
   */
  public function insert(Request $request)
  {
    $offer = SpecialOffer::create($request->only([
      'name', 'discount_type', 'discount_value', 'stay_type', 'min_guest', 'max_guest', 'min_stay', 'max_stay'
    ]));

    if ($request->has('stay_value') && $request->stay_value != '') {
      $tmp = explode(' - ', $request->stay_value);
      $start_date = Carbon::createFromFormat('d.m.Y', $tmp[0])->format('Y-m-d');
      $end_date = Carbon::createFromFormat('d.m.Y', $tmp[1])->format('Y-m-d');

      $offer->update(['stay_value' => $start_date .' - '. $end_date]);
    }

    if ($request->has('booked_between') && $request->booked_between != '') {
      $tmp = explode(' - ', $request->booked_between);
      $start_date = Carbon::createFromFormat('d.m.Y', $tmp[0])->format('Y-m-d');
      $end_date = Carbon::createFromFormat('d.m.Y', $tmp[1])->format('Y-m-d');

      $offer->update(['booked_between' => $start_date .' - '. $end_date]);
    }

    if ($request->has('rooms') && count($request->rooms) > 0) {
      foreach ($request->rooms as $room_id => $state) {
        SpecialOfferRoom::create([
          'special_offer_id' => $offer->id,
          'room_id' => $room_id
        ]);
      }
    }

    return redirect('/special-offers');
  }

  /**
   * SHOW OFFER
   * 
   * @param none
   * 
   * @return Illuminate\Http\View
   */
  public function show($id)
  {
    $locations = Location::with(['rooms'])->get();
    $offer = SpecialOffer::with(['rooms'])->find($id);

    return view('Booking.offers.show', compact('locations', 'offer'));
  }

  /**
   * UPDATE EXISTING OFFER
   * 
   * @param Object $request
   * @param Integer $id
   * 
   * @return Illuminate\Http\Redirect
   */
  public function update($id, Request $request)
  {
    $offer = SpecialOffer::find($id);
    
    $offer->update($request->only([
      'name', 'discount_type', 'discount_value', 'stay_type', 'min_guest', 'max_guest', 'min_stay', 'max_stay'
    ]));

    if ($request->has('stay_value') && $request->stay_value != '') {
      $tmp = explode(' - ', $request->stay_value);
      $start_date = Carbon::createFromFormat('d.m.Y', $tmp[0])->format('Y-m-d');
      $end_date = Carbon::createFromFormat('d.m.Y', $tmp[1])->format('Y-m-d');

      $offer->update(['stay_value' => $start_date .' - '. $end_date]);
    } else {
      $offer->update(['stay_value' => null]);
    }

    if ($request->has('booked_between') && $request->booked_between != '') {
      $tmp = explode(' - ', $request->booked_between);
      $start_date = Carbon::createFromFormat('d.m.Y', $tmp[0])->format('Y-m-d');
      $end_date = Carbon::createFromFormat('d.m.Y', $tmp[1])->format('Y-m-d');

      $offer->update(['booked_between' => $start_date .' - '. $end_date]);
    } else {
      $offer->update(['booked_between' => null]);
    }

    $offer->rooms()->delete();

    if ($request->has('rooms') && count($request->rooms) > 0) {
      foreach ($request->rooms as $room_id => $state) {
        SpecialOfferRoom::create([
          'special_offer_id' => $offer->id,
          'room_id' => $room_id
        ]);
      }
    }

    session()->flash('messages', 'Special offer updated');
    
    return redirect('/special-offers/'. $id);
  }

  /**
   * DELETE OFFER
   * 
   * @param Integer $id
   * 
   * @return Illuminate\Http\Redirect
   */
  public function remove($id)
  {
    $offer = SpecialOffer::with(['rooms'])->find($id);

    $offer->rooms()->delete();

    $offer->delete();

    return redirect('/special-offers');
  }
}
