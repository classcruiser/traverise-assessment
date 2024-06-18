<?php

namespace App\Http\Controllers\Booking;

use Carbon\Carbon;

use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use App\Models\Booking\Room;
use Illuminate\Http\Request;
use App\Models\Booking\Extra;
use App\Models\Booking\Location;
use App\Models\Booking\RoomInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Services\Booking\FileService;

use App\Services\Booking\RoomService;
use App\Services\Booking\UserService;
use App\Models\Booking\PricingCalendar;
use App\Models\Booking\OccupancyPricing;
use App\Services\Booking\BookingService;
use App\Models\Booking\ProgressivePricing;

class RoomController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $roomService;

    public function __construct(BookingService $bookingService, UserService $userService, RoomService $roomService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->roomService = $roomService;
    }

    /**
     * ROOM INDEX.
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function index()
    {
        $camps = Location::query()->with(['rooms.rooms']);

        $all_camps = $camps->get();

        if (request()->has('camp') && request('camp') != 'All camps') {
            $camps = $camps->where('id', request('camp'));
        }

        $camps = $camps->get();
        
        return view('Booking.rooms.index', compact('camps', 'all_camps'));
    }

    public function threshold()
    {
        $rooms = Room::with(['location', 'rooms'])->orderBy('location_id', 'asc')->get();

        return view('Booking.rooms.threshold')->with(['rooms' => $rooms]);
    }

    public function updateThreshold()
    {
        $threshold = request('threshold');
        $old = request('old_threshold');
        $name = request('room_name');

        $description = [];

        foreach ($threshold as $room_id => $value) {
            if ($old[$room_id] != $value) {
                // update
                Room::find($room_id)->update(['limited_threshold' => $value]);
                $room_name = $name[$room_id];

                if ('' == $old[$room_id]) {
                    $message = 'Set ' . $room_name . ' threshold to ' . $value . '%';
                } else {
                    $message = 'Updating ' . $room_name . ' threshold from ' . $old[$room_id] . '% to ' . $value . '%';
                }

                array_push($description, $message);
            }
        }

        if (count($description) > 0) {
            // log
            DB::table('user_histories')->insert([
                'user_id' => auth()->user()->id,
                'username' => auth()->user()->username,
                'action' => 'update-threshold',
                'description' => implode("\n", $description),
                'ip_address' => request()->ip(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * ROOM EDIT.
     *
     * @param int $id
     *
     * @return Illuminate\Http\View
     */
    public function show($id)
    {
        $room = Room::with(['location', 'progressive_prices', 'occupancy_prices'])->find($id);

        $locations = Location::orderBy('name', 'asc')->get();

        // generate calendar
        $year = request()->has('year') ? request('year') : date('Y');
        $cal = [];
        $cal_prices = [];
        $last_season = '';
        $last_key = '';

        $prices = PricingCalendar::where('date', 'LIKE', '%' . $year . '%')
            ->where('room_id', $id)
            ->orderBy('date', 'asc')
            ->get();

        $season_legends = $prices->groupBy('season_type')->all();

        if ($prices->count() > 0) {
            foreach ($prices as $index => $price) {
                $d = $price->date;
                $css_class = $price->season_type;

                if ('' == $last_season) {
                    $css_class .= '-START';
                }
                if ('' != $last_season && $price->season_type != $last_season) {
                    $cal_prices[$last_key]['end'] = true;
                    $cal_prices[$last_key]['css_class'] = $cal_prices[$last_key]['css_class'] . '-END';
                    $css_class .= '-START';
                    $last_season = '';
                }

                if ($index == $prices->count() - 1) {
                    $css_class .= '-END';
                }

                if (!isset($cal_prices[$d])) {
                    $cal_prices[$d] = [
                        'start' => 0 == $index,
                        'season' => $price->season_type,
                        'price' => $price->price,
                        'end' => $index == ($prices->count() - 1),
                        'css_class' => $css_class,
                    ];
                } else {
                    $cal_prices[$d] = [
                        'start' => $cal_prices[$d]['start'],
                        'season' => $price->season_type,
                        'price' => $price->price,
                        'end' => $cal_prices[$d]['end'],
                        'css_class' => $cal_prices[$d]['css_class'] . ' ' . $price->season_type,
                    ];
                }

                $last_season = $price->season_type;
                $last_key = $d;
            }
        }

        $period = null;
        $d = null;

        for ($i = 1; $i <= 12; ++$i) {
            $first_date = Carbon::create($year, $i, 1, '00', '00', '00')->startOfMonth();
            $end_date = Carbon::create($year, $i, 1, '00', '00', '00')->endOfMonth();
            $period = CarbonPeriod::create($first_date, $end_date);

            $day_of_week = intval($first_date->format('N'));
            $offset = $day_of_week > 1 ? $day_of_week - 1 : 0;
            $key = $first_date->format('n');

            $dates = [];

            foreach ($period as $date) {
                $d = $date->format('Y-m-d');
                $cal_key = isset($cal_prices[$d]) ? $cal_prices[$d] : null;

                array_push($dates, [
                    'date' => $date->format('j'),
                    'sat' => 'Sat' == $date->format('D'),
                    'sun' => 'Sun' == $date->format('D'),
                    'price' => $cal_key ? $cal_key['price'] : $room->default_price,
                    'season' => $cal_key ? $cal_key['season'] : 'NOTSET',
                    'start' => $cal_key ? $cal_key['start'] : false,
                    'end' => $cal_key ? $cal_key['end'] : false,
                    'css_class' => $cal_key ? $cal_key['css_class'] : false,
                ]);
            }

            if (!isset($cal[$key])) {
                $cal[$key] = [
                    'month' => $first_date->format('M'),
                    'offset' => intval($offset),
                    'dates' => $dates,
                    'onset' => intval(37 - intval($offset) - intval($period->count())),
                ];
            }
        }

        $path = "/tenancy/assets/images/rooms/{$id}/{$room->featured_image}";
        $picture = @file_exists(public_path($path)) ? $path : null;

        $gallery_path = '/tenancy/assets/images/rooms/'. $id .'/';
        $gallery_files = @file_exists(public_path($gallery_path)) ? File::files(public_path($gallery_path)) : [];

        return view('Booking.rooms.show', compact('room', 'locations', 'cal', 'season_legends', 'year', 'picture', 'gallery_files'));
    }

    /**
     * NEW ROOM.
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function create()
    {
        $locations = Location::with(['rooms'])->get();

        return view('Booking.rooms.new', compact('locations'));
    }

    public function insert(Request $request)
    {
        $room = Room::create($request->only([
            'location_id',
            'name',
            'room_type',
            'room_description',
            'room_short_description',
            'bathroom_type',
            'smoking',
            'room_type',
            'inclusions',
            'limited_threshold',
            'min_nights',
            'max_nights',
            'min_guest',
            'max_guest',
            'availability',
        ]));

        $room->update([
            'bed_type' => json_encode($request->bed_type),
            'active' => $request->has('active'),
            'admin_active' => $request->has('admin_active'),
            'allow_pending' => $request->has('allow_pending'),
            'allow_private' => $request->has('allow_private'),
            'calendar_visibility' => $request->has('calendar_visibility'),
        ]);

        $this->maintenance();

        return redirect(route('tenant.rooms.show', $room->id) .'#room-details');
    }

    /**
     * ROOM DETAILS UPDATE.
     *
     * @param int $id
     *
     * @return array
     */
    public function updateRoomDetails($id, Request $request)
    {
        $room = Room::findOrFail($request->room_id);

        $room->update($request->only([
            'location_id',
            'name',
            'room_type',
            'room_description',
            'room_short_description',
            'bathroom_type',
            'smoking',
            'room_type',
            'inclusions',
            'limited_threshold',
            'min_guest', 
            'max_guest',
            'min_nights', 
            'max_nights',
            'private_space',
            'availability',
        ]));

        $room->update([
            'bed_type' => json_encode($request->bed_type),
            'active' => $request->has('active'),
            'admin_active' => $request->has('admin_active'),
            'allow_pending' => $request->has('allow_pending'),
            'calendar_visibility' => $request->has('calendar_visibility'),
        ]);

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * ROOM PRICING UPDATE.
     *
     * @param int $id
     *
     * @return array
     */
    public function updateRoomPrices($id, Request $request)
    {
        $room = Room::with(['progressive_prices', 'occupancy_prices'])->findOrFail($request->room_id);
        $pp = $request->has('progressive_price') ? $request->progressive_price : null;
        $pp_new = $request->has('progressive_price_new') ? $request->progressive_price_new : null;
        $op = $request->has('occupancy_price') ? $request->occupancy_price : null;
        $op_new = $request->has('occupancy_price_new') ? $request->occupancy_price_new : null;

        $room->update($request->only([
            'default_price',
            'empty_fee_low',
            'empty_fee_main',
            'empty_fee_peak',
            'empty_fee_special'
        ]));

        $room->update([
            'allow_private' => $request->has('allow_private'),
            'progressive_pricing' => $request->has('progressive_pricing'),
            'occupancy_surcharge' => $request->has('occupancy_surcharge')
        ]);

        if ($pp) {
            foreach ($pp as $key => $val) {
                $price = $room->progressive_prices->where('id', $key)->first();

                $price->update([
                    'beds' => intval($val['beds']),
                    'amount' => floatval($val['amount']),
                ]);
            }
        }

        if ($pp_new) {
            foreach ($pp_new as $key => $pp) {
                ProgressivePricing::create([
                    'room_id' => $room->id,
                    'beds' => intval($pp['beds']),
                    'amount' => floatval($pp['amount']),
                ]);
            }
        }

        if ($op) {
            foreach ($op as $key => $val) {
                $price = $room->occupancy_prices->where('id', $key)->first();

                $price->update([
                    'pax' => intval($val['pax']),
                    'amount_main' => floatval($val['amount_main']),
                    'amount_low' => floatval($val['amount_low']),
                    'amount_peak' => floatval($val['amount_peak']),
                ]);
            }
        }

        if ($op_new) {
            foreach ($op_new as $key => $op) {
                OccupancyPricing::create([
                    'room_id' => $room->id,
                    'pax' => intval($op['pax']),
                    'amount_main' => floatval($op['amount_main']),
                    'amount_low' => floatval($op['amount_low']),
                    'amount_peak' => floatval($op['amount_peak']),
                ]);
            }
        }

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * UPDATE CALENDAR PRICING.
     *
     * @param int $id
     *
     * @return array
     */
    public function updateCalendarPrice($id, Request $request)
    {
        // should we check if there is overlap ?
        $dates = explode(' - ', $request->dates);
        $date_start = Carbon::createFromFormat('d.m.Y', $dates[0]);
        $date_end = Carbon::createFromFormat('d.m.Y', $dates[1]);
        $period = CarbonPeriod::create($date_start, $date_end);

        foreach ($period as $dates) {
            $date = $dates->format('Y-m-d');

            PricingCalendar::updateOrCreate(
                [
                    'room_id' => $id,
                    'date' => $date,
                ],
                [
                    'room_id' => $id,
                    'price' => $request->price,
                    'season_type' => $request->season_type,
                ]
            );
        }

        $year = $date_start->format('Y');

        return redirect('/rooms/' . $id . '?year=' . $year . '#pricing-calendar');
    }

    /**
     * BLOCK CALENDAR DATES.
     *
     * @param int $id
     *
     * @return array
     */
    public function blockCalendarDates($id, Request $request)
    {
        // should we check if there is overlap ?
        $dates = explode(' - ', $request->dates);
        $message = $request->message;
        $date_start = Carbon::createFromFormat('d.m.Y', $dates[0]);
        $date_end = Carbon::createFromFormat('d.m.Y', $dates[1]);
        $period = CarbonPeriod::create($date_start, $date_end);

        foreach ($period as $dates) {
            $date = $dates->format('Y-m-d');

            PricingCalendar::updateOrCreate(
                [
                    'date' => $date,
                    'room_id' => $id,
                ],
                [
                    'price' => 0,
                    'season_type' => strtoupper($message),
                ]
            );
        }

        $year = $date_start->format('Y');

        return response([
            'status' => 'SUCCESS',
            'year' => $year,
            'url' => '/rooms/' . $id . '?year=' . $year . '#pricing-calendar',
        ]);
    }

    /**
     * RESTORE CALENDAR DATES.
     *
     * @param int $id
     *
     * @return array
     */
    public function restoreCalendarDates($id, Request $request)
    {
        // should we check if there is overlap ?
        $dates = explode(' - ', $request->dates);
        $date_start = Carbon::createFromFormat('d.m.Y', $dates[0]);
        $date_end = Carbon::createFromFormat('d.m.Y', $dates[1]);
        $period = CarbonPeriod::create($date_start, $date_end);

        foreach ($period as $dates) {
            $date = $dates->format('Y-m-d');

            PricingCalendar::where('date', $date)->where('room_id', $id)->delete();
        }

        $year = $date_start->format('Y');

        return response([
            'status' => 'SUCCESS',
            'year' => $year,
            'url' => '/rooms/' . $id . '?year=' . $year . '#pricing-calendar',
        ]);
    }

    /**
     * REMOVE PROGRESSIVE PRICING THRESHOLD.
     *
     * @param object $request
     *
     * @return array
     */
    public function removeProgressivePricing(Request $request)
    {
        ProgressivePricing::find($request->id)->delete();

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * REMOVE OCCUPANCY PRICING THRESHOLD.
     *
     * @param object $request
     *
     * @return array
     */
    public function removeOccupancyPricing(Request $request)
    {
        OccupancyPricing::find($request->id)->delete();

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * UPDATE SUBROOM.
     *
     * @param int $id
     *
     * @return array
     */
    public function updateSubrooms($id, Request $request)
    {
        $room = Room::with(['rooms'])->findOrFail($request->room_id);
        $subrooms = $request->has('subroom') ? $request->subroom : null;
        $new_subrooms = $request->has('new_subroom') ? $request->new_subroom : null;
        $total_bed = 0;

        if ($subrooms) {
            foreach ($subrooms as $key => $subroom) {
                $sub = $room->rooms->where('id', $key)->first();

                $sub->update([
                    'name' => $subroom['name'],
                    'beds' => intval($subroom['beds']),
                ]);

                $total_bed += intVal($subroom['beds']);
            }
        }

        if ($new_subrooms) {
            foreach ($new_subrooms as $key => $subroom) {
                RoomInfo::create([
                    'room_id' => $room->id,
                    'beds' => intval($subroom['beds']),
                    'name' => $subroom['name'],
                ]);

                $total_bed += intVal($subroom['beds']);
            }
        }

        $room->update(['capacity' => $total_bed]);

        return response([
            'status' => 'success',
        ]);
    }

    public function upload($id, Request $request)
    {
        try {
            if (request()->has('file')) {
                foreach ($request->file as $file) {
                    $extension = $file->clientExtension();
                    $response = (new FileService())->upload(
                        $file,
                        '/tenancy/assets/images/rooms/'. $id,
                        'room_' . sha1(time().mt_rand()) . '.' . $extension,
                        ['w' => 800, 'h' => 500]
                    );
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return response([
            'status' => 'success',
            'redirect' => route('tenant.rooms.show', $id) .'?'. time() .'#images',
        ]);
    }

    public function setMainPicture(int $id, string $filename)
    {
        $room = Room::find($id)->update([
            'featured_image' => $filename,
        ]);

        return redirect()->route('tenant.rooms.show', ['id' => $id .'#images']);
    }

    public function deletePicture(int $id, string $filename)
    {
        $room = Room::find($id);
        
        if ($room->featured_image == $filename) {
            $room->update([
                'featured_image' => null,
            ]);
        }

        @unlink(public_path("/tenancy/assets/images/rooms/{$id}/{$filename}"));

        return redirect()->route('tenant.rooms.show', ['id' => $id .'#images']);
    }

    /**
     * Search whole location's rooms availability.
     *
     * @param Illuminate\Http\Request
     *
     * @return array
     */
    public function search(Request $request)
    {
        $dates = explode(' - ', request('dates'));
        $check_in = date('Y-m-d', strtotime($dates[0]));
        $check_out = date('Y-m-d', strtotime($dates[1]));
        $location = intval(request('location'));
        $guests = intval(request('guests'));
        $guest = 1; // change later

        $date = $this->bookingService->convertDates(request('dates'));

        // get list of the rooms for this location, along with its sub rooms, prices and progressive pricings
        $rooms = Room::orderBy('sort', 'asc')
            ->with([
                'rooms',
                'progressive_prices:id,room_id,beds,amount',
                'location:id,max_discount,min_discount,duration_discount',
            ])
            ->where('admin_active', 1)
            ->where('location_id', $location)
            ->get();

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy(
            check_in: $check_in,
            check_out: $check_out,
            rooms: $rooms,
            guest: $guest,
            exclude_ids: [],
            subroom_id: null,
            location_id: $location,
            private_booking: false,
            from_backend: true,
        );

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $date, null, null, $guest);

        $result = $result->sortBy('pos')->all();

        return response()->json([
            $result,
        ]);
    }

    /**
     * Search one subroom availability.
     *
     * @param Illuminate\Http\Request
     *
     * @return array
     */
    public function searchSubroom(Request $request)
    {
        $dates = request('startDate') . ' - ' . request('endDate');
        $date = $this->bookingService->convertDates($dates);
        $check_in = date('Y-m-d', strtotime($date['start']));
        $check_out = date('Y-m-d', strtotime($date['end']));
        $room_id = request('roomID');
        $subroom_id = request('subroomID');
        $booking_room_id = request()->has('bookingRoomID') && '' != request('bookingRoomID') ? [request('bookingRoomID')] : [];
        $private_booking = request('privateBooking');
        $guest = 1; // change later

        $rooms = Room::orderBy('sort', 'asc')
            ->with([
                'rooms',
                'progressive_prices:id,room_id,beds,amount',
                'location:id,max_discount,min_discount,duration_discount',
            ])
            ->where('id', $room_id)
            ->where('active', 1)
            ->whereHas('rooms', function ($q) use ($subroom_id) {
                $q->where('id', $subroom_id);
            })
            ->orderBy('sort', 'asc')
            ->get();

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy(
            check_in: $check_in,
            check_out: $check_out,
            rooms: $rooms,
            guest: $guest,
            exclude_ids: $booking_room_id,
            subroom_id: $subroom_id,
            location_id: null,
            private_booking: $private_booking,
            from_backend: true
        );

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $date, $subroom_id, $private_booking, $guest);

        return response($result);
    }

    /**
     * Search one room availability.
     *
     * @param Illuminate\Http\Request
     *
     * @return array
     */
    public function searchRoom(Request $request)
    {
        $dates = request('dates');
        $date = $this->bookingService->convertDates($dates);
        $check_in = date('Y-m-d', strtotime($date['start']));
        $check_out = date('Y-m-d', strtotime($date['end']));
        $room_id = request('roomID');
        $private_booking = request('privateBooking');
        $guest = 1; // change later

        $rooms = Room::with(['location', 'rooms', 'prices', 'progressive_prices'])
            ->where('id', $room_id)
            ->where('active', 1)
            ->orderBy('sort', 'asc')
            ->get();

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy($check_in, $check_out, $rooms, $guest, [], null, null, $private_booking);

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $date, null, $private_booking, $guest);

        // calculate default addon
        $camp_id = $rooms[0]->location->id;

        $addons_result = [];

        $result['template'] = [
            'addons' => implode("\n", $addons_result),
            'service' => 'Service in ' . $rooms[0]->location->name . "\n" . $rooms[0]->location->service,
        ];

        return response($result);
    }

    /**
     * Price Calculator.
     */
    public function priceCalculatorIndex()
    {
        $locations = Location::with(['rooms:id,location_id,name,allow_private', 'rooms.rooms:id,room_id,name']);

        $locations = $locations->orderBy('name', 'asc')->get(['id', 'name', 'abbr']);

        return view('Booking.calculator.index', compact('locations'));
    }

    public function removeSubroom($id, $subroom_id)
    {
        RoomInfo::find($subroom_id)->delete();

        return redirect('/rooms/' . $id . '#rooms');
    }

    public function maintenance()
    {
        $rooms = Room::orderBy('location_id', 'asc')->orderBy('id', 'asc')->get(['id', 'name', 'location_id', 'sort', 'cal_sort']);
        $counter = [];

        foreach ($rooms as $room) {
            if (!isset($counter[$room->location_id])) {
                $counter[$room->location_id] = 1;
            }

            $room->update([
                'sort' => $counter[$room->location_id],
                'cal_sort' => $counter[$room->location_id],
            ]);

            $counter[$room->location_id] += 1;
        }

        return 'OK';
    }

    public function sort()
    {
        $sort = request('data');

        foreach ($sort as $pos => $id) {
            if (!$id) { continue; }
            Room::find($id)->update([
                'sort' => intVal($pos) + 1
            ]);
        }

        return response('OK');
    }
}
