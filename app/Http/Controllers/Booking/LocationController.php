<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\BookingRule;
use App\Models\Booking\CustomTax;
use App\Models\Booking\Location;
use App\Models\Booking\Permission;
use App\Models\Booking\Room;
use App\Services\Booking\BookingService;
use App\Services\Booking\FileService;
use App\Services\Booking\RoomService;
use App\Services\Booking\UserService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;

class LocationController extends Controller
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
     * LOCATION INDEX.
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function index()
    {
        $locations = Location::with(['rooms'])->orderBy('name', 'asc')->get();

        return view('Booking.locations.index', compact('locations'));
    }

    public function indexAPI()
    {
        $req_dates = request()->has('dates') ? explode('_', request('dates')) : null;
        $camp = request()->has('camp') ? intval(request('camp')) : null;
        $room = request()->has('room') ? intval(request('room')) : null;
        $guest = request()->has('guest') ? intval(request('guest')) : 1;
        $key = request()->has('key') ? request('key') : null;

        if (!$key) {
            return response([
                'status' => 'error',
                'message' => 'API key missing.',
            ]);
        }

        $check = Cache::remember('api_check_'.$key, 60, function () use ($key) {
            return DB::table('api_keys')->where('key', $key)->first();
        });

        if (!$check) {
            return response([
                'status' => 'error',
                'message' => 'Invalid API key.',
            ]);
        }

        Cache::forget('camps_api_'. tenant('id'));
        $locations = Cache::remember('camps_api_'. tenant('id'), 10, function () {
            return Location::query()
                ->with([
                    'rooms' => fn ($q) => $q->where('active', 1),
                    'rooms.rooms',
                    'rooms.addons.extra' => fn ($q) => $q->where('admin_only', 0)->where('active', 1)
                ])
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($item, $key) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'abbr' => $item->abbr,
                        'picture' => asset('images/camps/'. tenant('id') .'_camp_'.$item->id.'.jpg'),
                        'rooms' => $item->rooms->sortBy(function ($room, $index) {
                            return $room['sort'];
                        })->map(function ($room, $index) {
                            return [
                                'id' => $room->id,
                                'name' => $room->name,
                                'description' => $room->room_description,
                                'description_html' => nl2br($room->room_description),
                                'room_type' => $room->room_type,
                                'capacity' => $room->capacity,
                                'beds' => $room->beds,
                                'default_price' => $room->default_price,
                                'sort' => $room->sort,
                                'active' => $room->active,
                                'total_rooms' => $room->total_rooms,
                                'addons' => $room->addons->filter(function ($addon) {
                                    return !is_null($addon->extra);
                                })->map(function ($addon, $index) {
                                    return [
                                        'id' => $addon->extra->id,
                                        'name' => $addon->extra->name,
                                        'description' => $addon->extra->description,
                                        'base_price' => $addon->extra->base_price,
                                        'rate_type' => $addon->extra->rate_type,
                                    ];
                                }),
                                'picture' => $room->featured_image ? asset('images/rooms/'. $room->id .'/'. $room->featured_image) : asset('images/rooms/'. tenant('id') .'_room_'.$room->id.'.jpg'),
                            ];
                        })->values()->all(),
                    ];
                })
            ;
        });

        if ($camp) {
            $locations = $locations->filter(function ($location) use ($camp) {
                return $location['id'] == $camp;
            });
        }

        if ($req_dates) {
            $date_start = new Carbon($req_dates[0]);
            $date_end = new Carbon($req_dates[1]);

            $default_check_in = $date_start->format('d M Y');
            $default_check_out = $date_end->format('d M Y');

            $cache_key = $camp.$date_start->format('Ymd').$date_end->format('Ymd').$guest;

            $dates = [
                'start' => $default_check_in,
                'end' => $default_check_out,
                'duration' => $date_start->diffInDays($date_end),
            ];

            $locations = Cache::remember('room_query_'.$cache_key, 1, function () use ($locations, $dates, $guest, $date_start, $date_end) {
                return $locations->map(function ($item, $key) use ($dates, $guest, $date_start, $date_end) {
                    $rooms = Room::orderBy('sort', 'asc')
                        ->with([
                            'rooms',
                            'prices',
                            'progressive_prices:id,room_id,beds,amount',
                            'location:id,max_discount,min_discount,duration_discount',
                        ])
                        ->where('active', 1)
                        ->where('location_id', $item['id'])
                        ->get([
                            'id', 'capacity', 'default_price', 'location_id', 'empty_fee_low', 'empty_fee_main', 'empty_fee_peak',
                            'room_type', 'price_type', 'limited_threshold', 'name', 'allow_private', 'bed_type'
                        ]);

                    $occupancy = $this->roomService->getRoomOccupancy($date_start->format('Y-m-d'), $date_end->format('Y-m-d'), $rooms, $guest);

                    if ($occupancy) {
                        $rooms_list = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, null, $guest);
                        $rooms_list = $rooms_list->sortBy('pos')->all();
                        $availability = $this->roomService->getAvailabilityList($rooms_list, $guest, true);
                    } else {
                        $availability = [];
                    }

                    $item['rooms'] = collect($item['rooms'])->map(function ($room, $index) use ($availability) {
                        $room['prices'] = isset($availability[$room['id']]) ? $availability[$room['id']] : null;

                        return $room;
                    });

                    return $item;
                });
            });
        }

        if ($camp) {
            $locations = $locations->filter(function ($location) use ($camp) {
                return $location['id'] == $camp;
            })->first();

            if ($room) {
                $locations = (collect($locations['rooms'])->filter(function ($item) use ($room) {
                    return $item['id'] == $room;
                }))->first();
            }
        }

        return response($locations);
    }

    public function create()
    {
        return view('Booking.locations.new');
    }

    public function insert(Request $request)
    {
        $camp = Location::create($request->only([
            'name', 'short_name', 'abbr', 'address', 'contact_email', 'phone', 'price_type', 'deposit_type',
            'deposit_value', 'deposit_due', 'min_discount', 'max_discount', 'minimum_nights', 'service', 'description',
            'cultural_tax', 'hotel_tax', 'goods_tax'
        ]));

        $camp->update([
            'allow_pending' => $request->has('allow_pending'),
            'enable_deposit' => $request->has('enable_deposit'),
            'duration_discount' => $request->has('duration_discount'),
            'minimum_checkin' => !empty($request->minimum_checkin) ? Carbon::createFromFormat('d.m.Y', $request->minimum_checkin)->format('Y-m-d') : null
        ]);

        return response([
            'status' => 'success',
            'id' => $camp->id,
        ]);
    }

    /**
     * CAMP EDIT.
     *
     * @param int $id
     *
     * @return Illuminate\Http\View
     */
    public function show($id)
    {
        $location = Location::with(['rooms'])->find($id);

        $path = '/tenancy/assets/images/camps/'. tenant('id') .'_camp_'. $id .'.jpg';
        $picture = @file_exists(public_path($path)) ? $path : null;
        $days = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];

        $taxes = CustomTax::where('is_active', 1)->orderBy('name', 'asc')->get();

        $location->minimum_checkin = !empty($location->minimum_checkin) ? Carbon::createFromFormat('Y-m-d', $location->minimum_checkin)->format('d.m.Y') : null;

        return view('Booking.locations.show', compact('location', 'picture', 'days', 'taxes'));
    }

    /**
     * UPDATE CAMP DETAILS.
     *
     * @param int    $id
     * @param object $request
     *
     * @return array
     */
    public function updateCampDetails($id, Request $request)
    {
        $camp = Location::find($id);

        $camp->update($request->only([
            'name', 'short_name', 'abbr', 'address', 'contact_email', 'phone', 'price_type', 'deposit_type', 'description',
            'deposit_value', 'deposit_due', 'min_discount', 'max_discount', 'minimum_nights', 'maximum_nights', 'service',
            'cultural_tax', 'hotel_tax', 'goods_tax', 'title', 'subtitle',
        ]));

        $camp->update([
            'allow_pending' => $request->has('allow_pending'),
            'enable_deposit' => $request->has('enable_deposit'),
            'duration_discount' => $request->has('duration_discount'),
            'active' => $request->has('active'),
            'has_arrival_rule' => $request->has('has_arrival_rule'),
            'minimum_checkin' => !empty($request->minimum_checkin) ? Carbon::createFromFormat('d.m.Y', $request->minimum_checkin)->format('Y-m-d') : null
        ]);

        $rule = BookingRule::firstOrCreate([
            'location_id' => $camp->id,
        ]);

        if (request()->has('has_arrival_rule')) {
            $rule->update([
                'period' => request('rule_period'),
                'disable_check_in_days' => request('disable_check_in_days'),
                'disable_check_out_days' => request('disable_check_out_days'),
                'is_active' => 1
            ]);
        } else {
            $rule->update(['is_active' => 0]);
        }

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * UPDATE TERMS TEMPLATE.
     *
     * @param int    $id
     * @param object $request
     *
     * @return array
     */
    public function updateTermsTemplate($id, Request $request)
    {
        $camp = Location::find($id)->update([
            'terms' => $request->terms,
        ]);

        return response([
            'status' => 'success',
        ]);
    }

    public function updateBankTransfer($id, Request $request)
    {
        $camp = Location::find($id)->update([
            'bank_transfer' => request()->has('bank_transfer'),
            'bank_transfer_text' => request('bank_transfer_text')
        ]);

        return response([
            'status' => 'success',
        ]);
    }

    public function upload($id)
    {
        if (request()->has('file')) {
            $response = (new FileService())->upload(request('file'), '/tenancy/assets/images/camps', tenant('id') .'_camp_'. $id. '.jpg', ['w' => 800, 'h' => 500]);
        }

        return redirect(route('tenant.camps.show', $id) .'#images');
    }

    public function destroy(int $id): RedirectResponse
    {
        $camp = Location::find($id);
        $camp->delete();

        $path = public_path('/tenancy/assets/images/camps/'. tenant('id') .'_camp_'. $id .'.jpg');
        if (@file_exists($path)) {
            unlink($path);
        }

        return redirect(route('tenant.camps'));
    }

    public function duplicate(int $id): RedirectResponse
    {
        /**
         * What needs to be duplicated:
         * - Location
         * - Rooms
         * - Prices
         * - Progressive Prices
         * - Addons
         */

        $replicates = [];

        $camp = Location::with([
            'rooms', 'rooms.prices', 'rooms.progressive_prices', 'rooms.addons', 'rooms.rooms', 'rule',
            'rooms.occupancy_prices'
        ])->find($id);

        $new_camp = $camp->replicate();
        $new_camp->name = $new_camp->name . ' (Copy)';
        $new_camp->save();

        // copy camp rule if any
        $replicates['rule'] = $camp->rule ? $camp->rule->replicate() : null;
        $new_camp->rule()->save($replicates['rule']);

        // copy camp file picture
        $path = public_path('/tenancy/assets/images/camps/'. tenant('id') .'_camp_'. $id .'.jpg');
        if (@file_exists($path)) {
            $new_path = public_path('/tenancy/assets/images/camps/'. tenant('id') .'_camp_'. $new_camp->id .'.jpg');
            copy($path, $new_path);
        }

        // copy rooms
        $replicates['rooms'] = $camp->rooms->map(function ($room) use ($new_camp) {
            $new_room = $room->replicate();
            $new_room->location_id = $new_camp->id;
            $new_room->save();

            // copy room file picture
            $path = public_path('/tenancy/assets/images/rooms/'. tenant('id') .'_room_'. $room->id .'.jpg');
            if (@file_exists($path)) {
                $new_path = public_path('/tenancy/assets/images/rooms/'. tenant('id') .'_room_'. $new_room->id .'.jpg');
                copy($path, $new_path);
            }

            // copy room prices
            $replicates['prices'][$room->id] = $room->prices->map(function ($price) use ($new_room) {
                $new_price = $price->replicate();
                $new_price->room_id = $new_room->id;
                $new_price->save();

                return $new_price;
            });

            // copy room addons
            $replicates['addons'][$room->id] = $room->addons->map(function ($addon) use ($new_room) {
                $new_addon = $addon->replicate();
                $new_addon->room_id = $new_room->id;
                $new_addon->save();

                return $new_addon;
            });

            // copy room subroom
            $replicates['subrooms'][$room->id] = $room->rooms->map(function ($subroom) use ($new_room) {
                $new_subroom = $subroom->replicate();
                $new_subroom->room_id = $new_room->id;
                $new_subroom->save();

                return $new_subroom;
            });

            // copy room progressive prices
            $replicates['progressive_prices'][$room->id] = $room->progressive_prices->map(function ($price) use ($new_room) {
                $new_price = $price->replicate();
                $new_price->room_id = $new_room->id;
                $new_price->save();

                return $new_price;
            });

            // copy room occupancy surcharges prices
            $replicates['occupancy_prices'][$room->id] = $room->occupancy_prices->map(function ($price) use ($new_room) {
                $new_price = $price->replicate();
                $new_price->room_id = $new_room->id;
                $new_price->save();

                return $new_price;
            });
        });

        return redirect(route('tenant.camps'));
    }
}
