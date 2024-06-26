<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Blacklist;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingRoomGuest;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\Document;
use App\Models\Booking\Extra;
use App\Models\Booking\Guest;
use App\Models\Booking\Location;
use App\Models\Booking\Profile;
use App\Models\Booking\Questionnaire;
use App\Models\Booking\Room;
use App\Models\Booking\RoomInfo;
use App\Models\Booking\SpecialPackage;
use App\Models\Booking\TransferExtra;
use App\Models\Booking\Voucher;
use App\Services\Booking\AffiliationService;
use App\Services\Booking\AutomatedEmailService;
use App\Services\Booking\BookingService;
use App\Services\Booking\LocationService;
use App\Services\Booking\PaymentService;
use App\Services\Booking\RoomService;
use App\Services\Booking\TaxService;
use App\Services\Booking\UserService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $roomService;
    protected $paymentService;

    public function __construct(BookingService $bookingService, UserService $userService, RoomService $roomService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->roomService = $roomService;
        $this->paymentService = $paymentService;
    }

    protected function checkDateFormat(string $date)
    {
        $tmp = explode('-', $date);
        if (strlen($tmp[0]) == 2) {
            // kebalik
            return $tmp[2] .'-'. $tmp[1] .'-'. $tmp[0];
        }

        return $date;
    }

    /**
     * STEP 1 OF BOOKING.
     */
    public function bookNow()
    {
        $step = request()->has('step') ? request('step') : 1;

        $locations = Location::orderBy('name', 'asc')->where('active', 1)->get(['name', 'id', 'description']);

        $location = request()->has('location_id') ? request('location_id') : null;
        $room_id = request()->has('room_id') ? request('room_id') : null;
        $check_in = request()->has('check_in') ? $this->checkDateFormat(request('check_in')) : null;
        $check_out = request()->has('check_out') ? $this->checkDateFormat(request('check_out')) : null;
        $guest = request()->has('guest') ? request('guest') : null;
        $add_ons = request()->has('add_ons') ? (strlen(request('add_ons')) > 0 ? request('add_ons') : 'none') : null;
        $transfers = request()->has('transfers') ? (strlen(request('transfers')) > 0 ? request('transfers') : 'none') : null;
        $ga = request()->has('_ga') ? '?_ga='.request('_ga') : null;

        $step = 1;

        if ($location) {
            $is_location_exists = $locations->filter(fn ($loc) => $loc->id == $location)->count() > 0;

            if (!$is_location_exists) {
                return redirect('book-now');
            }

            session(['location' => $location]);
            return redirect('book-now/rooms'.$ga);
        }

        if ($room_id && $check_in && $check_out && $guest) {
            $room = Room::find($room_id);
            session([
                'booking_date_start' => $check_in,
                'booking_date_end' => $check_out,
                'guest' => $guest,
                'location' => $room->location_id,
            ]);

            $date_start = new Carbon(session('booking_date_start'));
            $date_end = new Carbon(session('booking_date_end'));

            $dates = [
                'start' => $date_start->format('d M Y'),
                'end' => $date_end->format('d M Y'),
                'duration' => $date_start->diffInDays($date_end),
            ];

            $bed_type = $room->beds[0];
            $guest = intval($guest);
            $private_booking = 0;

            if ('Private' == $room->room_type && $guest <= 1) {
                $private_booking = 1;
            }

            $this->addRoom($room_id, $dates, $check_in, $check_out, $private_booking, $bed_type, $room->room_type, $guest, $add_ons, $transfers);

            if (request()->has('add_ons')) {
                return redirect('book-now/details'.$ga);
            } else {
                return redirect('book-now/extras'.$ga);
            }
        }

        return view('Booking.booking', compact('step', 'locations'));
    }

    /**
     * PROCESS STEP 1.
     */
    public function selectLocation(Request $request)
    {
        $location = $request->location_id;

        $camp = Location::with(['rule'])->find($location);
        $min_nights = $camp->minimum_nights ?? 7;

        $start = Carbon::now();
        $end = Carbon::now()->addDays($min_nights);

        $ls = new LocationService;

        list ('start' => $start, 'end' => $end) = $ls->getMinimumCheckIn($camp);

        session([
            'booking_date_start' => $start->format('Y-m-d'),
            'booking_date_end' => $end->format('Y-m-d'),
            'guest' => 1,
        ]);

        session(['location' => $location]);
        session(['room' => []]);

        if (session()->has('booking_ref')) {
            // delete booking
            $this->bookingService->deleteBooking(session('booking_ref'));
            session()->forget('booking_ref');
        }

        return redirect('book-now/rooms');
    }

    public function checkDatesDuration()
    {
        $dates = explode(' - ', request('dates'));
        $minimum = intval(request('minimum'));

        $start = Carbon::createFromFormat('d M Y', $dates[0]);
        $end = Carbon::createFromFormat('d M Y', $dates[1]);

        $duration = $start->diffInDays($end);

        return response([
            'duration' => intval($duration),
            'result' => intval($duration) >= $minimum,
        ]);
    }

    public function saveComment()
    {
        $comment = request('comment');

        session(['room.comment' => $comment]);

        return response('OK');
    }

    /**
     * UPDATE DATES AND GUEST FROM STEP 2.
     */
    public function updateDatesAndGuest(Request $request)
    {
        $dates = explode(' - ', request('dates'));
        $guest = request('guest');

        $start = Carbon::createFromFormat('d M Y', $dates[0]);
        $end = Carbon::createFromFormat('d M Y', $dates[1]);

        session([
            'booking_date_start' => $start->format('Y-m-d'),
            'booking_date_end' => $end->format('Y-m-d'),
            'guest' => $guest,
        ]);

        return redirect('book-now/rooms');
    }

    /**
     * STEP 2 : SELECT ROOM CATEGORY.
     */
    public function selectRooms()
    {
        if (!session()->has('location')) {
            return redirect('book-now');
        }

        if (session()->has('booking_ref')) {
            // delete booking
            $this->bookingService->deleteBooking(session('booking_ref'));
            session()->forget('booking_ref');
        }

        $documents = Document::orderBy('sort', 'asc')->get();

        $date_start = session()->has('booking_date_start') ? new Carbon(session('booking_date_start')) : new Carbon(date('Y-m-d'));
        $date_end = session()->has('booking_date_end') ? new Carbon(session('booking_date_end')) : new Carbon(date('Y-m-d'));

        if (!session()->has('booking_date_end')) {
            $date_end = $date_end->addDays(7);
        }

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $location_id = session('location');

        $room_ids = Room::where('location_id', $location_id)->pluck('id')->toArray();

        $rooms = Room::orderBy('sort', 'asc')
            ->with(['rooms', 'location', 'prices' => function ($query) use ($date_start, $date_end, $room_ids) {
                $query
                    ->whereIn('room_id', $room_ids)
                    ->where('date', '>=', $date_start->format('Y-m-d'))
                    ->where('date', '<=', $date_end->format('Y-m-d'))
              ;
            }, 'progressive_prices'])
            ->where('active', 1)
            ->where('location_id', $location_id)
            ->get()
            ->map(function ($room) {
                $path = public_path('/tenancy/assets/images/rooms/'.$room->id);
                $room['gallery'] = @file_exists($path)
                    ? collect(File::files($path))->map(fn ($file) => $file->getFilename())->filter(fn ($file) => $file != $room->featured_image)
                    : [];
                return $room;
            });

        $guest = session()->has('guest') ? intval(session('guest')) : 2;
        $step = 2;

        $occupancy = $this->roomService->getRoomOccupancy($date_start->format('Y-m-d'), $date_end->format('Y-m-d'), $rooms, $guest);

        $rooms_list = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, null, $guest);

        $rooms_list = $rooms_list->sortBy('pos')->all();

        $availability = $this->roomService->getAvailabilityList($rooms_list, $guest);

        $location = Location::find(session('location'));

        $arrival_dates = null;
        $has_rule = false;
        $rule_dates = [];
        $rule_option = null;
        $min_nights = $location->minimum_nights ? $location->minimum_nights : 7;

        $disable_check_in_dates = '';
        $disable_check_out_dates = '';
        $start_date = $default_check_in;
        $end_date = $default_check_out;

        if ($location->has_arrival_rule) {
            $has_rule = true;
            $rule_option = $location->rule->option;
            $temp_dates = explode(' - ', $location->rule->period);
            $disable_check_in_days = json_decode($location->rule->disable_check_in_days, true);
            $disable_check_out_days = json_decode($location->rule->disable_check_out_days, true);

            $disable_check_in_dates = [];
            $disable_check_out_dates = [];

            if (!$disable_check_in_days && !$disable_check_out_days) {
                $start_date = Carbon::createFromFormat('d.m.Y', $temp_dates[0])->format('d M Y');
                $end_date = Carbon::createFromFormat('d.m.Y', $temp_dates[1])->format('d M Y');
            }

            $arrival_dates = [
                'start' => Carbon::createFromFormat('d.m.Y', $temp_dates[0])->startOfDay(),
                'end' => Carbon::createFromFormat('d.m.Y', $temp_dates[1])->startOfDay()
            ];

            if ($disable_check_in_days || $disable_check_out_days) {
                $periods = new CarbonPeriod($arrival_dates['start'], $arrival_dates['end']);

                foreach ($periods as $key => $date) {
                    $day = $date->format('l');

                    if (in_array($day, $disable_check_in_days)) {
                        $disable_check_in_dates[] = $date->format('Y-m-d');
                    }

                    if (in_array($day, $disable_check_out_days)) {
                        $disable_check_out_dates[] = $date->format('Y-m-d');
                    }
                }
            }

            $disable_check_in_dates = implode("', '", $disable_check_in_dates);
            $disable_check_out_dates = implode("', '", $disable_check_out_dates);

            // if rule is active, check the date and set the default date instead
            // TODO
            $start = $arrival_dates['start'];
            $today = $date_start->startOfDay();

            /* comment the code below for now as it cause the date to be wrong
            if (!$disable_check_in_days || !$disable_check_out_days) {
                if ($today->lt($start) || $today->gt($start)) {
                    $arrival_dates = [
                        'start' => $start->startOfDay(),
                        'end' => Carbon::parse($start)->addDays($min_nights)->startOfDay()
                    ];

                    $default_check_in = $arrival_dates['start']->format('d M Y');
                    $default_check_out = $arrival_dates['end']->format('d M Y');
                } elseif ($today->eq($start)) {
                    // equal date
                    $start = $today;
                }
            }
            */
        }

        return view('Booking.booking', compact('step', 'documents', 'rooms', 'default_check_in', 'default_check_out', 'availability', 'guest', 'location', 'has_rule', 'min_nights', 'dates', 'disable_check_in_dates', 'disable_check_out_dates', 'start_date', 'end_date'));
    }

    public function updateBedType(Request $request)
    {
        $bed = request('bed');

        session([
            'room.bed_type' => $bed,
        ]);

        return response('OK');
    }

    /**
     * UPDATE GUEST NUMBER FROM EACH ROOM SELECTION.
     */
    public function updateRoomGuest(Request $request)
    {
        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $dates = [
            'start' => $date_start->format('d M Y'),
            'end' => $date_end->format('d M Y'),
            'duration' => $date_start->diffInDays($date_end),
        ];

        $check_in = $date_start->format('Y-m-d');
        $check_out = $date_end->format('Y-m-d');
        $room_id = request('roomID');
        $private_booking = intval(request('privateBooking'));
        $guest = intval(request('guest'));
        $bed_type = request()->has('bedType') ? request('bedType') : ($private_booking ? 'Double' : 'Twin');
        $room = session('room');

        $rooms = Cache::remember('room_book-now_room-'.$room_id, 10, function () use ($room_id) {
            return Room::with(['rooms', 'prices', 'progressive_prices'])
                ->where('id', $room_id)
                ->where('active', 1)
                ->orderBy('sort', 'asc')
                ->get()
            ;
        });

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy($check_in, $check_out, $rooms, $guest, [], null, null, $private_booking);

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, $private_booking, $guest);

        $availability = $this->roomService->getAvailabilityList($result, $guest);

        $total_addon = isset($room['addons']) ? $room['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        }) : 0;

        $total_transfer = isset($room['transfers']) ? $room['transfers']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        }) : 0;

        $grand_total = number_format(intval($availability[$room_id]['price']) + floatval($total_addon) + floatval($total_transfer), 2);

        session([
            'room.private_booking' => $private_booking,
            'room.price' => round($availability[$room_id]['price']),
            'room.bed_type' => $bed_type,
        ]);

        $availability[$room_id]['grand_total'] = '&euro;'.$grand_total;

        return response($availability[$room_id]);
    }

    public function applyVoucher(Request $request)
    {
        $voucher = strtoupper($request->voucher);

        // get total booking with voucher
        $totalUsed = Booking::where('voucher', $voucher)->where('status', 'CONFIRMED')->count();
        $today = today()->format('Y-m-d');

        // check voucher
        $record = Voucher::active()->where('voucher_code', $voucher)
                        ->where(function ($query) use ($totalUsed) {
                            $query->where('usage_limit', 0)
                                ->orWhere('usage_limit', '>=', $totalUsed);
                        })
                        ->where(function ($query) use ($today) {
                            $query->whereNull('expired_at')
                                ->orWhere('expired_at', '>=', $today);
                        })->first();

        if ($record) {
            $booking_room = session('room');
            $price = $booking_room['price'];
            $tax_info = $booking_room['tax_info'];

            $total_discount = 0;
            switch ($record->amount_type) {
                case 'PERCENTAGE':
                    $total_discount = floatval($price * intval($record->amount) / 100);
                    break;

                default:
                    $total_discount = floatval($record->amount);
                    break;
            }

            $room_price = floatval($price - $total_discount);
            session([
                'room.tax' => number_format($room_price * ($tax_info['cultural_tax'] / 100), 2),
                'room.other_tax' => [
                    'hotel_tax' => $this->bookingService->calculateTax($tax_info['hotel_tax'], $room_price),
                    'goods_tax' => $this->bookingService->calculateTax($tax_info['goods_tax'], $room_price),
                ],
                'room.accommodation_price' => $room_price,
                'room.voucher' => $voucher,
                'room.voucher_detail' => [
                    'type' => $record->amount_type,
                    'amount' => $record->amount,
                    'discount' => $total_discount
                ]
            ]);

            return response([
                'status' => 'SUCCESS',
                'code' => $voucher,
                'price' => $room_price
            ]);
        }

        return response([
            'status' => 'ERROR',
        ]);
    }

    public function cancelVoucher(Request $request)
    {
        $booking_room = session('room');
        $price = $booking_room['price'];
        $tax_info = $booking_room['tax_info'];

        session([
            'room.tax' => number_format($price * ($tax_info['cultural_tax'] / 100), 2),
            'room.other_tax' => [
                'hotel_tax' => $this->bookingService->calculateTax($tax_info['hotel_tax'], $price),
                'goods_tax' => $this->bookingService->calculateTax($tax_info['goods_tax'], $price),
            ],
            'room.accommodation_price' => $price,
            'room.voucher' => '',
            'room.voucher_detail' => null
        ]);

        $url = $request->url . '?' . time();

        return response([
            'status' => 'SUCCESS',
            'url' => $url,
        ]);
    }

    public function addRoom($room_id, $dates, $check_in, $check_out, $private_booking, $bed_type, $room_type, $guest, $add_ons = null, $transfers_addons = null)
    {
        $ref = $this->bookingService->generateBookingRef();

        $rooms = Room::with(['rooms', 'prices', 'progressive_prices'])
            ->where('id', $room_id)
            ->where('active', 1)
            ->orderBy('sort', 'asc')
            ->get();

        $room = Room::with('location')->findOrFail($room_id);

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy($check_in, $check_out, $rooms, $guest, [], null, null, $private_booking);

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, $private_booking, $guest);

        $availability = $this->roomService->getAvailabilityList($result, $guest);

        $availability = $availability[$room_id];

        $check_dates = [
            'start' => $check_in,
            'end' => $check_out,
        ];

        // find eligible offer
        $offer = $this->bookingService->checkSpecialOffer($room_id, $check_dates, $guest, $availability);

        $room_key = uniqid();

        $addons = collect([]);
        $addons_key = [];
        $transfers = collect([]);
        $transfers_key = [];

        session()->forget('room');

        if ($add_ons) {
            if ($add_ons != 'none') {
                $add_on_details_array = [];

                $add_on_indexes = [];

                $add_ons_array = explode("_", $add_ons);

                foreach ($add_ons_array as $add_on) {
                    $add_on_details = explode("x", $add_on);

                    $add_on_details_array[$add_on_details[0]]['id'] = $add_on_details[0];
                    $add_on_details_array[$add_on_details[0]]['guests'] = $add_on_details[1];
                    $add_on_details_array[$add_on_details[0]]['days'] = $add_on_details[2];

                    array_push($add_on_indexes, $add_on_details[0]);
                }

                $selected_addons = Extra::whereIn('id', $add_on_indexes)->get();

                if ($selected_addons) {
                    foreach ($selected_addons as $addon) {
                        if ('Fixed' == $addon->rate_type) {
                            $amount = 1;
                            $price = $addon->base_price;
                        } else {
                            $price = $addon->base_price * $dates['duration'];
                            $amount = $add_on_details_array[$addon->id]['days'];
                        }

                        $addons->push([
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'unit_name' => $addon->unit_name,
                            'guests' => $add_on_details_array[$addon->id]['guests'],
                            'amount' => $amount,
                            'price' => $price,
                            'total' => $this->bookingService->calculateAddon($addon, $add_on_details_array[$addon->id]['days'], $add_on_details_array[$addon->id]['guests']),
                            'questionnaire_id' => $addon->questionnaire_id,
                        ]);
                    }
                }
            }
        } else {
            // add complimentary add-ons & transfers
            $default_addons = Extra::where('add_default', 1)
                ->whereHas('rooms', function ($q) use ($room_id) {
                    $q->where('room_id', $room_id);
                })
                ->get()
            ;

            if ($default_addons) {
                foreach ($default_addons as $addon) {
                    if (
                        ((!is_null($addon->max_stay) && $dates['duration'] <= $addon->max_stay) && ($dates['duration'] >= $addon->min_stay)) ||
                        (is_null($addon->max_stay) && ($dates['duration'] >= $addon->min_stay))
                    ) {
                        if ('Fixed' == $addon->rate_type) {
                            $price = $addon->base_price;
                            $amount = 1;
                        } else {
                            $price = $addon->base_price * $dates['duration'];
                            $amount = $dates['duration'];
                        }

                        $addons->push([
                            'id' => $addon->id,
                            'name' => $addon->name,
                            'unit_name' => $addon->unit_name,
                            'guests' => $guest,
                            'amount' => $amount,
                            'price' => $price,
                            'total' => $this->bookingService->calculateAddon($addon, $dates['duration'], $guest),
                            'questionnaire_id' => $addon->questionnaire_id,
                        ]);
                    }
                }
            }
        }

        if ($transfers_addons) {
            if ($transfers_addons != 'none') {
                $transfer_details_array = [];

                $transfers_array = explode("_", $transfers_addons);

                foreach ($transfers_array as $transfer) {
                    $details_array = explode("x", $transfer);

                    $transfer_details_array[$details_array[0]]['id'] = $details_array[0];
                    $transfer_details_array[$details_array[0]]['guests'] = $details_array[1];
                }

                if (count($transfer_details_array) > 0) {
                    $directions = array("1"=>"Inbound", "2"=>"Outbound");

                    foreach ($directions as $transferDirectionKey => $transferDirectionVal) {
                        $transfersItem = TransferExtra::with(['prices'])
                            ->whereHas('rooms', function ($q) use ($room_id) {
                                $q->where('room_id', $room_id);
                            })
                            ->where('direction', $transferDirectionVal)
                            ->first();

                        if (array_key_exists($transferDirectionKey, $transfer_details_array)) {
                            $transfers->push([
                                'id' => $transfersItem->id,
                                'name' => $transfersItem->name,
                                'flight_number' => 'TBA',
                                'flight_time' => null,
                                'guests' => $guest,
                                'price' => $transfersItem->prices->where('guest', 1)->first()->price,
                                'total' => $this->bookingService->calculateTransfer($transfersItem, $dates['duration'], $transfer_details_array[$transferDirectionKey]['guests']),
                            ]);
                        }
                    }
                }
            }
        } else {
            $default_transfers = TransferExtra::with(['prices'])
                ->where('add_default', 1)
                ->where('default_min_nights', '<=', $dates['duration'])
                ->whereHas('rooms', function ($q) use ($room_id) {
                    $q->where('room_id', $room_id);
                })
                ->get()
            ;

            if ($default_transfers) {
                foreach ($default_transfers as $transfer) {
                    if ($transfer->is_complimentary && $dates['duration'] >= $transfer->complimentary_min_nights) {
                        $transfers->push([
                            'id' => $transfer->id,
                            'name' => $transfer->name,
                            'flight_number' => 'TBA',
                            'flight_time' => null,
                            'guests' => $guest,
                            'price' => 0,
                            'total' => 0,
                        ]);
                    }
                }
            }
        }

        // minus the special offer amount if there is any
        $price = $offer && isset($offer['discount']) ? $availability['price'] - $offer['discount'] : $availability['price'];

        session(['room' => [
            'key' => $room_key,
            'name' => $availability['name'],
            'room_id' => $room_id,
            'featured_image' => $room->featured_image,
            'location_id' => $room->location->id,
            'guest' => $guest,
            'room_type' => $room_type,
            'private_booking' => $private_booking,
            'empty_fee' => $availability['surcharge'],
            'total_empty_fee' => $availability['surcharge'],
            'bed_type' => $bed_type,
            'tax' => number_format($price * ($room->location->cultural_tax / 100), 2),
            'other_tax' => [
                'hotel_tax' => $this->bookingService->calculateTax($room->location->hotel_tax, $price),
                'goods_tax' => $this->bookingService->calculateTax($room->location->goods_tax, $price),
            ],
            'tax_info' => [
                'cultural_tax' => floatval($room->location->cultural_tax),
                'hotel_tax' => floatval($room->location->hotel_tax),
                'goods_tax' => floatval($room->location->goods_tax),
            ],
            'accommodation_price' => floatVal($price),
            'price' => floatval($price),
            'duration' => $dates['duration'],
            'duration_discount' => round($availability['duration_discount']),
            'availability_status' => $availability['availability_status'],
            'is_available' => $availability['is_available'],
            'addons_key' => $addons_key,
            'addons' => $addons,
            'open_spot' => $availability['open_spot'],
            'transfers_key' => $transfers_key,
            'transfers' => $transfers,
            'inclusions' => $rooms->first()->inclusions_formatted,
            'voucher' => '',
            'voucher_detail' => null,
            'special_offer' => $offer,
            'offer_discount' => $offer ? floatval($offer['discount']) : 0,
            'comment' => '',
        ]]);
    }

    /**
     * PROCESS SELECTED ROOMS.
     */
    public function processRooms(Request $request)
    {
        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $dates = [
            'start' => $date_start->format('d M Y'),
            'end' => $date_end->format('d M Y'),
            'duration' => $date_start->diffInDays($date_end),
        ];

        $check_in = $date_start->format('Y-m-d');
        $check_out = $date_end->format('Y-m-d');
        $room_id = request('room_id');
        $private_booking = request()->has('private_booking');
        $bed_type = request('bed_type');
        $room_type = request('room_type');
        $guest = intval(request('guest'));

        $this->addRoom($room_id, $dates, $check_in, $check_out, $private_booking, $bed_type, $room_type, $guest);

        return redirect('book-now/extras');
    }

    /**
     * STEP 3 - EXTRA.
     */
    public function selectExtras()
    {
        $ga = request()->has('_ga') ? '?_ga='.request('_ga') : null;
        $ga = is_null($ga) && request()->has('_gl') ? '?_gl='.request('_gl') : $ga;

        $documents = Document::orderBy('sort', 'asc')->get();

        if (!session()->has('location') || !session()->has('room')) {
            return redirect('book-now');
        }

        if (session()->has('booking_ref')) {
            // delete booking
            $this->bookingService->deleteBooking(session('booking_ref'));
            session()->forget('booking_ref');
        }

        $guest = session()->has('guest') ? intval(session('guest')) : 2;

        $date_start = session()->has('booking_date_start') ? new Carbon(session('booking_date_start')) : new Carbon(date('Y-m-d'));
        $date_end = session()->has('booking_date_end') ? new Carbon(session('booking_date_end')) : new Carbon(date('Y-m-d'));

        if (!session()->has('booking_date_end')) {
            $date_end = $date_end->addDays(7);
        }

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $duration = intval($dates['duration']);

        $booking_room = session('room');

        $rooms = Room::with(['rooms', 'prices' => function ($query) use ($date_start, $date_end, $booking_room) {
            $query
                ->where('room_id', $booking_room['room_id'])
                ->where('date', '>=', $date_start->format('Y-m-d'))
                ->where('date', '<=', $date_end->format('Y-m-d'));
            }, 'progressive_prices'])
            ->where('location_id', session('location'))
            ->where('active', 1)
            ->orderBy('sort', 'asc')
            ->get()
            ->map(function ($room) {
                $path = public_path('/tenancy/assets/images/rooms/'.$room->id);
                $room['gallery'] = @file_exists($path)
                    ? collect(File::files($path))->map(fn ($file) => $file->getFilename())->filter(fn ($file) => $file != $room->featured_image)
                    : [];
                return $room;
            });

        $addons = Extra::whereHas('rooms', function ($q) use ($booking_room) {
            $q->where('room_id', $booking_room['room_id']);
        })
            ->with(['prices'])
            ->where('active', 1)
            ->where('admin_only', 0)
            ->where('hidden', 0)
            ->orderBy('sort', 'asc')
            ->get([
                'id', 'description', 'name', 'rate_type', 'base_price', 'is_flexible', 'unit_name',
                'min_stay', 'max_stay', 'min_guests', 'max_guests', 'min_units', 'max_units', 'sort', 'week_question'
            ])
            ->map(function ($item, $key) use ($dates, $booking_room) {
                // always start with 1 guest
                $item['total'] = $this->bookingService->calculateAddon($item, $dates['duration'], 1);

                return $item;
            })
            ->filter(function ($item, $key) use ($duration) {
                if (is_null($item->max_stay)) {
                    return $item;
                }

                return $duration >= $item->min_stay && $duration <= $item->max_stay;
            });

        if ($addons->count() <= 0) {
            return request()->has('details') ? redirect('book-now/rooms') : redirect('book-now/details');
        }

        $location = Location::find(session('location'));

        $total_addon = $this->bookingService->getTotalAddons($booking_room);

        $total_transfer = $booking_room['transfers']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $transfers = TransferExtra::with(['prices'])
            ->whereHas('rooms', function ($q) use ($booking_room) {
                $q->where('room_id', $booking_room['room_id']);
            })
            ->where('is_active', 1)
            ->get()
            ->map(function ($item, $key) use ($dates, $booking_room) {
                $data = collect($booking_room['transfers'])->where('id', $item->id)->first();
                $guest = $data ? intval($data['guests']) : $booking_room['guest'];
                $item['price'] = $item->is_complimentary && ($dates['duration'] >= $item->complimentary_min_nights) ? 0 : floatval($item->prices->where('guest', $guest)->first()->price);

                return $item;
            });

        $step = 3;

        $location = Location::findOrFail($booking_room['location_id']);

        $tax_info = $this->bookingService->displayTaxInfo($booking_room, $location);

        $weeks = $date_start->diffInWeeks($date_end);

        $tax = TaxService::getActiveTaxes();

        return view('Booking.booking', compact(
            'ga', 'location', 'step', 'rooms', 'guest', 'booking_room', 'default_check_in',
            'default_check_out', 'addons', 'duration', 'total_addon', 'total_transfer',
            'transfers', 'tax_info', 'documents', 'weeks', 'tax'
        ));
    }

    /**
     * ADD ADDON.
     */
    public function addAddon(Request $request)
    {
        $origin = request()->has('origin') ? request('origin') : 'book-now';

        $response = $origin == 'book-now' ? $this->addNormalAddon() : $this->addSPAddon();

        return response($response);
    }

    /**
     * GET ADDON PRICE.
     */
    public function getAddonPrice(Request $request)
    {
        $id = request('id');
        $amount = request('duration');
        $guest = request('guest');

        $addon = Extra::find($id);

        $addon_price = $this->bookingService->calculateAddon($addon, $amount, $guest);

        return response([
            'status' => 'OK',
            'id' => $addon->id,
            'price' => '&euro;'.number_format(floatval($addon_price), 2),
        ]);
    }

    /**
     * REMOVE ADDON.
     */
    public function removeAddon(Request $request)
    {
        $origin = request()->has('origin') ? request('origin') : 'book-now';
        $id = request('id');

        $tax_info = null;

        if ('book-now' == $origin) {
            $room = session('room');

            if ($room['addons']->contains('id', $id)) {
                $room['addons'] = $room['addons']->filter(function ($value, $key) use ($id) {
                    return $value['id'] != $id;
                });
            }

            session(['room' => $room]);

            $grand_total = $this->recalculateGrandTotal();

            $location = Location::findOrFail($room['location_id']);

            $tax_info = $this->bookingService->displayTaxInfo($room, $location);
        } else {
            $extras = null;
            if (session('sp_extras')->contains('id', $id)) {
                $extras = session('sp_extras')->filter(function ($value, $key) use ($id) {
                    return $value['id'] != $id;
                });
            }

            session(['sp_extras' => $extras]);

            $grand_total = $this->recalculateSPGrandTotal();
        }

        return response([
            'status' => 'OK',
            'grand_total' => '&euro;'.number_format($grand_total, 2),
            'tax_info' => $tax_info
        ]);
    }

    public function recalculateGrandTotal()
    {
        return $this->bookingService->recalculateGrandTotal();
    }

    public function recalculateSPGrandTotal()
    {
        return $this->bookingService->recalculateSPGrandTotal();
    }

    /**
     * ADD TRANSFER.
     */
    public function addTransfer(Request $request)
    {
        $origin = request()->has('origin') ? request('origin') : 'book-now';

        if ('book-now' == $origin) {
            $response = $this->addNormalTransfer();
        } else {
            $response = $this->addSPTransfer();
        }

        return response($response);
    }

    /**
     * REMOVE TRANSFER.
     */
    public function removeTransfer(Request $request)
    {
        $origin = request()->has('origin') ? request('origin') : 'book-now';
        $id = request('id');

        if ('book-now' == $origin) {
            $room = session('room');

            if ($room['transfers']->contains('id', $id)) {
                $room['transfers'] = $room['transfers']->filter(function ($value, $key) use ($id) {
                    return $value['id'] != $id;
                });
            }

            session(['room' => $room]);

            $grand_total = $this->recalculateGrandTotal();
        } else {
            $transfers = null;
            if (session('sp_transfers')->contains('id', $id)) {
                $transfers = session('sp_transfers')->filter(function ($value, $key) use ($id) {
                    return $value['id'] != $id;
                });
            }

            session(['sp_transfers' => $transfers]);

            $grand_total = $this->recalculateSPGrandTotal();
        }

        return response([
            'status' => 'OK',
            'grand_total' => '&euro;'.number_format($grand_total, 2),
        ]);
    }

    /**
     * GET TRANSFER PRICE.
     */
    public function getTransferPrice(Request $request)
    {
        $room = session('room');
        $id = request('id');
        $guest = request('guest');

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $transfer = TransferExtra::with(['prices'])->find($id);

        if ($transfer->is_complimentary && $dates['duration'] >= $transfer->complimentary_min_nights) {
            $price = 'FREE';
        } else {
            $transfer_price = $transfer->prices->where('guest', $guest)->first()->price;
            $price = '&euro;'.number_format(round($transfer_price, 2));
        }

        return response([
            'status' => 'OK',
            'id' => $transfer->id,
            'price' => $price,
        ]);
    }

    /**
     * GUEST DETAILS.
     */
    public function guestDetails()
    {
        $step = 4;

        $guest = session('guest');

        $ga = request()->has('_ga') ? '?_ga='.request('_ga') : null;

        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        if (!session()->has('location') || !session()->has('room')) {
            return redirect('book-now');
        }

        if (session()->has('booking_ref')) {
            // delete booking
            $this->bookingService->deleteBooking(session('booking_ref'));
            session()->forget('booking_ref');
        }

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $check_in = $date_start->format('d.m.Y');
        $check_out = $date_end->format('d.m.Y');

        $booking_room = session('room');

        $addons = $booking_room['addons']->toArray();
        $questionnaires = [];
        foreach ($addons as $key => $addon) {
            if ($addon['questionnaire_id']) {
                $questionnaires[$key] = Questionnaire::with('answers', 'type')->find($addon['questionnaire_id']);
                $questionnaires[$key]->addon_id = $addon['id'];
            }
        }

        $duration = intval($dates['duration']);

        $total_addon = $booking_room['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $total_transfer = $booking_room['transfers']->reduce(function ($total, $item) {
            $total += intval($item['total']);

            return $total;
        });

        $documents = Document::orderBy('sort', 'asc')->get();

        $terms = $documents->where('position', 'terms-and-conditions')->sortBy('sort')->all();

        $location = Location::findOrFail($booking_room['location_id']);

        $tax = TaxService::getActiveTaxes();

        $tax_info = $this->bookingService->displayTaxInfo($booking_room, $location);

        return view('Booking.booking', compact(
            'step',
            'booking_room',
            'default_check_in',
            'default_check_out',
            'duration',
            'total_addon',
            'countries',
            'check_in',
            'check_out',
            'guest',
            'total_transfer',
            'location',
            'documents',
            'tax_info',
            'ga',
            'terms',
            'questionnaires',
            'tax'
        ));
    }

    /**
     * SAVE GUEST DETAILS.
     */
    public function saveGuestDetails(Request $request)
    {
        $booking_room = session('room');
        $check_in = session('booking_date_start');
        $check_out = session('booking_date_end');
        $location = session('location');
        $ga = request()->has('_ga') ? '?_ga='.request('_ga') : null;
        $ga = is_null($ga) && request()->has('_gl') ? '?_gl='.request('_gl') : $ga;
        $emails = collect([]);

        $location_data = Location::find($location);

        $terms = Document::where('position', 'terms-and-conditions')->get();

        $validator = Validator::make($request->all(), $terms->mapWithKeys(function ($term, $key) {
            return ['terms.'. $term->id => 'required'];
        })->merge([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'birthdate_day' => 'required',
            'birthdate_month' => 'required',
            'birthdate_year' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'phone' => 'required|numeric',
            'arrival_time_h' => 'required_unless:skip_transfer,on',
            'arrival_time_m' => 'required_unless:skip_transfer,on',
            'arrival_flight' => 'required_unless:skip_transfer,on',
            'departure_time_h' => 'required_unless:skip_transfer,on',
            'departure_time_m' => 'required_unless:skip_transfer,on',
            'departure_flight' => 'required_unless:skip_transfer,on',
            'guest.*.fname' => 'required',
            'guest.*.lname' => 'required',
            'guest.*.email' => 'required',
            'guest.*.birthdate_day' => 'required',
            'guest.*.birthdate_month' => 'required',
            'guest.*.birthdate_year' => 'required',
            'terms' => 'required|array|min:'. $terms->count(),
        ])->toArray(), [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'phone' => 'Phone is required',
            'birthdate_day.required' => 'Birthdate (day) is required',
            'birthdate_month.required' => 'Birthdate (month) is required',
            'birthdate_year.required' => 'Birthdate (year) is required',
            'arrival_time_h.required_unless' => 'Arrival time is required',
            'arrival_time_m.required_if' => 'Arrival time is required',
            'arrival_flight.required_if' => 'Arrival flight info required',
            'departure_time_h.required_if' => 'Departure time is required',
            'departure_time_m.required_if' => 'Departure time is required',
            'departure_flight.required_if' => 'Departure flight info required',
            'terms.min' => 'You must agree to all the terms and conditions'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // first, check room availability
        $rooms = Room::with(['rooms', 'prices', 'progressive_prices'])
            ->where('id', intval($booking_room['room_id']))
            ->where('active', 1)
            ->orderBy('sort', 'asc')
            ->get();

        $date_start = new Carbon($check_in);
        $date_end = new Carbon($check_out);

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $phone = str_replace('+', '', request('phone'));

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $default_expiry_date = Carbon::now()->addDays($location_data->deposit_due)->format('Y-m-d');

        // if check in under 7 days
        if ($date_start->diffInDays($default_expiry_date) < 7) {
            $default_expiry_date = $date_start->format('Y-m-d');
        }

        // get all entry for this period of dates for this location
        $occupancy = $this->roomService->getRoomOccupancy($check_in, $check_out, $rooms, $booking_room['guest'], [], null, null, $booking_room['private_booking']);

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, $booking_room['private_booking'], $booking_room['guest']);

        $availability = $this->roomService->getAvailabilityList(room_list: $result, guest: $booking_room['guest'], private_booking: $booking_room['private_booking']);

        $availability = $availability[$booking_room['room_id']];

        DB::beginTransaction();

        $vaccine_last_shot_at = request('vaccine_status') == 'Fully vacinated' ? Carbon::createFromFormat('d.m.Y', request('vaccine_last_shot_at')) : null;

        // first : create guests
        $guest = Guest::updateOrCreate(
            [
                'email' => request('email'),
            ],
            [
                'fname' => request('fname'),
                'lname' => request('lname'),
                'company' => request('company'),
                'title' => request('title'),
                'phone' => $phone,
                'street' => request('street'),
                'zip' => request('zip'),
                'city' => request('city'),
                'country' => request('country'),
                'birthdate' => request('birthdate_year').'-'.request('birthdate_month').'-'.request('birthdate_day'),
            ]
        );

        $emails->push(strtolower(request('email')));

        session(['booking_guest' => $guest->full_name]);

        // second, create the booking
        $ref = $this->bookingService->generateBookingRef();
        $location_id = session('location');
        $affiliation_id = AffiliationService::checkAffiliationSession();
        $opportunity = $booking_room['availability_status'] == 1 ? 'Sale' : 'Pending';

        $booking = Booking::create([
            'ref' => $ref,
            'source_type' => 'Guest',
            'channel' => 'Online',
            'opportunity' => $opportunity,
            'source_id' => null,
            'location_id' => intval(session('location')),
            'expiry' => Carbon::now()->addHours(24),
            'deposit_expiry' => $default_expiry_date,
            'status' => 'RESERVED',
            'check_in' => $check_in,
            'check_out' => $check_out,
            'agent_id' => null,
            'agent_commission' => 0,
            'voucher' => isset($booking_room['voucher']) ? $booking_room['voucher'] : null,
            'origin' => request()->getHost(),
            'affiliation_id' => $affiliation_id
        ]);

        // history
        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => null,
            'action' => 'Reserved booking',
            'info_type' => 'info',
            'details' => '<b>'.$guest->full_name.'</b> reserved booking #<b>'.$booking->ref.'</b>',
            'ip_address' => request()->ip(),
        ]);

        // create booking guest
        $booking_guest = $booking->guest()->create([
            'guest_id' => $guest->id,
            'booking_id' => $booking->id,
            'group_id' => 0,
        ]);

        $subroom_id = $availability['available_rooms'][0]['id'];
        $subroom = RoomInfo::with(['room'])->where('id', $subroom_id)->first();

        $private_booking = false;

        if ($booking_room['private_booking']) {
            $private_booking = true;
        }

        if ($booking_room['guest'] == 1 && $booking_room['room_type'] == 'Private' && $subroom->room->private_space <= 0) {
            $private_booking = true;
        }

        // create booking room
        $booking_room_new = $booking->rooms()->create([
            'booking_id' => $booking->id,
            'room_id' => $booking_room['room_id'],
            'subroom_id' => $subroom_id,
            'bed' => $availability['available_rooms'][0]['bed'],
            'bed_type' => $booking_room['bed_type'],
            'bathroom' => 'Shared',
            'from' => $check_in,
            'to' => $check_out,
            'is_private' => $private_booking,
            'guest' => 1,
            'price' => (floatVal($booking_room['accommodation_price']) + floatVal($booking_room['duration_discount'])) / intVal($booking_room['guest']),
            'duration_discount' => intval($booking_room['duration_discount']) / intval($booking_room['guest']),
        ]);

        // create booking guest room
        $br_guest = BookingRoomGuest::create([
            'booking_room_id' => $booking_room_new->id,
            'booking_guest_id' => $booking_guest->id,
        ]);

        $booking_room_group = [$booking_room_new->id];

        if ($booking_room['guest'] > 1) {
            // multiple guest
            $extra_guests = request('guest');

            foreach ($extra_guests as $index => $extra_guest) {
                ++$index;
                $new_extra_guest = Guest::updateOrCreate(
                    [
                        'email' => $extra_guest['email'],
                    ],
                    [
                        'fname' => $extra_guest['fname'],
                        'lname' => $extra_guest['lname'],
                        'email' => $extra_guest['email'],
                        'title' => $extra_guest['title'],
                        'birthdate' => $extra_guest['birthdate_year'].'-'.$extra_guest['birthdate_month'].'-'.$extra_guest['birthdate_day'],
                    ]
                );
                $emails->push(strtolower($extra_guest['email']));

                $booking_guest_extra = $booking->guests()->create([
                    'guest_id' => $new_extra_guest->id,
                    'booking_id' => $booking->id,
                    'group_id' => $booking_guest->id,
                ]);

                $booking_room_extra = $booking->rooms()->create([
                    'booking_id' => $booking->id,
                    'room_id' => $booking_room['room_id'],
                    'subroom_id' => $availability['available_rooms'][$index]['id'],
                    'bed' => $availability['available_rooms'][$index]['bed'],
                    'bed_type' => $booking_room['bed_type'],
                    'bathroom' => 'Shared',
                    'from' => $check_in,
                    'to' => $check_out,
                    'is_private' => $booking_room['private_booking'],
                    'guest' => 1,
                    'price' => (floatVal($booking_room['accommodation_price']) + floatVal($booking_room['duration_discount'])) / intval($booking_room['guest']),
                    'duration_discount' => floatVal($booking_room['duration_discount']) / intval($booking_room['guest']),
                ]);

                array_push($booking_room_group, $booking_room_extra->id);

                BookingRoomGuest::create([
                    'booking_room_id' => $booking_room_extra->id,
                    'booking_guest_id' => $booking_guest_extra->id,
                ]);
            }
        }

        // add ons
        $addons = $booking_room['addons']->toArray();

        if (count($addons) > 0) {
            $questionnaire_answers = request('questionnaire_answers');

            foreach ($addons as $key =>  $addon) {
                $guest_number = intval($addon['guests']);
                $amount_number = intval($addon['amount']);

                if (isset($addon['questionnaire']) && !is_null($addon['questionnaire_id'])) {
                    $addon['questionnaire']['name'] = Questionnaire::find($addon['questionnaire_id'])->name;
                }

                for ($i = 0; $i < $guest_number; ++$i) {
                    $info = isset($addon['weeks']) && $addon['weeks'] != '' ? ('Starts in '. (new \NumberFormatter('en_US', \NumberFormatter::ORDINAL))->format(intVal($addon['weeks'])) .' week') : null;
                    if ($questionnaire_answers) {
                        $addon['questionnaire']['answers'][$i] = $questionnaire_answers[$addon['id']][$i] ?? null;
                    }

                    BookingAddon::create([
                        'booking_room_id' => $booking_room_group[$i] ?? $booking_room_new->id,
                        'extra_id' => $addon['id'],
                        'guests' => 1,
                        'amount' => $amount_number,
                        'price' => $addon['price'],
                        'transaction_id' => null,
                        'stock_id' => null,
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                        'info' => $info,
                        'questionnaire_answers' => $questionnaire_answers ? ($questionnaire_answers[$addon['id']][$i] ?? null) : null,
                    ]);
                }

                $addons[$key] = $addon;
            }
        }

        $booking_room['addons'] = collect($addons);
        session(['room' => $booking_room]);

        // transfers
        $transfers = $booking_room['transfers'];

        if (count($transfers) > 0) {
            foreach ($transfers as $transfer) {
                if ('Airport Pickup' == $transfer['name']) {
                    $flight_number = !request()->has('skip_transfer') ? request('arrival_flight') : 'TBA';
                    $flight_time = !request()->has('skip_transfer') ? $check_in.' '.request('arrival_time_h').':'.request('arrival_time_m').':00' : $booking->check_in->format('Y-m-d').' 02:00:00';
                } else {
                    $flight_number = !request()->has('skip_transfer') ? request('departure_flight') : 'TBA';
                    $flight_time = !request()->has('skip_transfer') ? $check_out.' '.request('departure_time_h').':'.request('departure_time_m').':00' : $booking->check_out->format('Y-m-d').' 02:00:00';
                }
                BookingTransfer::create([
                    'booking_id' => $booking->id,
                    'transfer_extra_id' => $transfer['id'],
                    'flight_number' => $flight_number,
                    'flight_time' => $flight_time,
                    'guests' => $transfer['guests'],
                    'price' => $transfer['price'],
                ]);
            }
        }

        // check blacklist code here
        // if email is blacklisted, update the booking is_blacklisted to 1
        // so at the next step the booking status will be set to pending
        $blacklisted = Blacklist::where('email', request('email'))->first();
        if ($blacklisted) {
            $booking->update([
                'is_blacklisted' => 1
            ]);
        }

        if (!is_null($booking_room['special_offer'])) {
            $booking->discounts()->create([
                'name' => 'Special Offer: '.$booking_room['special_offer']['offer'].' '.$booking_room['special_offer']['type'],
                'category' => 'special_offer',
                'type' => 'Percent',
                'apply_to' => 'ROOM',
                'value' => floatval($booking_room['special_offer']['value']),
            ]);
        }

        DB::commit();

        $this->paymentService->createPayment($booking);

        $booking->refresh();

        $deposit_due = $booking->location->deposit_due;
        $deposit_date = Carbon::now()->addDays($deposit_due);

        $booking->payment->update([
            'status' => 'DUE',
            'deposit_due_date' => $deposit_date,
            'processing_fee' => $booking->processing_fee,
        ]);

        session(['booking_ref' => $booking->ref]);

        return redirect('book-now/confirm'.$ga);
    }

    /**
     * CONFIRM BOOKING.
     */
    public function confirmBooking()
    {
        $step = 5;

        $guest = session('guest');
        $ref = session('booking_ref');
        $ga = request()->has('_ga') ? '?_ga='.request('_ga') : null;
        $ga = is_null($ga) && request()->has('_gl') ? '?_gl='.request('_gl') : $ga;
        $room = session('room');
        $documents = Document::orderBy('sort', 'asc')->get();

        if (!session()->has('location') || !session()->has('room')) {
            return redirect('book-now');
        }

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $check_in = $date_start->format('d.m.Y');
        $check_out = $date_end->format('d.m.Y');

        $booking = Booking::with(['rooms.addons', 'transfers', 'guest', 'other_guests', 'rooms', 'location'])->where('ref', $ref)->first();
        $booking_room = session('room');

        if (!$booking) {
            return redirect('book-now');
        }

        $duration = intval($dates['duration']);

        $total_addon = $this->bookingService->getTotalAddons($booking_room);

        $total_transfer = $booking_room['transfers']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $addons = $booking_room['addons'];
        $transfers = $booking_room['transfers'];

        $location = Location::find(session('location'));
        $state = $booking->opportunity == 'Sale' ? 'CONFIRMED' : 'PENDING';

        $tax_info = $this->bookingService->displayTaxInfo($booking_room, $booking->location);

        $tax = TaxService::getActiveTaxes();

        $path = public_path('/tenancy/assets/images/rooms/'.$room['room_id']);

        $room['gallery'] = @file_exists($path)
            ? collect(File::files($path))->map(fn ($file) => $file->getFilename())->filter(fn ($file) => $file != $room['featured_image'])
            : [];

        return view('Booking.booking', compact(
            'tax_info',
            'documents',
            'step',
            'booking_room',
            'default_check_in',
            'default_check_out',
            'addons',
            'duration',
            'total_addon',
            'room',
            'check_in',
            'check_out',
            'guest',
            'total_transfer',
            'booking',
            'transfers',
            'location',
            'ga',
            'state',
            'tax'
        ));
    }

    /**
     * REDIRECT TO STEP 5 BOOKING.
     */
    public function emailLinkRedirect($hashid)
    {
        $id = Hashids::decode($hashid);
        $booking = Booking::with(['rooms', 'guest', 'other_guests', 'location'])->where('id',$id)->first();

        if (!$booking) {
            Log::info('Tried to access a booking with an invalid hashid: '.$hashid);
            return redirect('book-now');
        }

        if ($booking->status != 'DRAFT') {
            return redirect('book-now');
        }

        $date_start = new Carbon($booking->check_in);
        $date_end = new Carbon($booking->check_out);

        $dates = [
            'start' => $booking->check_in,
            'end' => $booking->check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $room = Room::find($booking->rooms[0]->room_id);
        $bed_type = $booking->rooms[0]->bed_type;
        $room_type = $room->room_type;
        $guest = count($booking->other_guests) + 1;

        $private_booking = boolval($booking->rooms[0]->is_private);
        $this->addRoom($room->id, $dates, $booking->check_in, $booking->check_out, $private_booking, $bed_type, $room_type, $guest);

        session([
            'booking_date_start' => $booking->check_in,
            'booking_date_end' => $booking->check_out,
            'guest' => $guest,
            'booking_ref' => $booking->ref,
            'location' => $booking->location_id
        ]);

        return redirect('book-now/confirm');
    }

    public function refreshConfirmBooking()
    {
        return redirect('book-now');
    }

    /**
     * PROCESS CONFIRM BOOKING.
     */
    public function processConfirmBooking()
    {
        $ref = session('booking_ref');
        $booking_room = session('room');
        //$booking_status = 3 == $booking_room['availability_status'] ? 'PENDING' : 'CONFIRMED';
        $booking_status = $booking_room['is_available'] ? 'CONFIRMED' : 'PENDING';
        $guest_name = session('booking_guest');
        $location = session('location');
        $ga = request()->has('_ga') ? '&_ga='.request('_ga') : null;
        $ga = is_null($ga) && request()->has('_gl') ? '?_gl='.request('_gl') : $ga;
        $documents = Document::orderBy('sort', 'asc')->get();
        $profile = Profile::where('tenant_id', tenant('id'))->first();

        if (!session()->has('booking_ref')) {
            return redirect('book-now');
        }

        $booking = Booking::with(['payment', 'guests'])->where('ref', $ref)->first();

        $subroom_id = $booking->rooms()->first()->subroom_id;
        $subroom = RoomInfo::with(['room'])->find($subroom_id);

        $booking->update([
            'notes' => $booking_room['comment']
        ]);

        if ('ABANDONED' == $booking->status) {
            // booking already abandoned
            session()->flush();

            return response('Oops! Your session has expired. Please <a href="/book-now" title="">click here</a> to book.');
        }

        if ($subroom->room->availability != 'auto') {
            $booking_status = $subroom->room->availability == 'pending' ? 'PENDING' : 'CONFIRMED';
        }

        if ($booking->is_blacklisted) {
            $booking->update(['status' => 'PENDING', 'opportunity' => 'Pending']);
            $booking->refresh();
            $booking_status = 'PENDING';
        } else {
            $booking->update([
                'status' => $booking_status,
            ]);
        }

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $duration = intval($dates['duration']);

        $total_addon = $booking_room['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $total_transfer = $booking_room['transfers']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $addons = $booking_room['addons'];
        $transfers = $booking_room['transfers'];

        $step = 6;

        $location = Location::find(session('location'));

        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => null,
            'action' => 'Confirmed booking',
            'info_type' => 'info',
            'details' => '<b>'.$guest_name.'</b> confirmed booking #<b>'.$booking->ref.'</b>',
            'ip_address' => request()->ip(),
        ]);

        // send confirmation email
        if ($booking->status == 'CONFIRMED') {
            // confirmed email
            if ($this->bookingService->sendConfirmationEmail($booking)) {
                // redirect to payment page
                session()->flush();
                return redirect()->route('tenant.payment.show', ['id' => $booking->payment->link]);
            }

            try {
                AutomatedEmailService::checkAndSendEmailWhenBookingIsApproved($booking);
            } catch (\Exception $e) {
                Log::error('Cannot send auto email when booking is approved', $e->getMessage());
            }
        } else {
            // pending email
            $this->bookingService->sendPendingEmail($booking);
        }

        session()->flush();

        $tax_info = $this->bookingService->displayTaxInfo($booking_room, $booking->location);

        $tax = TaxService::getActiveTaxes();

        return view('Booking.booking', compact(
            'tax_info',
            'documents',
            'ref',
            'ga',
            'booking',
            'booking_status',
            'step',
            'default_check_in',
            'default_check_out',
            'addons',
            'transfers',
            'duration',
            'booking_room',
            'total_addon',
            'total_transfer',
            'location',
            'profile',
            'tax'
        ));
    }

    /**
     * THANK YOU PAGE.
     */
    public function thankyou()
    {
        $pending = session('pending');
        $payment_link = session('payment_link');

        session()->flush();

        return view('Booking.payments.thank-you', compact('pending', 'payment_link'));
    }

    /**
     * COMPLETED PAYMENT PAGE.
     */
    public function completed()
    {
        return view('Booking.payment.booking-finished');
    }

    /**
     * CONVERT MONTH TO ROMAN CHARACTER.
     *
     * @param string $mo
     *
     * @return string
     */
    public function convertMonthToRoman($mo)
    {
        $symbols = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return $symbols[$mo - 1];
    }

    protected function addNormalTransfer()
    {
        $room = session('room');
        $id = request('id');
        $guest = request('guest');

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $transfer = TransferExtra::with(['prices'])->find($id);
        $transfer_data = [];

        if ($transfer->is_complimentary && $dates['duration'] >= $transfer->complimentary_min_nights) {
            $transfer_price = 0;
            $transfer_data = [
                'id' => $transfer->id,
                'name' => $transfer->name,
                'guests' => $guest,
                'price' => 0,
                'total' => 0,
            ];
            $room['transfers']->push($transfer_data);
        } else {
            $transfer_price = $transfer->prices->where('guest', $guest)->first()->price;
            $transfer_data = [
                'id' => $transfer->id,
                'name' => $transfer->name,
                'guests' => $guest,
                'price' => floatval($transfer_price),
                'total' => floatval($transfer_price),
            ];
            $room['transfers']->push($transfer_data);
        }

        session(['room' => $room]);

        $grand_total = $this->recalculateGrandTotal();

        return [
            'status' => 'OK',
            'id' => $transfer->id,
            'name' => $transfer->name,
            'guest' => $guest.' '.Str::plural('guest', $guest),
            'price' => floatval($transfer_price),
            'total' => floatval($transfer_price) > 0 ? '&euro;'.number_format($transfer_price, 2) : 'FREE',
            'grand_total' => '&euro;'.number_format($grand_total, 2),
        ];
    }

    protected function addNormalAddon()
    {
        $room = session('room');
        $id = request('id');
        $amount = request('duration');
        $guest = intval(request('guest'));
        $weeks = intval(request('weeks'));

        $date_start = session()->has('booking_date_start') ? new Carbon(session('booking_date_start')) : new Carbon(date('Y-m-d'));
        $date_end = session()->has('booking_date_end') ? new Carbon(session('booking_date_end')) : new Carbon(date('Y-m-d'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $addon = Extra::find($id);
        $addon_total = $this->bookingService->calculateAddon($addon, $amount, $guest);

        if ($addon->rate_type == 'Fixed') {
            $addon_price = $addon->base_price;
            $amount = 1;
        } else {
            $addon_price = $addon->base_price * $amount;
        }

        $room['addons']->push([
            'id' => $addon->id,
            'name' => $addon->name,
            'unit_name' => $addon->unit_name,
            'guests' => $guest,
            'amount' => $amount,
            'price' => floatval($addon_price),
            'total' => floatval($addon_total),
            'weeks' => $weeks,
            'questionnaire_id' => $addon->questionnaire_id,
        ]);

        session(['room' => $room]);

        if ('Day' == $addon->rate_type) {
            $duration_text = $amount > 1 ? ', '.$amount.' '.Str::plural('day', $amount) : '';
        } else {
            $duration_text = '';
        }

        $weeks_text = $weeks != '' ? '. Starts in '. (new \NumberFormatter('en_US', \NumberFormatter::ORDINAL))->format(intVal($weeks)) .' week' : '';
        $weeks_text = $weeks != '' ? '. Starts in '. (new \NumberFormatter('en_US', \NumberFormatter::ORDINAL))->format(intVal($weeks)) .' week' : '';

        $grand_total = $this->recalculateGrandTotal();

        $location = Location::findOrFail($room['location_id']);

        $tax_info = $this->bookingService->displayTaxInfo($room, $location);

        return [
            'status' => 'OK',
            'id' => $addon->id,
            'name' => $addon->name,
            'unit' => $guest.' '.Str::plural($addon->unit_name, $guest) . $weeks_text,
            'unit_name' => $addon->unit_name,
            'duration' => $duration_text,
            'price' => $addon_price,
            'total' => '&euro;'.number_format($addon_total, 2),
            'grand_total' => '&euro;'.number_format($grand_total, 2),
            'tax_info' => $tax_info,
        ];
    }

    protected function addSPAddon()
    {
        $slug = request('spSlug');
        $sp_duration = request()->has('spDuration') ? request('spDuration') : 0;
        $guest = intval(request('guest'));
        $id = request('id');
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->firstOrFail();
        $room = $package->room;

        $date_start = session()->has('sp_date_start') ? new Carbon(session('sp_date_start')) : new Carbon(date('Y-m-d'));
        $date_end = session()->has('sp_date_end') ? new Carbon(session('sp_date_end')) : new Carbon(date('Y-m-d'));

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        $addon = Extra::find($id);
        $addon_data = [];
        $addon_total = $this->bookingService->calculateAddon($addon, $sp_duration, $guest);

        if (!$addon->is_flexible && 'Fixed' == $addon->rate_type) {
            $addon_price = $addon->base_price;
            session()->push('sp_extras', [
                'id' => $addon->id,
                'name' => $addon->name,
                'unit_name' => $addon->unit_name,
                'guests' => $guest,
                'amount' => 1,
                'price' => floatval($addon_price),
                'total' => floatval($addon_total),
            ]);
        } else {
            $addon_price = 14 == $addon->id ? ($addon_total / $guest) : ($addon->base_price * $sp_duration);
            session()->push('sp_extras', [
                'id' => $addon->id,
                'name' => $addon->name,
                'unit_name' => $addon->unit_name,
                'guests' => $guest,
                'amount' => $sp_duration,
                'price' => floatval($addon_price),
                'total' => floatval($addon_total),
            ]);
        }

        if ('Day' == $addon->rate_type) {
            $duration_text = $sp_duration > 1 ? ', '.$sp_duration.' '.Str::plural('day', $sp_duration) : '';
        } else {
            $duration_text = '';
        }

        $grand_total = $this->recalculateSPGrandTotal();

        $location = Location::findOrFail($room->location_id);

        $tax_info = $this->bookingService->displayTaxInfo($room, $location);

        return [
            'status' => 'OK',
            'id' => $addon->id,
            'name' => $addon->name,
            'unit' => $guest.' '.Str::plural($addon->unit_name, $guest),
            'unit_name' => $addon->unit_name,
            'guest' => $guest.' '.Str::plural('guest', $guest),
            'duration' => $duration_text,
            'price' => $addon_price,
            'total' => '&euro;'.number_format($addon_total, 2),
            'grand_total' => '&euro;'.number_format($grand_total, 2),
            'tax_info' => $tax_info,
        ];
    }
}
