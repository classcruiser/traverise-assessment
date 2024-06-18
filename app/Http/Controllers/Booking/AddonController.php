<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\CustomTax;
use App\Models\Booking\Extra;
use App\Models\Booking\Location;
use App\Models\Booking\Questionnaire;
use App\Models\Booking\RoomExtra;
use App\Services\Booking\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddonController extends Controller
{
    /**
    * ADDON INDEX
    *
    * @param none
    *
    * @return Illuminate\Http\View
    */
    public function index()
    {
        $addons = Extra::with(['rooms', 'rooms.room', 'rooms.room.location'])->orderBy('sort', 'asc')->where('hidden', 0)->get();

        $addons = $addons->map(function ($addon, $key) {
            $addon['location_details'] = '--';
            $addon['total_locations'] = 0;
            $addon['limit_locations'] = 2;
            $addon['more_locations'] = 0;

            $html = '';
            $more = '';

            if (count($addon->rooms) > 0) {
                $loc = [];

                foreach ($addon->rooms as $room) {
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
                    if ($addon['total_locations'] < $addon['limit_locations']) {
                        $rooms = implode(', ', $data['rooms']);
                        $html .= '<span class="tippy font-size-sm mr-1" data-tippy-content="'. $rooms .'"><b>'. $data['name'] .'</b></span> ';
                    } else {
                        $more .= $data['name'] ." ";
                    }
                    $addon['total_locations'] += 1;
                }

                $addon['location_details'] = $html;
                $addon['more_details'] = $more;
                $addon['more_locations'] = ($addon['total_locations'] > $addon['limit_locations']) ? ($addon['total_locations'] - $addon['limit_locations']) : 0;
            }

            return $addon;
        });

        return view('Booking.addons.index', compact('addons' ));
    }

    /**
    * NEW ADDON
    *
    * @param none
    *
    * @return Illuminate\Http\View
    */
    public function create()
    {
        $locations = Location::with(['rooms' => fn ($query) => $query->where('active', 1)])->get();
        $questionnaires = Questionnaire::where('active', 1)->get();
        $taxes = CustomTax::where('is_active', 1)->orderBy('name', 'asc')->get();

        return view('Booking.addons.new', compact('locations', 'questionnaires', 'taxes'));
    }

    /**
    * INSERT NEW ADDON
    *
    * @param Object $request
    *
    * @return Illuminate\Http\Redirect
    */
    public function insert(Request $request)
    {
        DB::beginTransaction();

        $addon = Extra::create($request->only([
            'name', 'rate_type', 'description', 'base_price', 'min_guests', 'max_guests',
            'min_stay', 'max_stay', 'sort', 'qty', 'unit_name', 'min_units', 'max_units', 'questionnaire_id'
        ]));

        if ($request->has('week_question')) {
            $addon->update(['week_question' => 1]);
        }

        if ($request->has('active')) {
            $addon->update(['active' => 1]);
        }

        if ($request->has('admin_only')) {
            $addon->update(['admin_only' => 1]);
        }

        if ($request->has('rooms') && count($request->rooms) > 0) {
            foreach ($request->rooms as $room_id => $state) {
                RoomExtra::create([
                    'extra_id' => $addon->id,
                    'room_id' => $room_id
                ]);
            }
        }

        DB::commit();

        session()->flash('messages', 'Addon added');

        return redirect(route('tenant.addons.show', ['id' => $addon->id]));
    }

    /**
    * SHOW ADDON
    *
    * @param none
    *
    * @return Illuminate\Http\View
    */
    public function show($id)
    {
        $locations = Location::with(['rooms' => fn ($query) => $query->where('active', 1)])->get();
        $addon = Extra::with(['rooms'])->find($id);
        $path = '/tenancy/assets/images/addons/'. tenant('id') .'_addon_' . $id . '.jpg';
        $picture = @file_exists(public_path($path)) ? $path : null;
        $questionnaires = Questionnaire::where('active', 1)->get();
        $taxes = CustomTax::where('is_active', 1)->orderBy('name', 'asc')->get();

        return view('Booking.addons.show', compact('locations', 'addon', 'picture', 'questionnaires', 'taxes'));
    }

    /**
    * UPDATE EXISTING ADDON
    *
    * @param Object $request
    * @param Integer $id
    *
    * @return Illuminate\Http\Redirect
    */
    public function update($id, Request $request)
    {
        $addon = Extra::find($id);

        DB::beginTransaction();

        $addon->update($request->only([
            'name', 'rate_type', 'description', 'base_price', 'min_guests', 'max_stay', 'max_guests', 'min_stay',
            'sort', 'qty', 'unit_name', 'min_units', 'max_units', 'questionnaire_id'
        ]));

        $old_rooms = json_decode(request('old_rooms'));
        $rooms = request('rooms');

        $addon->update([
            'active' => $request->has('active'),
            'admin_only' => $request->has('admin_only'),
            'add_default' => $request->has('add_default'),
            'week_question' => $request->has('week_question'),
        ]);

        foreach ($old_rooms as $room) {
            if (!isset($rooms[$room->room_id])) {
                RoomExtra::find($room->id)->delete();
            }
        }

        if (request()->has('rooms')) {
            foreach ($rooms as $room_id => $state) {
                RoomExtra::FirstOrCreate([
                    'extra_id' => $addon->id,
                    'room_id' => $room_id
                ]);
            }
        }

        if (request('file')) {
            $response = (new FileService())->upload(
                request('file'),
                '/tenancy/assets/images/addons/',
                tenant('id') .'_addon_'. $id .'.jpg',
                ['w' => 100, 'h' => 100]
            );
        }

        DB::commit();

        session()->flash('messages', 'Addon updated');

        return redirect('addons/'. $id);
    }

    /**
    * DELETE ADDON
    *
    * @param Integer $id
    *
    * @return Illuminate\Http\Redirect
    */
    public function remove($id)
    {
        $addon = Extra::find($id);

        $addon->taxes()->delete();
        $addon->rooms()->delete();
        $addon->delete();

        return redirect('/addons');
    }

    public function sort()
    {
        $sort = request('data');

        foreach ($sort as $pos => $id) {
            Extra::find($id)->update(['sort' => intVal($pos) + 1]);
        }

        return response('OK');
    }

    public function fixAddonDates()
    {
        BookingAddon::with('details', 'booking_room.booking')->whereHas('details', function ($query) {
            $query->where('tenant_id', tenant('id'));
        })->chunk(100, function ($addons) {
            foreach ($addons as $addon) {
                if ($addon->details->rate_type == 'Day') {
                    $addon->update([
                        'check_out' => $addon->check_in->addDays($addon->amount)
                    ]);
                }
                if ($addon->details->rate_type == 'Fixed' && $addon->booking_room->booking) {
                    $addon->update([
                        'check_out' => $addon->booking_room->booking->check_out,
                    ]);
                }
            }
        });

        return response('OK');
    }
}
