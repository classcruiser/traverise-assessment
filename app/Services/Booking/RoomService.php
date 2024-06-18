<?php

namespace App\Services\Booking;

use Debugbar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Booking\Room;
use Illuminate\Http\Request;
use App\Models\Booking\Setting;

use Illuminate\Support\Collection;
use App\Models\Booking\BookingRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Booking\PricingCalendar;
use App\Models\Booking\RoomInfo;

class RoomService
{
    /**
     * Retrieves the availability of rooms based on the given parameters.
     * For most availability related operations, you should use this function.
     *
     * @param Collection $rooms The collection of rooms to check availability for.
     * @param string $check_in The check-in date in the format "Y-m-d".
     * @param string $check_out The check-out date in the format "Y-m-d".
     * @param int $guest The number of guests.
     * @param bool $private Indicates whether the room should be private or shared.
     * @param array $options Additional options for retrieving availability.
     * @return array The availability of rooms.
     */
    public function getAvailability(Collection $rooms, string $check_in, string $check_out, int $guest, bool $private, array $options = []): array
    {
        $exclude_ids = $options['exclude_ids'] ?? [];
        $subroom_id = $options['subroom_id'] ?? null;
        $location_id = $options['location_id'] ?? null;
        $from_API = $options['from_API'] ?? false;

        $occupancy = $this->getRoomOccupancy(
            check_in: $check_in,
            check_out: $check_out,
            rooms: $rooms,
            guest: $guest,
            exclude_ids: $exclude_ids,
            subroom_id: $subroom_id,
            location_id: $location_id,
            private_booking: $private
        );

        $result = $this->buildRoomList($rooms, $occupancy, [
            'check_in' => $check_in,
            'check_out' => $check_out,
            'duration' => Carbon::parse($check_in)->diffInDays(Carbon::parse($check_out)),
        ], null, $private, $guest);

        $availability = $this->getAvailabilityList(
            room_list: $result,
            guest: $guest,
            from_API: $from_API,
            private_booking: $private
        );

        $response = [
            'availability' => $availability,
            'result' => $result->toArray()
        ];

        return $response;
    }

    /**
     * Get the room occupancy based on the check-in and check-out dates, rooms, and other parameters.
     * This function is used internally by the system to determine the room availability.
     *
     * @param string $check_in The check-in date.
     * @param string $check_out The check-out date.
     * @param Collection $rooms The collection of rooms.
     * @param int $guest The number of guests.
     * @param array $exclude_ids The array of room IDs to exclude.
     * @param int|null $subroom_id The ID of the subroom.
     * @param int|null $location_id The ID of the location.
     * @param bool $private_booking Indicates if it is a private booking.
     * @param bool $from_backend Indicates if it is from the backend.
     * @return array The array containing the room occupancy information.
     */
    public function getRoomOccupancy(
        string $check_in,
        string $check_out,
        Collection $rooms,
        int $guest,
        array $exclude_ids = [],
        int $subroom_id = null,
        int $location_id = null,
        bool $private_booking = false,
        bool $from_backend = false
    ): array
    {
        $bookings = $this->getBookingRooms(
            rooms: $rooms,
            check_in: $check_in,
            check_out: $check_out,
            exclude_ids: $exclude_ids,
            location_id: $location_id
        );

        $this->hasAPendingBookingRequest(bookings: $bookings);

        $response = $this->generateRoomOccupancyData(
            check_in: $check_in,
            check_out: $check_out,
            rooms: $rooms,
            guest: $guest,
            bookings: $bookings,
            private_booking: $private_booking,
            from_backend: $from_backend
        );

        return $response;
    }

    /**
     * Generates room occupancy data based on check-in and check-out dates, room collection, number of guests,
     * booking collection, private booking flag, and backend flag.
     *
     * @param string $check_in The check-in date.
     * @param string $check_out The check-out date.
     * @param Collection $rooms The collection of rooms.
     * @param int $guest The number of guests.
     * @param Collection $bookings The collection of bookings.
     * @param bool $private_booking Flag indicating if it is a private booking.
     * @param bool $from_backend Flag indicating if it is from the backend.
     * @return array The generated room occupancy data.
     */
    protected function generateRoomOccupancyData(
        string $check_in,
        string $check_out,
        Collection $rooms,
        int $guest,
        Collection $bookings,
        bool $private_booking,
        bool $from_backend
    ): array
    {
        $cal_prices = $this->getCalendarPricings(rooms: $rooms, check_in: $check_in, check_out: $check_out);

        $rates = [];

        $period = CarbonPeriod::create($check_in, $check_out, CarbonPeriod::EXCLUDE_END_DATE);
        $period_with_end_date = CarbonPeriod::create($check_in, $check_out);

        $room_capacity = [];

        foreach ($rooms as $room) {

            $prog_prices = $room->progressive_prices->keyBy('beds')->toArray();
            $occupancy_prices = $room->occupancy_prices->keyBy('pax')->toArray();
            $room_capacity[$room->id] = intVal($room->total_capacity);
            $extra_space = null;

            $filtered_bookings = $this->filterBookingsByRoom($bookings, $room->id);

            if (!isset($rates[$room->id])) {
                $rates[$room->id] = $this->generateDefaultRoomRateData(nights: $period->count());

                $subtotal = 0;

                $total_surcharge = 0;

                foreach ($period as $cdate) {
                    $date = $cdate->format('Y-m-d');

                    $room_occupied = $this->filterBookingsByDate($filtered_bookings, $cdate);

                    $occupied = 0;

                    if ($room_occupied) {
                        foreach ($room_occupied as $ro) {
                            $occupied += $ro->is_private ? ($ro->room->private_space <= 0 ? $ro->subroom->beds : $ro->room->private_space) : 1;
                        }
                    }

                    $room_occupied = null;

                    $progressive_price = 0;

                    $beds = intVal($room->total_capacity) - intVal($occupied);

                    $season_price = isset($cal_prices[$room->id]) && isset($cal_prices[$room->id][$date]) ? $cal_prices[$room->id][$date] : null;

                    $progressive_rate = isset($prog_prices[$beds]) ? $prog_prices[$beds] : null;

                    $price = $room->default_price;

                    $season = 'LOW';
                    $calendar_block = false;

                    if ($season_price) {
                        $price = $season_price['price'] <= 0 && ($season_price['season_type'] == 'BLOCK' || $season_price['season_type'] == 'FULL') ? $room->default_price : $season_price['price'];
                        $calendar_block = true;
                        $season = $season_price['season_type'];
                    }

                    if ($progressive_rate) {
                        $progressive_price = ($price * (floatVal($progressive_rate['amount']) / 100));
                    }

                    // surcharge
                    $surcharge_col = 'empty_fee_' . (strtolower($season));
                    $surcharge = $room->{$surcharge_col} ?? 0;

                    $occupancy_surcharge_col = strtolower($season) == 'block' ? 'amount_low' : 'amount_' . strtolower($season);
                    $occupancy_surcharge = isset($occupancy_prices[$guest]) ? (isset($occupancy_prices[$guest][$occupancy_surcharge_col]) ? $occupancy_prices[$guest][$occupancy_surcharge_col] : 0) : 0;

                    $rates[$room->id]['rates'][$date] = [
                        'calendar_block' => $calendar_block,
                        'season' => $season,
                        'beds' => $beds,
                        'price' => $price,
                        'surcharge' => $surcharge,
                        'progressive_price' => $progressive_price,
                        'occupancy_price' => $occupancy_surcharge,
                        'subtotal' => floatVal(round($price + $progressive_price + $occupancy_surcharge, 2))
                    ];

                    $subtotal += floatVal(round($rates[$room->id]['rates'][$date]['subtotal'], 2));
                    $total_surcharge += floatVal(round($rates[$room->id]['rates'][$date]['surcharge'], 2));
                }

                $max_duration_discount = intVal($period->count()) > intVal($room->location->max_discount) ? intVal($room->location->max_discount) : intVal($period->count()) * intVal($room->location->min_discount);

                if ($max_duration_discount <= 0) {
                    $max_duration_discount = 1;
                }

                $room_price = $subtotal * $guest;

                $duration_discount = $room->location->duration_discount ? floatVal(round($room_price * ($max_duration_discount / 100), 2)) : 0;

                if ($private_booking || ($room->room_type == 'Private' && !$from_backend)) {
                    if ($guest <= 1) {
                        $room_price = $room_price + $total_surcharge;
                    }
                }

                $rates[$room->id]['guest'] = $guest;
                $rates[$room->id]['room_price'] = $room_price;
                $rates[$room->id]['surcharge'] = $total_surcharge;
                $rates[$room->id]['final_room_price'] = floatVal(round($room_price - $duration_discount, 2));
                $rates[$room->id]['duration_discount'] = $duration_discount;
                $rates[$room->id]['average_price'] = floatVal(round($room_price / $max_duration_discount, 2));
            }

            // loop subrooms
            $total_bed_occupied = 0;
            foreach ($room->rooms as $subroom) {
                $filtered_subroom_bookings = $filtered_bookings->filter(function ($b) use ($subroom) {
                    return $b->subroom_id == $subroom->id;
                });

                if (!isset($rates[$room->id]['occupancy'][$subroom->id])) {
                    $daily_occupancy = [];
                    $oc = 0;
                    $can_book = 1;
                    $bed_number = [];
                    $occ = $filtered_subroom_bookings
                    ->filter(function ($q) use ($check_in, $check_out) {
                        $_to = strtotime($q->to);
                        $_from = strtotime($q->from);
                        $_check_in = strtotime($check_in);
                        $_check_out = strtotime($check_out);
                        return ($_to > $_check_in) && ($_from <= $_check_out);
                    });

                    if ($occ) {
                        foreach ($occ as $ro) {
                            $oc += $ro->is_private ? ($ro->room->private_space <= 0 ? $subroom->beds : $ro->room->private_space) : 1;

                            if ($ro->is_private && $ro->room->private_space <= 0) {
                                // get all bed for this room
                                $bed_number = range(1, $subroom->beds);
                            } else {
                                if (!in_array($ro->bed, $bed_number)) {
                                    array_push($bed_number, $ro->bed);
                                }
                            }
                        }
                    }

                    foreach ($period as $cdate) {
                        $date = $cdate->format('Y-m-d');
                        $room_occupied = $filtered_subroom_bookings->filter(function ($q) use ($date) {
                            $_to = strtotime($q->to);
                            $_date = strtotime($date);
                            $_from = strtotime($q->from);
                            //return Carbon::parse($q->to)->gt(Carbon::parse($date)) && Carbon::parse($q->from)->lte(Carbon::parse($date));
                            return ($_to > $_date) && ($_from <= $_date);
                        });

                        $occupied = 0;

                        if ($room_occupied) {
                            foreach ($room_occupied as $ro) {
                                $occupied += $ro->is_private ? ($ro->room->private_space <= 0 ? $subroom->beds : $ro->room->private_space) : 1;
                            }
                        }

                        // is the room must be book as private?
                        $extra_space = 0;

                        if ($room->room_type == 'Private' && $guest <= 1 && $room->allow_private) {
                            $extra_space = $private_booking ? ($room->private_space <= 0 ? $subroom->beds : $room->private_space) : 1;

                            $can_book = (intVal($occupied) + $extra_space <= $subroom->beds);
                        } else {
                            $can_book = (intVal($occupied) + intVal($guest) <= $subroom->beds);
                        }

                        // if pricing calendar is blocked
                        // $rates[$room->id]['rates'][$date]['calendar_block']
                        if ($rates[$room->id]['rates'][$date]['price'] <= 0 || $rates[$room->id]['rates'][$date]['season'] == 'BLOCK' || $rates[$room->id]['rates'][$date]['season'] == 'FULL') {
                            $oc = $subroom->beds;
                            $bed_number = range(1, $subroom->beds);
                            $can_book = 0;
                            $total_occupied = $subroom->beds;
                            $available = 0;
                        } else {
                            $total_occupied = $occupied;
                            $available = $subroom->beds - $total_occupied;
                        }

                        $daily_occupancy[$date] = $total_occupied;
                    }

                    // make the bar
                    $occupancy_bar = $this->generateOccupancyBar($daily_occupancy, $subroom, $period_with_end_date);

                    $rates[$room->id]['occupancy'][$subroom->id] = [
                        //'occupied' => $oc,
                        'occupied' => count($bed_number),
                        'can_book' => ($private_booking && count($bed_number)) ? false : $can_book,
                        'capacity' => $subroom->beds,
                        'occupied_beds' => $bed_number,
                        'daily_occupancy' => $daily_occupancy,
                        'occupancy_bar' => $occupancy_bar
                    ];

                    //$total_bed_occupied += $oc;
                    $total_bed_occupied += count($bed_number);
                }
            }

            $rates[$room->id]['total_occupied'] = $total_bed_occupied;
            $rates[$room->id]['available_beds'] = $room_capacity[$room->id] - $total_bed_occupied;
            $rates[$room->id]['can_book_room'] = $extra_space ? (($room_capacity[$room->id] - $total_bed_occupied) >= $extra_space) : (($room_capacity[$room->id] - $total_bed_occupied) >= $guest);
        }

        return $rates;
    }

    /**
     * Checks if there is a pending booking request in the given collection of bookings.
     * If there is, it will be added to the collection so system will consider it as a booked room.
     *
     * @param Collection $bookings The collection of bookings to check.
     * @return void
     */
    protected function hasAPendingBookingRequest(Collection &$bookings): void
    {
        if (request()->has('ref') && request()->has('status') && request('status') == 'PENDING') {
            $booking = BookingRoom::with(['subroom:id,room_id,beds'])
                ->where('booking_id', request('id'))
                ->first(['id', 'from', 'to', 'is_private', 'room_id', 'subroom_id', 'bed', 'guest', 'price', 'duration_discount', 'created_at']);

            if ($booking) {
                $bookings->push($booking);
            }
        }
    }

    /**
     * Generates a cache name for rooms based on the given prefix, room collection, check-in date, and check-out date.
     *
     * @param string $prefix The prefix to be added to the cache name (optional).
     * @param Collection $rooms The collection of rooms.
     * @param string $check_in The check-in date.
     * @param string $check_out The check-out date.
     * @return string The generated cache name.
     */
    protected function generateRoomCacheName(string $prefix = '', Collection $rooms, string $check_in, string $check_out): string
    {
        return ($prefix ? $prefix .'-' : '') . $rooms->pluck('id')->implode('_') .'-'. $check_in .'-'. $check_out;
    }

    /**
     * Retrieves the calendar pricings for a collection of rooms within a specified date range.
     *
     * @param Collection $rooms The collection of rooms.
     * @param string $check_in The check-in date in the format "Y-m-d".
     * @param string $check_out The check-out date in the format "Y-m-d".
     * @return array The array of calendar pricings.
     */
    protected function getCalendarPricings(Collection $rooms, string $check_in, string $check_out): array
    {
        $room_ids = $rooms->pluck('id')->toArray();

        $room_cache_name = $this->generateRoomCacheName(prefix: 'calendar', rooms: $rooms, check_in: $check_in, check_out: $check_out);

        return Cache::remember($room_cache_name, 5, function () use ($room_ids, $check_in, $check_out) {
            return PricingCalendar::whereIn('room_id', $room_ids)
                ->whereDate('date', '>=', $check_in)
                ->whereDate('date', '<=', $check_out)
                ->get(['id', 'room_id', 'price', 'season_type', 'date'])
                ->groupBy('room_id')
                ->map(function ($item, $key) {
                    return $item->keyBy('date');
                })->toArray();
        });
    }

    /**
     * Retrieves the existing booking by its rooms based on the given parameters.
     *
     * @param Collection $rooms The collection of rooms to filter.
     * @param string $check_in The check-in date in the format "Y-m-d".
     * @param string $check_out The check-out date in the format "Y-m-d".
     * @param array $exclude_ids The array of room IDs to exclude from the result.
     * @param int|null $location_id The ID of the location to filter the rooms by. If null, all locations will be considered.
     * @return Collection The collection of available booking rooms.
     */
    protected function getBookingRooms(Collection $rooms, string $check_in, string $check_out, array $exclude_ids = [], int $location_id = null): Collection
    {
        $room_ids = $rooms->pluck('id')->toArray();

        return BookingRoom::with(['subroom:id,room_id,beds'])
            ->where('to', '>', $check_in)
            ->where('from', '<', $check_out)
            ->whereIn('room_id', $room_ids)
            ->whereNotIn('id', $exclude_ids)
            ->whereHas('booking', function ($q) use ($location_id) {
                if ($location_id) {
                    return $q
                    ->where('location_id', $location_id)
                    ->where('status', '!=', 'PENDING')
                    ->where('status', '!=', 'EXPIRED')
                    ->where('status', '!=', 'CANCELLED')
                    ->where('status', '!=', 'ABANDONED');
                } else {
                    return $q
                    ->where('status', '!=', 'PENDING')
                    ->where('status', '!=', 'EXPIRED')
                    ->where('status', '!=', 'CANCELLED')
                    ->where('status', '!=', 'ABANDONED');
                }
            })
            ->get(['id', 'from', 'to', 'is_private', 'room_id', 'subroom_id', 'bed', 'guest', 'price', 'duration_discount', 'created_at']);
    }

    /**
     * Filters the bookings we have by its room ID.
     *
     * @param Collection $bookings The collection of bookings.
     * @param int $room_id The ID of the room.
     * @return Collection The filtered collection of bookings.
     */
    protected function filterBookingsByRoom(Collection $bookings, int $room_id): Collection
    {
        return $bookings->filter(fn ($booking) => $booking->room_id == $room_id);
    }

    /**
     * Filters the bookings by date.
     * Check out date is considered as the last day of the booking and is excluded from the result.
     *
     * @param Collection $bookings The collection of bookings.
     * @param string $date The date to filter the bookings.
     * @return Collection The filtered collection of bookings.
     */
    protected function filterBookingsByDate(Collection $bookings, string $date): Collection
    {
        return $bookings->filter(fn ($booking) => $booking->from <= $date && $booking->to > $date);
    }

    /**
     * Generates the default room rate data for a given number of nights.
     *
     * @param int $nights The number of nights to generate the room rate data for.
     * @return array The generated room rate data.
     */
    protected function generateDefaultRoomRateData(int $nights): array
    {
        return [
            'rates' => [],
            'nights' => $nights,
            'occupancy' => [],
            'total_occupied' => 0,
            'room_price' => 0,
            'duration_discount' => 0,
            'average_price' => 0,
            'available_beds' => 0,
            'occupancy_price' => 0,
        ];
    }

    /**
     * Generates the occupancy bar for a specific subroom and period.
     *
     * @param array $occupancy The occupancy data
     * @param RoomInfo $subroom The subroom object.
     * @param string $period The period identifier.
     * @return void
     */
    protected function generateOccupancyBar(array $occupancy, RoomInfo $subroom, $period)
    {
        $occupancy_bar = [];
        $last = '';
        $last_available = '';

        foreach ($occupancy as $date => $occupied_bed) {
            $date = date('d.m.Y', strtotime($date));

            if (count($occupancy_bar) <= 0) {
                // first entry
                $occupancy_bar[$date] = [
                    'from' => $date,
                    'to' => $date,
                    'occupied' => $occupied_bed,
                    'free_bed' => $subroom->beds - $occupied_bed,
                    'color_code' => $this->determineBarColorCode($occupied_bed, $subroom->beds),
                    'length' => 1,
                    'text' => $this->determineBarText($occupied_bed, $subroom->beds)
                ];

                $last = $date;
                $last_available = $occupied_bed;
            } else {
                $occupancy_bar[$last]['to'] = $date;
                $occupancy_bar[$last]['length'] += 1;
                if ($occupied_bed != $last_available) {
                    // beda - update from dan to
                    $occupancy_bar[$last]['length'] -= 1;
                    $occupancy_bar[$date] = [
                        'from' => $date,
                        'to' => $date,
                        'occupied' => $occupied_bed,
                        'free_bed' => $subroom->beds - $occupied_bed,
                        'color_code' => $this->determineBarColorCode($occupied_bed,  $subroom->beds),
                        'length' => 1,
                        'text' => $this->determineBarText($occupied_bed,  $subroom->beds)
                    ];
                    $last = $date;
                }
                $last_available = $occupied_bed;
            }
        }

        if ($last != '' && isset($occupancy_bar[$last])) {
            $occupancy_bar[$last]['to'] = $period->last()->format('d.m.Y');
        }

        return $occupancy_bar;
    }

    public function buildRoomList($rooms, $occupancy, $date, $subroom_id = null, $is_private_booking = false, $guest = 1)
    {
        $result = collect([]);
        $duration = $date['duration'];

        $agent_rooms = null;

        if (Auth::check() && Auth::user()->role_id == 4) {
            $agent_rooms = Auth::user()->rooms()->pluck('room_id')->toArray();
        }

        if ($rooms->count() <= 0) {
            return null;
        }

        $index = 1;

        foreach ($rooms as $room) {

            $progressive_price = 0;
            $price = 0;

            if (!$result->has($room->id)) {
                $ro = isset($occupancy[$room->id]) ? $occupancy[$room->id] : null;
                $price = $ro['room_price'];
                $total_occupied = 0;
                $total_beds = $room->capacity * $room->rooms->count();
                $progressive_price = 0;
                $final_price = $ro['final_room_price'];

                $result[$room->id] = collect([
                    'available_beds' => $ro['available_beds'],
                    'private_booking' => $is_private_booking,
                    //'can_book_room' => $ro['can_book_room'] && $room->availability != 'pending',
                    'can_book_room' => $ro['can_book_room'],
                    'force_private' => $room->force_private,
                    'id' => $room->id,
                    'pos' => $index++,
                    'name' => $room->name,
                    'type' => $room->bed_type,
                    'availability' => $room->availability,
                    'capacity' => $room->total_capacity,
                    'limited_threshold' => $room->limited_threshold,
                    'occupancy_percentage' => intVal((intVal($ro['available_beds']) / intVal($room->total_capacity)) * 100),
                    'room_type' => $room->room_type,
                    'bed_type' => json_decode($room->bed_type, true),
                    'bathroom_type' => $room->bathroom_type,
                    'default_price' => $room->default_price,
                    'min_nights' => $room->min_nights,
                    'max_nights' => $room->max_nights,
                    'average_price' => $ro['average_price'],
                    'price' => $price,
                    'final_price' => $final_price,
                    'allow_private' => $room->allow_private,
                    'allow_pending' => $room->allow_pending,
                    'progressive_pricing' => $room->progressive_pricing,
                    'empty_fee' => $ro['surcharge'],
                    'active' => $room->active,
                    'duration' => $duration,
                    'duration_discount' => $ro['duration_discount'],
                    'total_occupied' => $ro['total_occupied'],
                    'rates' => $ro['rates'],
                    'rooms' => collect([]),
                    'subrooms' => collect([]),
                ]);

                foreach ($room->rooms as $sub) {
                    $room_occupied = ($ro && isset($ro['occupancy'][$sub->id]['occupied'])) ? $ro['occupancy'][$sub->id]['occupied'] : 0;
                    $room_data = [
                        'id' => $sub->id,
                        'name' => Auth::check() && Auth::user()->role_id == 4 ? $sub->agent_name : $sub->name,
                        'capacity' => $sub->beds,
                        'occupied' => $room_occupied,
                        'ota_reserved' => $sub->ota_reserved,
                        'occupied_beds' => ($ro && isset($ro['occupancy'][$sub->id]['occupied_beds'])) ? $ro['occupancy'][$sub->id]['occupied_beds'] : [],
                        'daily_occupancy' => ($ro && isset($ro['occupancy'][$sub->id]['daily_occupancy'])) ? $ro['occupancy'][$sub->id]['daily_occupancy'] : [],
                        'occupancy_bar' => ($ro && isset($ro['occupancy'][$sub->id]['occupancy_bar'])) ? $ro['occupancy'][$sub->id]['occupancy_bar'] : [],
                        'can_book' => ($ro && isset($ro['occupancy'][$sub->id]['can_book'])) ? $ro['occupancy'][$sub->id]['can_book'] : [],
                    ];

                    $result[$room->id]['rooms']->push($room_data);
                    $result[$room->id]['subrooms']->push($room_data);

                    $total_occupied += $room_occupied;
                }

                if ($agent_rooms) {
                    $result[$room->id]['rooms'] = $result[$room->id]['rooms']->filter(function ($room) use ($agent_rooms) {
                        return in_array($room['id'], $agent_rooms);
                    })->values();

                    $result[$room->id]['subrooms'] = $result[$room->id]['rooms'];
                }

                if ($subroom_id) {
                    // filter by subroom_id
                    $result[$room->id]['rooms'] = $result[$room->id]['rooms']->filter(function ($value, $key) use ($subroom_id) {
                        return $value['id'] == $subroom_id;
                    })->first();
                }

                $result[$room->id]['subrooms'] = $result[$room->id]['subrooms']->toArray();

                $result[$room->id]['duration_discount'] = $ro['duration_discount'];
                $result[$room->id]['price'] = $price;
            }
        }

        return $result;
    }

    public function findRoomForGuests(Collection $rooms, int $numGuests, bool $has_enough_rooms): array
    {
        if (!$rooms || count($rooms) <= 0) {
            return [];
        }

        $guests = $numGuests;
        $selectedRooms = [];
        $defaultRooms = [];

        foreach ($rooms as $room) {
            // Check if the room has enough free beds for the guests.
            if ($has_enough_rooms && ($room['occupied'] == 0 || ($room['occupied'] + $numGuests <= $room['capacity']))) {
                $availableBeds = $room['capacity'] - count($room['occupied_beds']);
                $beds = range(1, $room['capacity']);
                $bed_list = array_values(array_diff($beds, $room['occupied_beds']));

                if (count($bed_list) >= $numGuests) {
                    // Assign guests to the available beds in the room.
                    for ($i = 1; $i <= $numGuests; $i++) {
                        $selectedRooms[] = ['id' => $room['id'], 'bed' => $bed_list[$i-1]];
                    }
                    break;
                } else {
                    // If there are not enough beds in the current room, assign available beds and continue to the next room.
                    for ($i = 1; $i <= $availableBeds; $i++) {
                        $selectedRooms[] = ['id' => $room['id'], 'bed' => $i];
                    }
                    $numGuests -= $availableBeds;
                }
            }

            if (count($defaultRooms) < $numGuests) {
                for ($i = 1; $i <= $room['capacity']; $i++) {
                    $defaultRooms[] = ['id' => $room['id'], 'bed' => $i];
                }
            }
        }

        if (count($selectedRooms) < $guests) {
            $selectedRooms = array_merge($selectedRooms, $defaultRooms);
        }

        return count($selectedRooms) ? $selectedRooms : $defaultRooms;
    }

    public function getAvailabilityList($room_list, $guest, $from_API = false, $private_booking = false)
    {
        $result = [];

        if ($room_list && count($room_list) > 0) {
            foreach ($room_list as $id => $data) {
                $is_available = $this->isRoomAvailable($data['rooms'], $guest, $private_booking);

                $available_rooms = $guest == 1
                    ? $this->getAvailableRoomID($data['rooms'], $guest, $private_booking)
                    : $this->findRoomForGuests($data['rooms'], $guest, $is_available);

                $surcharge = round($data['empty_fee']);

                $final_price = round($data['final_price'], 2);
                if (request()->has('privateBooking') && request('privateBooking') && $guest <= 1) {
                    $basic_price = ($final_price - $surcharge) + round($data['duration_discount']);
                } else {
                    $basic_price = ($final_price) + round($data['duration_discount']);
                }

                $open_spot = collect($data['subrooms'])->filter(function ($room, $key) {
                    return $room['occupied'] <= 0;
                })->count();

                $limited_threshold = $data['limited_threshold'];
                $occupancy_percentage = $data['occupancy_percentage'];

                /*
                * 1 = available
                * 2 = limited
                * 3 = full
                */

                $availability_status = $this->determineRoomAvailability($limited_threshold, $occupancy_percentage, $is_available);

                if (!$data['can_book_room']) {
                    $availability_status = 2;
                }

                // last check - if rates has FULL then display status as 3
                if ($this->containsFullDate($data["rates"])) {
                    $is_available = false;
                    $availability_status = 3;
                }

                $result[$id] = [
                    'room_id' => $id,
                    'name' => $data['name'],
                    'private_booking' => $private_booking,
                    'surcharge' => $surcharge,
                    'limited_threshold' => $data['limited_threshold'],
                    'occupancy_percentage' => $data['occupancy_percentage'],
                    'availability_status' => intVal($availability_status),
                    'surcharge_text' => '&euro;'. $surcharge .' <em><b>Private Room Surcharge</b></em>',
                    'basic_price' => $basic_price,
                    'basic_price_text' => '&euro;'. number_format($basic_price) .' - <em><b>&euro;'. round($data['duration_discount']) .' Duration Discount</b></em>',
                    'price' => ($from_API && $data['room_type'] == 'Private' && $guest <= 1) ? round($data['final_price'] - $surcharge) : round($data['final_price']),
                    'price_text' => '&euro;'. number_format(round($data['final_price'])),
                    'duration' => $data['duration'],
                    'duration_discount' => round($data['duration_discount']),
                    'allow_private' => $data['allow_private'],
                    'type' => json_decode($data['type'], true),
                    'is_available' => $is_available && $data['can_book_room'],
                    'available_rooms' => $available_rooms,
                    'open_spot' => $open_spot
                ];
            }
        }

        return $result;
    }

    protected function containsFullDate($rates)
    {
        if (! is_array($rates)) {
            return false;
        }

        foreach ($rates as $date => $data) {
            if ($data['season'] == 'FULL') {
                return true;
            }
        }

        return false;
    }

    protected function determineRoomAvailability($threshold, $occupancy, $is_available) : int
    {
        // full occupancy - limited
        if (! $is_available) {
            return 2;
        }

        // disabled threshold - available
        if ($threshold <= 0) {
            return 1;
        }

        // enabled threshold and occupancy below threshold - limited
        if ($occupancy <= $threshold) {
            return 2;
        }

        // else - available
        return 1;
    }

    public function getAvailableRoomID($room_list, $guest, $private_booking = false)
    {
        if (!$room_list) {
            return [];
        }

        if (count($room_list) <= 0) {
            return [];
        }

        if (isset($room_list['id'])) {
            $room_list = [$room_list];
        }

        $available = [];
        $bed_taken = [];
        $assigned_room = [];

        // search room per total guest first only if guest is more than 1
        for ($i = 1; $i <= $guest; $i++) {
            $assigned_room[$i] = '';
            foreach ($room_list as $room) {
                $need_room = $private_booking ? $room['capacity'] : 1;
                $free = intVal($room['capacity'] - $room['occupied']);
                if ($free >= $need_room) {
                    foreach (range(1, $room['capacity']) as $bed) {
                        if (
                            $assigned_room[$i] == '' &&                           // this guest is not assigned a room yet
                            (!in_array($room['id'] .'-'. $bed, $bed_taken))       // this combination is not taken yet
                            && (!in_array($bed, $room['occupied_beds']))          // this bed is not exist in the occupied bed of this room
                        ) {
                            $assigned_room[$i] = $room['id'] .'-'. $bed;
                            array_push($bed_taken, $room['id'] .'-'. $bed);
                            array_push($available, [
                                'id' => $room['id'],
                                'bed' => $bed
                            ]);
                        }
                    }
                }
            }
                // twice
            foreach ($room_list as $room) {
                $free = intVal($room['capacity'] - $room['occupied']);
                foreach (range(1, $room['capacity']) as $bed) {
                    if (
                            $assigned_room[$i] == '' &&                           // this guest is not assigned a room yet
                            (!in_array($room['id'] .'-'. $bed, $bed_taken))       // this combination is not taken yet
                            && (!in_array($bed, $room['occupied_beds']))          // this bed is not exist in the occupied bed of this room
                        ) {
                        $assigned_room[$i] = $room['id'] .'-'. $bed;
                        $bed_taken[] = $room['id'] .'-'. $bed;
                        $available[] = [
                            'id' => $room['id'],
                            'bed' => $bed
                        ];
                    }
                }
            }
        }


        if (count($available) < $guest) {
            // ga cukup,
            for ($i = 1; $i <= $guest; $i++) {
                if (count($available) < $guest) {
                    foreach ($room_list as $room) {
                        if (count($available) < $guest) {
                            $available[] = [
                                'id' => $room['id'],
                                'bed' => $i
                            ];
                        }
                    }
                }
            }
        }

        return $available;
    }

    protected function isRoomAvailable($rooms, $guest, $private_booking = false)
    {
        $data = collect($rooms);

        $guest = intVal($guest);

        if ($data->has('id')) {
            $capacity = intVal($data['capacity']);
            $occupied = intVal($data['occupied']);
            $free = $capacity - $occupied;
            if ($guest > $capacity) {
                $guest = $capacity;
            }

            if (!$data['can_book']) {
                return false;
            }

            if ($private_booking && $occupied > 1) {
                return false;
            }

            if ($free >= $guest) {
                return true;
            }
        } else {
            if (count($data) <= 0 || is_null($rooms) || $rooms == '') {
                return false;
            }

            $total_occupied = $data->sum('occupied');
            $total_capacity = $data->sum('capacity');
            $room_capacity = $total_capacity / count($data);

            if ($guest > 1) {
                $available_rooms = $data
                    ->filter(fn ($room) => $room['occupied'] == 0 || ($room['occupied'] + $guest) <= $room['capacity'])
                    ->sum('capacity');
            } elseif ($guest <= 1) {
                if ($private_booking) {
                    $available_rooms = $data->filter(fn ($room) => $room['occupied'] == 0)->count();
                } else {
                    $available_rooms = $data->sum(fn ($room) => $room['capacity'] - $room['occupied']);
                }
            }

            $needed_rooms = intVal(ceil($guest / $room_capacity));

            if ((($total_occupied + $guest) <= $total_capacity) && ($available_rooms >= $needed_rooms)) {
                return true;
            }
        }

        return false;
    }

    protected function determineBarColorCode($occupied, $capacity)
    {
        $free_bed = $capacity - $occupied;

        if ($free_bed == 0) {
            return 'bg-danger';
        }

        if ($free_bed >= 1 && ($free_bed < ($capacity / 2))) {
            return 'bg-orange';
        }

        if ($free_bed == $capacity) {
            return 'bg-green';
        }

        if ($free_bed >= 1 && ($free_bed < $capacity) && ($free_bed >= ($capacity / 2))) {
            return 'bg-green';
        }
    }

    protected function determineBarText($occupied, $capacity)
    {
        $free_bed = $capacity - $occupied;

        $text = '';

        if ($occupied == 0) {
            $text = 'Available';
        } else if ($occupied == $capacity) {
            $text = 'Full';
        } else {
            $text = 'Available: '. $free_bed .' of '. $capacity;
        }

        return $text;
    }

    public function searchInternal($data)
    {
        $dates = explode('_', $data['dates']);
        $check_in = date('Y-m-d', strtotime($dates[0]));
        $check_out = date('Y-m-d', strtotime($dates[1]));
        $location = intval($data['location']);
        $guests = intval($data['guests']);

        $date = $this->convertDates($data['dates']);

                // get list of the rooms for this location, along with its sub rooms, prices and progressive pricings
        $rooms = Room::orderBy('sort', 'asc')
        ->with([
            'rooms',
            'progressive_prices:id,room_id,beds,amount',
            'location:id,max_discount,min_discount,duration_discount',
        ])
        ->where('active', 1)
        ->where('location_id', $location)
        ->get();

        // get all entry for this period of dates for this location
        $occupancy = $this->getRoomOccupancy($check_in, $check_out, $rooms, $guests, [], null, $location);

        $result = $this->buildRoomList($rooms, $occupancy, $date, null, null, $guests);

        $result = $result->sortBy('pos')->all();

        $availability = $this->getAvailabilityList($result, $guests, true);

        return $availability;
    }

    public function convertDates($dates)
    {
        $tmp = explode('_', $dates);
        $start = Carbon::createFromFormat('Y-m-d', $tmp[0]);
        $end = Carbon::createFromFormat('Y-m-d', $tmp[1]);
        $duration = $start->diffInDays($end);

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'duration' => intval($duration),
        ];
    }
}
