<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Http\Requests\Booking\GuestDetails;
use App\Models\Booking\Blacklist;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingRoomGuest;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\Document;
use App\Models\Booking\Extra;
use App\Models\Booking\Guest;
use App\Models\Booking\Location;
use App\Models\Booking\PricingCalendar;
use App\Models\Booking\Profile;
use App\Models\Booking\Room;
use App\Models\Booking\RoomInfo;
use App\Models\Booking\SpecialPackage;
use App\Models\Booking\SpecialPackageAddon;
use App\Models\Booking\TransferExtra;
use App\Services\Booking\BookingService;
use App\Services\Booking\PaymentService;
use App\Services\Booking\RoomService;
use App\Services\Booking\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpecialPackageController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $roomService;
    protected $paymentService;
    protected $homepage;

    public function __construct(BookingService $bookingService, UserService $userService, RoomService $roomService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->roomService = $roomService;
        $this->paymentService = $paymentService;
        $this->homepage = 'https://traverise.com';
    }

    /**
     * SPECIAL PACKAGE INDEX PAGE.
     *
     * @param int $id
     *
     * @return array
     */
    public function index()
    {
        $packages = SpecialPackage::with(['room', 'location', 'addons'])->get();

        return view('Booking.special_packages.index', compact('packages'));
    }

    /**
     * SPECIAL PACKAGE INDEX PAGE API.
     *
     * @param int $id
     *
     * @return array
     */
    public function indexAPI()
    {
        $packages = Cache::remember('special_packages', 10, function () {
            return SpecialPackage::with(['room', 'location', 'addons.details'])->get()->map(function ($package, $index) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'slug' => $package->slug,
                    'description' => $package->description,
                    'nights' => $package->nights,
                    'check_in' => $package->check_in->format('Y-m-d'),
                    'check_out' => $package->check_out->format('Y-m-d'),
                    'price' => $package->price,
                    'airport_pickup' => $package->airport_pickup,
                    'airport_dropoff' => $package->airport_dropoff,
                    'camp' => [
                        'id' => $package->location_id,
                        'name' => $package->location->name,
                    ],
                    'room' => [
                        'id' => $package->room_id,
                        'name' => $package->room->name,
                    ],
                    'addons' => count($package->addons) > 0 ? collect($package->addons)->map(function ($addon, $key) {
                        return [
                            'name' => $addon->details->name,
                            'qty' => $addon->qty,
                        ];
                    }) : null,
                ];
            });
        });

        return response($packages);
    }

    /**
     * SPECIAL PACKAGE SHOW PAGE.
     *
     * @param int $id
     *
     * @return array
     */
    public function show($id)
    {
        $package = SpecialPackage::with(['room', 'location', 'addons'])->find($id);

        $addons = [];
        if ($package->addons) {
            foreach ($package->addons as $addon) {
                $addons[$addon->extra_id] = $addon->qty;
            }
        }

        $locations = Location::with(['rooms'])->get();

        $room_ids = Room::with(['location'])
            ->get()
            ->keyBy('id')
            ->keys()
            ->toArray();

        $extras = Extra::whereHas('rooms', function ($room) use ($room_ids) {
            $room->whereIn('room_id', $room_ids);
        })
            ->where('active', 1)
            ->get([
                'id', 'name', 'description', 'base_price', 'is_flexible'
            ]);

        return view('Booking.special_packages.show', compact('package', 'id', 'addons', 'extras', 'locations'));
    }

    /**
     * SPECIAL PACKAGE NEW PAGE.
     *
     * @return View
     */
    public function create()
    {
        $locations = Location::with(['rooms' => function ($q) {
            $q->where('active', 1);
        }])->get();

        $room_ids = Room::with(['location'])
            ->get()
            ->keyBy('id')
            ->keys()
            ->toArray();

        $extras = Extra::whereHas('rooms', function ($room) use ($room_ids) {
            $room->whereIn('room_id', $room_ids);
        })
            ->where('active', 1)
            ->get(['id', 'name', 'description', 'base_price', 'is_flexible']);

        return view('Booking.special_packages.new', compact('extras', 'locations'));
    }

    /**
     * SPECIAL PACKAGE INSERT.
     *
     * @param object $request
     *
     * @return Redirect
     */
    public function insert(Request $request)
    {
        $room = Room::with(['location'])->find($request->room_id);

        $dates = explode(' - ', $request->dates);

        $check_in = date('Y-m-d', strtotime($dates[0]));
        $check_out = date('Y-m-d', strtotime($dates[1]));

        $package = SpecialPackage::create($request->only([
            'name',
            'room_id',
            'price',
            'nights',
            'min_guest',
            'description',
            'inclusions',
        ]));

        $package->update([
            'airport_pickup' => $request->has('airport_pickup'),
            'airport_dropoff' => $request->has('airport_dropoff'),
            'location_id' => $room->location->id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'slug' => Str::slug($room->location->name.'-'.$package->name),
        ]);

        if (request()->has('addons')) {
            $addons = collect($request->addons)->keys()->toArray();
            $addons_qty = $request->addons_qty;

            foreach ($addons as $addon_id) {
                SpecialPackageAddon::create([
                    'special_package_id' => $package->id,
                    'extra_id' => $addon_id,
                    'qty' => isset($addons_qty[$addon_id]) ? $addons_qty[$addon_id] : 1,
                ]);
            }
        }

        session()->flash('messages', 'Package updated');

        return redirect('special-packages/'.$package->id);
    }

    /**
     * SPECIAL PACKAGE UPDATE.
     *
     * @param int    $id
     * @param object $request
     *
     * @return array
     */
    public function update($id, Request $request)
    {
        $package = SpecialPackage::with(['room', 'location', 'addons'])->find($id);

        $room = Room::with(['location'])->find($request->room_id);

        $dates = explode(' - ', $request->dates);

        $check_in = date('Y-m-d', strtotime($dates[0]));
        $check_out = date('Y-m-d', strtotime($dates[1]));

        $package->update($request->only([
            'name',
            'room_id',
            'price',
            'nights',
            'min_guest',
            'max_guest',
            'description',
            'inclusions',
        ]));

        $package->update([
            'airport_pickup' => $request->has('airport_pickup') ? 1 : 0,
            'airport_dropoff' => $request->has('airport_dropoff') ? 1 : 0,
            'location_id' => $room->location->id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'slug' => Str::slug($request->slug),
            'excluded_addons' => json_encode($request->hide),
        ]);

        $addons = collect($request->addons)->keys()->toArray();
        $addons_qty = $request->addons_qty;
        $current_addons = json_decode($request->current_addons, true);

        // delete addons first
        $sp = SpecialPackageAddon::where('special_package_id', $id)->whereNotIn('extra_id', $addons)->delete();

        foreach ($addons as $addon_id) {
            SpecialPackageAddon::firstOrCreate([
                'special_package_id' => $id,
                'extra_id' => $addon_id,
            ], [
                'qty' => $addons_qty[$addon_id],
            ]);
        }

        session()->flash('messages', 'Package updated');

        return redirect('special-packages/'.$id);
    }

    /**
     * SPECIAL PACKAGE DELETE.
     *
     * @param int $id
     *
     * @return Redirect
     */
    public function delete($id)
    {
        $package = SpecialPackage::with(['addons'])->find($id);

        $package->addons()->delete();

        $package->delete();

        session()->flash('messages', $package->name.' Package removed');

        return redirect('special-packages');
    }

    /**
     * BOOK PACKAGE PAGE.
     *
     * @param object $request
     * @param mixed  $slug
     *
     * @return View
     */
    public function book($slug, Request $request)
    {
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->firstOrFail();

        $guest = session()->has('sp_guest') ? session('sp_guest') : $package->min_guest;

        $step = 1;

        if (!$package) {
            // redirect back to homepage
        }

        $room = $package->room;

        if ($request->isMethod('post')) {
            $date = request('date');
            $guest = request('guest');
            $start = Carbon::createFromFormat('d M Y', $date);
            $end = Carbon::createFromFormat('d M Y', $date)->addDays($package->nights);
            $default_date_start = $start->format('Y-m-d');
            $default_date_end = $end->format('Y-m-d');
            $default_check_in = $start->format('d M Y');
            $default_check_out = $end->format('d M Y');
        } else {
            $is_today_default = $this->isTodayGreaterThanMinimumPackageCheckInDate($package->check_in, Carbon::now());
            $default_date_start = !$is_today_default ? $package->check_in->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $default_date_end = !$is_today_default ? $package->check_in->addDays($package->nights)->format('Y-m-d') : Carbon::now()->addDays($package->nights)->format('Y-m-d');
            $default_check_in = !$is_today_default ? $package->check_in->format('d M Y') : Carbon::now()->format('d M Y');
            $default_check_out = !$is_today_default ? $package->check_in->addDays($package->nights)->format('d M Y') : Carbon::now()->addDays($package->nights)->format('d M Y');
        }

        // check complimentary add-ons ?

        $sp_extras = session()->has('sp_extras') ? session('sp_extras') : collect([]);
        $sp_transfers = session()->has('sp_transfers') ? session('sp_transfers') : collect([]);
        $sp_comment = session()->has('sp_comment') ? session('sp_comment') : '';

        //$season = strtolower($this->getSeason($default_date_start, $package->room));
        //$sp_price = 'price_'. $season;
        $season = 'low';
        $sp_price = 'price_low';

        $date_start = new Carbon($default_date_start);
        $date_end = new Carbon($default_date_end);

        $dates = [
            'start' => $default_check_in,
            'end' => $default_check_out,
            'duration' => $date_start->diffInDays($date_end),
        ];

        session([
            'sp_date_start' => $default_date_start,
            'sp_date_end' => $default_date_end,
            'sp_check_in' => $default_check_in,
            'sp_check_out' => $default_check_out,
            'sp_season' => $season,
            //'sp_price' => $package->$sp_price,
            'sp_price' => $package->price,
            'sp_guest' => $guest,
            'sp_inclusion' => $package->inclusions_formatted,
            'sp_extras' => $sp_extras,
            'sp_duration' => $dates['duration'],
            'sp_transfers' => $sp_transfers,
            'sp_slug' => $slug,
            'sp_comment' => $sp_comment,
        ]);

        $duration = intval($dates['duration']);

        //$price = $package->$sp_price;
        $price = $package->price;

        $extras = $sp_extras;

        $pickup = null;
        $dropoff = null;

        $addons = Extra::whereHas('rooms', function ($q) use ($package) {
            $q->where('room_id', $package->room_id);
        })
            ->with(['prices'])
            ->where('active', 1)
            ->where('admin_only', 0)
            ->orderBy('sort', 'desc')
            ->get(['id', 'description', 'name', 'rate_type', 'base_price', 'is_flexible', 'min_stay', 'min_guests', 'max_guests', 'min_units', 'max_units', 'sort'])
            ->map(function ($item, $key) use ($dates) {
                $item['total'] = $this->bookingService->calculateAddon($item, $dates['duration'], 1);

                return $item;
            });

        if ($package->excluded_addons) {
            $excluded = collect(json_decode($package->excluded_addons));

            $addons = $addons->filter(function ($addon) use ($excluded) {
                return !$excluded->has($addon->id);
            });
        }

        $transfers = TransferExtra::with(['prices'])
            ->whereHas('rooms', function ($q) use ($package) {
                $q->where('room_id', $package->room_id);
            })
            ->where('is_active', 1)
            ->get()
            ->map(function ($item, $key) use ($dates) {
                $item['price'] = $item->is_complimentary && ($dates['duration'] >= $item->complimentary_min_nights) ? 0 : floatval($item->prices->where('guest', 1)->first()->price);

                return $item;
            });

        $inclusion = session('sp_inclusion');

        $grand_total = $this->bookingService->recalculateSPGrandTotal();

        $documents = Document::orderBy('sort', 'asc')->get();

        $terms = $documents->where('position', 'terms-and-conditions')->sortBy('sort')->all();

        return view('Booking.book-package', compact('extras', 'transfers', 'grand_total', 'package', 'sp_transfers', 'sp_extras', 'duration', 'step', 'addons', 'default_check_in', 'default_check_out', 'guest', 'room', 'pickup', 'dropoff', 'slug', 'price', 'inclusion', 'terms', 'documents'));
    }

    /**
     * GUEST DETAILS PAGE.
     *
     * @param object $request
     * @param mixed  $slug
     *
     * @return View
     */
    public function guestDetails($slug, Request $request)
    {
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->firstOrFail();

        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        $guest = session()->has('sp_guest') ? session('sp_guest') : $package->min_guest;

        $documents = Document::orderBy('sort', 'asc')->get();

        $terms = $documents->where('position', 'terms-and-conditions')->sortBy('sort')->all();

        $step = 2;

        $room = $package->room;

        $pickup = null;
        $dropoff = null;

        if ($package->airport_pickup) {
            $pickup_id = 1;
            $pickup = TransferExtra::find($pickup_id);
        }

        if ($package->airport_dropoff) {
            $dropoff_id = 2;
            $dropoff = TransferExtra::find($dropoff_id);
        }

        $default_check_in = session('sp_check_in');
        $default_check_out = session('sp_check_out');
        $extras = session('sp_extras');
        $sp_transfers = session('sp_transfers');
        $inclusion = session('sp_inclusion');

        $season = 'low';
        $sp_price = 'price_low';

        $price = $package->price;

        $grand_total = $this->bookingService->recalculateSPGrandTotal();

        return view('Booking.book-package', compact('default_check_in', 'default_check_out', 'extras', 'sp_transfers', 'inclusion', 'grand_total', 'package', 'step', 'countries', 'guest', 'room', 'slug', 'price', 'pickup', 'dropoff', 'documents', 'terms'));
    }

    /**
     * UPDATE DATES AND GUEST
     * koko dodol.
     */
    public function updateDatesAndGuest(Request $request)
    {
        $date = request('date');
        $guest = request('guest');
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->find($request->package_id);

        $start = Carbon::createFromFormat('d M Y', $date);
        $end = Carbon::createFromFormat('d M Y', $date)->addDays($package->nights);

        session([
            'sp_date_start' => $start->format('Y-m-d'),
            'sp_date_end' => $end->format('Y-m-d'),
            'sp_check_in' => $start->format('d M Y'),
            'sp_check_out' => $end->format('d M Y'),
            'sp_guest' => $guest,
        ]);

        return redirect("book-package/{$package->slug}");
    }

    /**
     * SAVE GUEST DETAILS.
     *
     * @param mixed $slug
     */
    public function saveGuestDetails($slug, Request $request)
    {
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->first();
        $check_in = session('sp_date_start');
        $check_out = session('sp_date_end');
        $guest = intval(session('sp_guest'));
        $price = session('sp_price');
        $emails = collect([]);
        $extras = session('sp_extras');
        $sp_transfers = session('sp_transfers');

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

        $location_data = Location::find($package->location_id);

        // first, check room availability
        $rooms = Room::with(['rooms', 'prices', 'progressive_prices'])
            ->where('id', intval($package->room_id))
            ->orderBy('sort', 'asc')
            ->get()
        ;

        $date_start = new Carbon($check_in);
        $date_end = new Carbon($check_out);

        $default_check_in = $date_start->format('d M Y');
        $default_check_out = $date_end->format('d M Y');

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
        $occupancy = $this->roomService->getRoomOccupancy($check_in, $check_out, $rooms, $guest);

        $result = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, false, $guest);

        $availability = $this->roomService->getAvailabilityList($result, $guest);

        $availability = $availability[$package->room_id];

        DB::beginTransaction();

        // first : create guests
        $new_guest = Guest::updateOrCreate(
            [
                'email' => request('email'),
            ],
            [
                'fname' => request('fname'),
                'lname' => request('lname'),
                'company' => request('company'),
                'phone' => request('phone'),
                'street' => request('street'),
                'zip' => request('zip'),
                'city' => request('city'),
                'country' => request('country'),
                'birthdate' => request('birthdate_year').'-'.request('birthdate_month').'-'.request('birthdate_day'),
                'marketing_flag' => request()->has('marketing') ? 1 : 0,
            ]
        );

        $emails->push(request('email'));

        // second, create the booking
        $ref = $this->bookingService->generateBookingRef();
        $location_id = $package->location_id;

        $opportunity = 1 == $availability['availability_status'] ? 'Sale' : 'Pending';

        $booking = Booking::create([
            'ref' => $ref,
            'special_package_id' => $package->id,
            'source_type' => 'Guest',
            'channel' => 'Online',
            'opportunity' => $opportunity,
            'source_id' => null,
            'location_id' => $location_id,
            'expiry' => Carbon::now()->addHours(24),
            'deposit_expiry' => $default_expiry_date,
            'status' => 'RESERVED',
            'check_in' => $check_in,
            'check_out' => $check_out,
            'agent_id' => null,
            'voucher' => null,
            'origin' => request()->getHost(),
            'notes' => session('sp_comment'),
        ]);

        // history
        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => null,
            'action' => 'Reserved booking',
            'info_type' => 'info',
            'details' => '<b>'.$new_guest->full_name.'</b> reserved booking #<b>'.$booking->ref.'</b>',
            'ip_address' => request()->ip(),
        ]);

        $this->paymentService->createPayment($booking);

        $booking->refresh();
        $booking->payment->refresh();

        $deposit_due = $booking->location->deposit_due;
        $deposit_date = Carbon::now()->addDays($deposit_due);

        $booking->payment->update([
            'status' => 'DUE',
            'deposit_due_date' => $deposit_date,
        ]);

        // create booking guest
        $booking_guest = $booking->guest()->create([
            'guest_id' => $new_guest->id,
            'booking_id' => $booking->id,
            'group_id' => 0,
        ]);

        // create booking room
        $booking_room_new = $booking->rooms()->create([
            'booking_id' => $booking->id,
            'room_id' => $package->room_id,
            'subroom_id' => $availability['available_rooms'][0]['id'],
            'bed' => $availability['available_rooms'][0]['bed'],
            'bed_type' => $package->room->beds[0],
            'bathroom' => 'Shared',
            'from' => $check_in,
            'to' => $check_out,
            'is_private' => 0,
            'guest' => 1,
            'price' => $price,
            'duration_discount' => 0,
        ]);

        // create booking guest room
        $br_guest = BookingRoomGuest::create([
            'booking_room_id' => $booking_room_new->id,
            'booking_guest_id' => $booking_guest->id,
        ]);

        $booking_room_group = [$booking_room_new->id];

        if ($guest > 1) {
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
                        'birthdate' => $extra_guest['birthdate_year'].'-'.$extra_guest['birthdate_month'].'-'.$extra_guest['birthdate_day'],
                    ]
                );
                $emails->push($extra_guest['email']);

                $booking_guest_extra = $booking->guests()->create([
                    'guest_id' => $new_extra_guest->id,
                    'booking_id' => $booking->id,
                    'group_id' => $booking_guest->id,
                ]);

                $booking_room_extra = $booking->rooms()->create([
                    'booking_id' => $booking->id,
                    'room_id' => $package->room_id,
                    'subroom_id' => $availability['available_rooms'][$index]['id'],
                    'bed' => $availability['available_rooms'][$index]['bed'],
                    'bed_type' => $package->room->beds[0],
                    'bathroom' => 'Shared',
                    'from' => $check_in,
                    'to' => $check_out,
                    'is_private' => 0,
                    'guest' => 1,
                    'price' => $package->price,
                    'duration_discount' => 0,
                ]);

                array_push($booking_room_group, $booking_room_extra->id);

                BookingRoomGuest::create([
                    'booking_room_id' => $booking_room_extra->id,
                    'booking_guest_id' => $booking_guest_extra->id,
                ]);
            }
        }

        // add ons
        $addons = $package->addons;

        if (count($addons) > 0) {
            foreach ($addons as $addon) {
                for ($i = 0; $i < $guest; ++$i) {
                    BookingAddon::create([
                        'booking_room_id' => $booking_room_group[$i],
                        'extra_id' => $addon->extra_id,
                        'guests' => $guest,
                        'amount' => intval($addon->qty),
                        'price' => 0,
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                    ]);
                }
            }
        }

        // extras
        if (count($extras) > 0) {
            foreach ($extras as $extra) {
                for ($i = 0; $i < $guest; ++$i) {
                    BookingAddon::create([
                        'booking_room_id' => $booking_room_group[$i],
                        'extra_id' => $extra['id'],
                        'guests' => $extra['guests'],
                        'amount' => intval($extra['amount']),
                        'price' => $extra['price'],
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                    ]);
                }
            }
        }

        if (count($sp_transfers) > 0) {
            foreach ($sp_transfers as $transfer) {
                if ('Airport Pickup' == $transfer['name'] || 'Airport shuttle collection' == $transfer['name']) {
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

        // transfers
        if ($package->airport_pickup) {
            $arr_flight_number = !request()->has('skip_transfer') ? request('arrival_flight') : 'TBA';
            $arr_flight_time = !request()->has('skip_transfer') ? $check_in.' '.request('arrival_time_h').':'.request('arrival_time_m').':00' : null;

            BookingTransfer::create([
                'booking_id' => $booking->id,
                'transfer_extra_id' => (1 == $package->location_id ? 1 : 5),
                'flight_number' => $arr_flight_number,
                'flight_time' => $arr_flight_time,
                'guests' => $guest,
                'price' => 0,
            ]);
        }

        if ($package->airport_dropoff) {
            $dep_flight_number = !request()->has('skip_transfer') ? request('departure_flight') : 'TBA';
            $dep_flight_time = !request()->has('skip_transfer') ? $check_out.' '.request('departure_time_h').':'.request('departure_time_m').':00' : null;

            BookingTransfer::create([
                'booking_id' => $booking->id,
                'transfer_extra_id' => (1 == $package->location_id ? 1 : 6),
                'flight_number' => $dep_flight_number,
                'flight_time' => $dep_flight_time,
                'guests' => $guest,
                'price' => 0,
            ]);
        }

        $this->paymentService->refreshPayment($booking);

        $booking->payment->update([
            'processing_fee' => $booking->processing_fee,
        ]);

        // check blacklist
        $blacklists = Blacklist::all();

        $diff = ($emails->diff($blacklists->pluck('email'))->all());

        if (count($diff) < $emails->count()) {
            $booking->update(['is_blacklisted' => 1]);
        }

        DB::commit();

        session(['sp_booking_ref' => $booking->ref, 'sp_guest_name' => $new_guest->full_name]);

        return redirect("book-package/{$package->slug}/confirm");
    }

    public function confirm($slug)
    {
        $booking = Booking::with(['guest'])->where('ref', session('sp_booking_ref'))->first();
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->first();
        $default_check_in = session('sp_check_in');
        $default_check_out = session('sp_check_out');
        $guest = intval(session('sp_guest'));
        $room = $package->room;
        $addons = $package->addons;
        $extras = session('sp_extras');
        $sp_transfers = session('sp_transfers');
        $inclusion = session('sp_inclusion');
        $step = 3;

        $pickup = null;
        $dropoff = null;

        if ($package->airport_pickup) {
            $pickup_id = 1 == $package->location_id ? 1 : 5;
            $pickup = TransferExtra::find($pickup_id);
        }

        if ($package->airport_dropoff) {
            $dropoff_id = 1 == $package->location_id ? 1 : 6;
            $dropoff = TransferExtra::find($dropoff_id);
        }

        $price = session('sp_price');

        $grand_total = $this->bookingService->recalculateSPGrandTotal();

        $documents = Document::orderBy('sort', 'asc')->get();

        $terms = $documents->where('position', 'terms-and-conditions')->sortBy('sort')->all();

        return view('Booking.book-package', compact('grand_total', 'extras', 'price', 'sp_transfers', 'package', 'default_check_in', 'default_check_out', 'step', 'guest', 'room', 'pickup', 'dropoff', 'addons', 'booking', 'inclusion', 'documents', 'terms'));
    }

    public function refreshConfirmBooking()
    {
        return redirect($this->homepage);
    }

    public function processConfirm($slug)
    {
        if (!session()->has('sp_booking_ref')) {
            // redirect back to kimasurf.com
            return redirect('book-package');
        }

        $ref = session('sp_booking_ref');
        $booking = Booking::with(['guest'])->where('ref', $ref)->first();
        $package = SpecialPackage::with(['addons.details', 'location', 'room'])->where('slug', $slug)->first();
        $default_check_in = session('sp_check_in');
        $default_check_out = session('sp_check_out');
        $guest = intval(session('sp_guest'));
        $guest_name = session('sp_guest_name');
        $room = $package->room;
        $addons = $package->addons;
        $price = session('sp_price');
        $sp_transfers = session('sp_transfers');
        $inclusion = session('sp_inclusion');
        $step = 4;
        $documents = Document::orderBy('sort', 'asc')->get();
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $subroom_id = $booking->rooms()->first()->subroom_id;
        $subroom = RoomInfo::with(['room'])->find($subroom_id);

        $pickup = null;
        $dropoff = null;

        if ($package->airport_pickup) {
            $pickup_id = 1;
            $pickup = TransferExtra::find($pickup_id);
        }

        if ($package->airport_dropoff) {
            $dropoff_id = 2;
            $dropoff = TransferExtra::find($dropoff_id);
        }

        if ($subroom->room->availability != 'auto') {
            $booking_status = $subroom->room->availability == 'pending' ? 'PENDING' : 'CONFIRMED';
        }

        $booking->update([
            'status' => $booking_status,
        ]);

        $date_start = new Carbon(session('booking_date_start'));
        $date_end = new Carbon(session('booking_date_end'));

        if ('PENDING' == $booking->status) {
            $this->bookingService->sendPendingEmail($booking);
        }

        if ('CONFIRMED' == $booking->status) {
            $this->bookingService->sendConfirmationEmail($booking);
        }

        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => null,
            'action' => 'Confirmed booking',
            'info_type' => 'info',
            'details' => '<b>'.$guest_name.'</b> confirmed booking #<b>'.$booking->ref.'</b>',
            'ip_address' => request()->ip(),
        ]);

        $extras = session('sp_extras');

        $grand_total = $this->bookingService->recalculateSPGrandTotal();

        session()->flush();

        return view('Booking.book-package', compact('grand_total', 'extras', 'sp_transfers', 'price', 'package', 'default_check_in', 'default_check_out', 'step', 'guest', 'room', 'pickup', 'dropoff', 'addons', 'booking', 'ref', 'inclusion', 'documents', 'profile'));
    }

    public function saveComment()
    {
        $comment = request('comment');

        session(['sp_comment' => $comment]);

        return response('OK');
    }

    protected function isTodayGreaterThanMinimumPackageCheckInDate(Carbon $check_in, Carbon $today)
    {
        return $today->gte($check_in);
    }

    protected function getSeason($date, $room)
    {
        $season = PricingCalendar::where('room_id', $room->id)->where('date', $date)->first();

        if (is_null($season)) {
            return 'LOW';
        }

        return 'BLOCK' == $season->season_type ? 'LOW' : $season->season_type;
    }
}
