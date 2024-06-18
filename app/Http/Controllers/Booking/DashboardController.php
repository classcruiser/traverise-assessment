<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingDriver;
use App\Models\Booking\BookingGuest;
use App\Models\Booking\BookingRoom;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\Driver;
use App\Models\Booking\Location;
use App\Models\Booking\Room;
use App\Services\Booking\BookingService;
use App\Services\Booking\UserService;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $bookingService;
    protected $userService;

    public function __construct(BookingService $bookingService, UserService $user)
    {
        $this->bookingService = $bookingService;
        $this->userService = $user;

        $count = (Booking::where('status', 'DRAFT')->count());
    }

    public function index(Request $request)
    {
        if (tenant('plan') == 'events') {
            return redirect()->route('tenant.classes.bookings.index');
        }

        $allowed_camps = $this->userService->user()->allowed_camps;

        $locations = Location::query()
            ->with(['rooms:id,location_id,name,allow_private', 'rooms.rooms:id,room_id,name'])
            ->when(!is_null($allowed_camps), function ($q) use ($allowed_camps) {
                $q->whereIn('id', json_decode($allowed_camps, true));
            })
            ->get(['id', 'name', 'abbr', 'short_name']);

        $bookings = BookingRoom::with([
            'guestDetails.details.details',
            'booking.payment',
            'room',
            'subroom',
        ])->whereHas(
            'booking', fn (Builder $query) => $query->where('status', 'CONFIRMED')
        );

        if ($request->has('check_in_dates') && '' != $request->check_in_dates) {
            $dts = explode(' - ', request('check_in_dates'));
            $date_start = explode('.', $dts[0]);
            $date_start = $date_start[2].'-'.$date_start[1].'-'.$date_start[0];
            $date_end = explode('.', $dts[1]);
            $date_end = $date_end[2].'-'.$date_end[1].'-'.$date_end[0];
        } else {
            $date_end = $date_start = date("Y-m-d");
        }

        $bookings = $bookings
            ->where('from', '>=', $date_start)->where('from', '<=', $date_end)
            ->orderBy('from', 'asc')
            ->get()
            ->sortBy(['date', 'asc']);

        return view('Booking.dashboard.index', compact('locations', 'bookings'));
    }

    public function updateTransfers()
    {
        $transfers = BookingTransfer::with(['booking'])->where('flight_number', 'TBA')->where('updated_at', 'like', '%' . date('Y-m-d') . '%')->whereIn('transfer_extra_id', [2, 4])->get();

        foreach ($transfers as $transfer) {
            $transfer->update([
                'flight_time' => $transfer->booking->check_out->format('Y-m-d') . ' 03:00:00',
            ]);
        }

        return response('OK');
    }

    /**
     * For dashboard
     * Show arriving / departing guests by check in / out date.
     *
     * @param mixed $type
     */
    public function stayingGuests($type, Request $request)
    {
        $dates = explode(" - ", $request->has('date') ? html_entity_decode($request->date) : '');
        $start_date = Carbon::now()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        if ($dates[0]) {
            $start_date = new Carbon($dates[0]);
            $start_date = $start_date->format('Y-m-d');
        }
        if ($dates[1]) {
            $end_date = new Carbon($dates[1]);
            $end_date = $end_date->format('Y-m-d');
        }
        $range = $request->has('range') ? $request->range : 0;
        $camp = $request->has('camp') ? $request->camp : 'all';
        $transfer_type = 'arrival' == $type ? 'arriving' : 'departure';
        $date_column = 'arrival' == $type ? 'check_in' : 'check_out';
        $result = [];

        if ('arrival' == $type) {
            session(['defaultArrival' => $range]);
        } else {
            session(['defaultDeparture' => $range]);
        }

        $is_agent = $this->userService->is_agent();


        //Cache::forget($transfer_type . '_staying_guest_' . $range . '_days_' . $camp . '_' . $this->userService->user()->id . '_' . date('Ymd'));
        $transfers = Cache::remember($transfer_type . '_staying_guest_' . $range . '_days_' . $camp . '_' . $this->userService->user()->id . '_' . date('Ymd'), 1, function () use ($range, $camp, $date_column, $start_date, $end_date, $is_agent) {
            $allowed_camps = $this->userService->user()->allowed_camps;
            $date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($range);

            return Booking::with(['location', 'guest.details', 'rooms.room', 'rooms.subroom', 'transfers.details', 'payment'])
                ->when($is_agent, fn ($query) => $query->where('source_id', $this->userService->user()->id)->orWhere('agent_id', $this->userService->user()->id))
                ->withCount('guests')
                ->when(!is_null($allowed_camps), function ($q) use ($allowed_camps) {
                    $q->whereIn('location_id', json_decode($allowed_camps, true));
                })
                ->orderBy($date_column)
                ->where(function ($q) {
                    $q->where('status', 'CONFIRMED');
                })
                ->where($date_column, '>=', $start_date)
                ->where($date_column, '<=', $end_date)
                ->when($camp != 'all', fn ($query) => $query->where('location_id', $camp))
                ->get();
        });

        $date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($range);

        $flight_type = 'arrival' == $type ? 'Inbound' : 'Outbound';

        $result = $transfers->map(function ($booking, $key) use ($flight_type) {
            $flights = $booking->transfers;
            if ($flights) {
                $transfer_info = [];
                $transfer_time = [];
                $info = '';
                $time = '';
                foreach ($flights as $flight) {
                    if ($flight->details->direction == $flight_type) {
                        $info .= 'TBA' != $flight->flight_number ? $flight->flight_number . ' (' . $flight->guests . ' ' . Str::plural('guest', $flight->guests) . ')' : 'TBA';
                        $time .= $flight->flight_time ? $flight->flight_time->format('d.m.Y H:i') : '-';

                        array_push($transfer_info, $info);
                        array_push($transfer_time, $time);
                        $info = '';
                        $time = '';
                    }
                }
            } else {
                $transfer_info = '-';
                $transfer_time = '-';
                $transfer_number = '-';
            }

            $transfer_info = implode('<br />', $transfer_info);
            $transfer_time = implode('<br />', $transfer_time);

            return [
                'id' => $booking->id,
                'status' => $booking->status,
                'location' => $booking->location->name,
                'ref' => $booking->ref,
                'check_in' => $booking->check_in->format('d.m.Y'),
                'check_out' => $booking->check_out->format('d.m.Y'),
                'guests_count' => $booking->guests_count,
                'payment_status' => $booking->payment->status,
                'guest_name' => $booking->guest?->details?->full_name,
                'guest_link' => $booking->guest?->details?->id,
                'booked_date' => $booking->created_at->format('d.m.Y H:i'),
                'transfer_info' => $transfer_info,
                'transfer_time' => $transfer_time,
                'status_badge' => $booking->status_badge,
                'rooms_name' => $booking->getAllRoomsName(),
                'phone' => $booking->guest?->details?->phone,
            ];
        });

        return response($result);
    }

    /**
     * for transfer page
     * show pickup / dropoff schedule on transfer page.
     *
     * @param mixed $type
     */
    public function transferGuests($type, Request $request)
    {
        $dates = explode(" - ", $request->has('date') ? html_entity_decode($request->date) : '');
        $start_date = Carbon::now()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        if ($dates[0]) {
            $start_date = new Carbon($dates[0]);
            $start_date = $start_date->format('Y-m-d');
        }
        if ($dates[1]) {
            $end_date = new Carbon($dates[1]);
            $end_date = $end_date->format('Y-m-d');
        }
        $range = $request->has('range') ? $request->range : 0;
        $camp = $request->has('camp') ? $request->camp : 'all';
        $transfer_type = 'arrival' == $type ? 'arriving' : 'departure';
        $date_column = 'arrival' == $type ? 'check_in' : 'check_out';
        $result = [];

        if ('arrival' == $type) {
            $ids = [1, 3, 5];
            session(['defaultArrival' => $range]);
        } else {
            $ids = [2, 4, 6];
            session(['defaultDeparture' => $range]);
        }

        $user = $this->userService->user();
        $is_agent = $this->userService->is_agent();

        $date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($range);
        $transfers = BookingTransfer::with(['booking' => function ($q) {
            $q->withCount(['guests', 'drivers']);
        }, 'booking.location' => function ($q) use ($user) {
            $q
                //  ->whereIn('id', json_decode($user->allowed_camps, true))
                ->select('id', 'name', 'abbr', 'short_name');
        }, 'booking.payment' => function ($q) {
            return $q->select('id', 'booking_id', 'status');
        }, 'booking.guest.details', 'booking.rooms' => function ($q) {
            return $q->select('id', 'booking_id', 'room_id', 'subroom_id', 'from', 'to', 'created_at');
        }, 'booking.rooms.subroom' => function ($q) {
            return $q->select('id', 'room_id', 'name');
        }])
            ->whereHas('booking', function ($q) use ($camp, $user, $is_agent) {
                $q
                    ->where('status', 'CONFIRMED')
                    ->when($is_agent, fn ($q) => $q->where('source_id', $user->id)->orWhere('agent_id', $user->id))
                    ->when($camp != 'all', fn ($query) => $query->where('location_id', $camp))
                    ->select('id', 'location_id', 'status', 'source_id', 'created_at', 'check_in', 'check_out');
            })
            ->whereIn('transfer_extra_id', $ids)
            ->orderBy('flight_time', 'asc')
            ->where('flight_time', '>=', $start_date . ' 00:00:00')
            ->where('flight_time', '<=', $end_date . ' 00:00:00');

        $flight_type = 'arrival' == $type ? 'Inbound' : 'Outbound';

        if ($transfers->count()) {
            $result = $transfers->get()->map(function ($transfer, $key) use ($user) {
                $booking = $transfer->booking;

                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'location' => $booking->location->name,
                    'location_id' => $booking->location_id,
                    'ref' => $booking->ref,
                    'check_in' => $booking->check_in->format('d.m.Y'),
                    'check_out' => $booking->check_out->format('d.m.Y'),
                    'guests_count' => $booking->guests_count,
                    'payment_status' => $booking->payment->status,
                    'guest_name' => $booking->guest->details->full_name,
                    'guest_link' => $booking->guest->details->id,
                    'booked_date' => $booking->created_at->format('d.m.Y H:i'),
                    'transfer_info' => $transfer->flight_number,
                    'transfer_time' => $transfer->flight_time->format('d.m.Y H:i'),
                    'status_badge' => $booking->status_badge,
                    'rooms_name' => $booking->getAllRoomsName($user->role_id),
                    'phone' => $booking->guest->details->phone,
                    'total_drivers' => $booking->drivers_count,
                    'drivers' => $booking->getAllDrivers(),
                ];
            });
        }

        return response($result);
    }

    /**
     * SCHEDULE INDEX.
     * @route /schedule
     *
     * @return object Illuminate\Http\View
     */
    public function schedule()
    {
        $allowed_camps = $this->userService->user()->allowed_camps;

        $locations = Location::query()
            ->with(['rooms:id,location_id,name,allow_private', 'rooms.rooms:id,room_id,name'])
            ->when(!is_null($allowed_camps), function ($q) use ($allowed_camps) {
                $q->whereIn('id', json_decode($allowed_camps, true));
            })
            ->get(['id', 'name', 'abbr', 'short_name']);

        $start = Carbon::now()->format('d.m.Y');

        $end = Carbon::now()->addDays(7)->format('d.m.Y');

        $dates = [
            'start' => $start,
            'end' => $end,
        ];

        return view('Booking.dashboard.schedule', compact('locations', 'dates'));
    }

    public function driverGuests($ref)
    {
        $booking = Booking::with(['guests.details', 'guests.driver.details'])->where('ref', $ref)->first();

        return response($booking->toArray());
    }

    public function updateDriver()
    {
        $guests = request('guest');
        $ref = request('ref');

        $booking = Booking::with(['guests'])->where('ref', $ref)->first();

        foreach ($guests as $guest_id => $guest) {
            $driver = BookingDriver::updateOrCreate(
                ['booking_id' => $booking->id, 'booking_guest_id' => $guest_id],
                ['driver_id' => $guest['driver'], 'notes' => $guest['notes']]
            );

            $guest = BookingGuest::find($guest_id);
            $notes = '' != $guest['notes'] ? ' (' . $guest['notes'] . ')' : '';

            $booking->histories()->create([
                'user_id' => auth()->user()->id,
                'info_type' => 'slate',
                'action' => 'Update driver',
                'details' => '<b>' . auth()->user()->name . '</b> assign driver <b>' . $driver->details->name . $notes . '</b> for guest <b>' . $guest->details->full_name . '</b>',
                'ip_address' => request()->ip(),
            ]);
        }

        return redirect('schedule');
    }

    /**
     * ROOM MOVE INFO.
     *
     * @return object Illuminate\Http\View
     */
    public function roomMove()
    {
        $date = Carbon::now();
        $date_end = Carbon::now()->addDays(7);
        $today = request()->has('start') ? request('start') : $date->format('Y-m-d');
        $end = request()->has('end') ? request('end') : $date_end->format('Y-m-d');

        $allowed_camps = $this->userService->user()->allowed_camps;

        $bookings = Booking::with(['location', 'rooms.mainGuest', 'guests.rooms.room.room', 'guests.rooms.room.subroom', 'guests.details'])
            ->where('status', 'CONFIRMED')
            ->where('check_in', '<', $end . ' 00:00:00')
            ->where('check_out', '>=', $today . ' 00:00:00')
            ->when(!is_null($allowed_camps), function ($q) use ($allowed_camps) {
                $q->whereIn('location_id', json_decode($allowed_camps, true));
            })
            ->get();

        $result = [];

        foreach ($bookings as $booking) {
            $result[$booking->id] = [
                'id' => $booking->id,
                'ref' => $booking->ref,
                'camp' => $booking->location->name,
                'check_in' => $booking->check_in->format('Y-m-d'),
                'check_out' => $booking->check_out->format('Y-m-d'),
                'guests' => $booking->guests,
            ];
        }

        // prepare the data
        $data = $this->transformRoomMoveData($result, $today);

        // only show guest with multiple rooms
        $data = $this->filterGuestWithMultipleRooms($data);

        // set default if no current room
        $data = $this->filterGuestWithNoCurrentRoom($data, $today);

        // sort by check out
        $data = $this->sortGuestByCheckOut($data);

        // only show guest which room move is not the last
        $data = $this->filterGuestWithLastMove($data);

        // finalize the data to be readable
        $data = $this->finalizeRoomMove($data, $today);

        return view('Booking.dashboard.room-move', compact('bookings', 'data', 'today'));
    }

    protected function transformRoomMoveData($result, $today)
    {
        $data = [];
        $today = Carbon::createFromFormat('Y-m-d', $today);

        foreach ($result as $r) {
            $data[$r['id']] = [
                'id' => $r['id'],
                'camp' => $r['camp'],
                'ref' => $r['ref'],
                'check_in' => $r['check_in'],
                'check_out' => $r['check_out'],
                'guests' => [],
            ];

            foreach ($r['guests'] as $guest) {
                if (!isset($data[$r['id']]['guests'][$guest['guest_id']])) {
                    $data[$r['id']]['guests'][$guest['guest_id']] = [];
                }

                foreach ($guest['rooms'] as $room) {
                    $from = Carbon::parse($room['room']['from']);
                    $to = Carbon::parse($room['room']['to']);

                    $current = ($to->gte($today) && $from->lt($today));

                    array_push($data[$r['id']]['guests'][$guest['guest_id']], [
                        'id' => $guest['details']['id'],
                        'name' => trim($guest['details']['full_name']),
                        'room' => $room['room']['room']['name'],
                        'subroom' => $room['room']['subroom']['name'],
                        'from' => $from->format('Y-m-d'),
                        'to' => $to->format('Y-m-d'),
                        'current' => $current,
                    ]);
                }
            }
        }

        return $data;
    }

    protected function filterGuestWithNoCurrentRoom($array, $today)
    {
        $data = collect($array);
        $today = Carbon::createFromFormat('Y-m-d', $today);

        return $data->map(function ($b, $id) use ($today) {
            $guests = collect($b['guests']);
            $b['guests'] = $guests->map(function ($guest, $guest_id) use ($today) {
                $guest = collect($guest);
                $sum = $guest->sum(function ($g) {
                    return boolval($g['current']);
                });

                if ($sum <= 0) {
                    $guest = $guest->map(function ($g, $key) use ($today) {
                        $date = Carbon::parse($g['to']);
                        if (0 == $key && $today->lte($date)) {
                            $g['current'] = true;
                        }

                        return $g;
                    });
                }

                return $guest;
            })->toArray();

            return $b;
        });
    }

    protected function filterGuestWithMultipleRooms($array)
    {
        $data = collect($array);

        return $data->filter(function ($b, $id) {
            $guests = collect($b['guests']);
            $b['guests'] = $guests->filter(function ($guest, $key) {
                return count($guest) > 1;
            });

            return count($b['guests']) >= 1;
        })->map(function ($b, $id) {
            $guests = collect($b['guests']);
            $b['guests'] = $guests->filter(function ($guest, $key) {
                return count($guest) > 1;
            });

            return $b;
        });
    }

    protected function filterGuestWithLastMove($array)
    {
        if (count($array) <= 0) {
            return [];
        }

        return $array->filter(function ($b, $id) {
            $guests = collect($b['guests']);
            $b['guests'] = $guests->filter(function ($guest, $key) {
                $total_rooms = count($guest) - 1;

                // eliminate booking with room move at the last index
                return !$guest[$total_rooms]['current'];
            });

            return count($b['guests']) >= 1;
        });
    }

    protected function sortGuestByCheckOut($array)
    {
        if (count($array) <= 0) {
            return [];
        }

        $data = collect($array);

        return $data->map(function ($guest, $id) {
            $guests = collect($guest['guests']);
            $guest['guests'] = $guests->map(function ($g, $i) {
                $moves = collect($g);

                return $moves->sortBy(function ($move, $key) {
                    return strtotime($move['to']);
                })->values()->all();
            });

            return $guest;
        });
    }

    protected function finalizeRoomMove($array, $today)
    {
        if (count($array) <= 0) {
            return [];
        }

        $today = Carbon::createFromFormat('Y-m-d', $today);

        $array = $array->map(function ($b, $id) {
            $guests = $b['guests'];
            $b['moves'] = [];

            foreach ($guests as $guest_id => $guest_data) {
                foreach ($guest_data as $key => $guest) {
                    if ($guest['current']) {
                        $next_key = intval($key) + 1;
                        if (isset($guest_data[$next_key])) {
                            $next_from = Carbon::parse($guest_data[$next_key]['from']);
                            $date_to = Carbon::parse($guest['to']);
                            if ($date_to->lte($next_from)) {
                                array_push($b['moves'], [
                                    'guest' => $guest['name'],
                                    'room' => $guest['room'],
                                    'subroom' => $guest['subroom'],
                                    'from' => Carbon::parse($guest['from'])->format('d.m.Y'),
                                    'to' => $date_to->format('d.m.Y'),
                                    'next_room' => $guest_data[$next_key]['room'],
                                    'next_subroom' => $guest_data[$next_key]['subroom'],
                                    'next_from' => $next_from->format('d.m.Y'),
                                    'next_to' => Carbon::parse($guest_data[$next_key]['to'])->format('d.m.Y'),
                                ]);
                            }
                        }
                    }
                }
            }

            return $b;
        });

        $result = [];

        foreach ($array as $booking) {
            $moves = $booking['moves'];

            foreach ($moves as $move) {
                $out = Carbon::parse($move['to'])->addHours(23)->addMinutes(59)->addSeconds(59);

                if ($today->lte($out) && ($move['subroom'] != $move['next_subroom'])) {
                    array_push($result, [
                        'id' => $booking['id'],
                        'ref' => $booking['ref'],
                        'guest' => $move['guest'],
                        'room' => $move['room'],
                        'camp' => $booking['camp'],
                        'subroom' => $move['subroom'],
                        'from' => $move['from'],
                        'to' => $move['to'],
                        'sort' => strtotime(Carbon::parse($move['to'])->format('Y-m-d H:i:s')),
                        'next_room' => $move['next_room'],
                        'next_subroom' => $move['next_subroom'],
                        'next_from' => $move['next_from'],
                        'next_to' => $move['next_to'],
                    ]);
                }
            }
        }

        return collect($result)->sortBy('to')->toArray();
    }
}
