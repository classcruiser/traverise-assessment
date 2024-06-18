<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\Booking\UserService;
use App\Models\Booking\Location;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingRoom;
use App\Models\Booking\Room;
use App\Models\Booking\AgentRoom;

class CalendarController extends Controller
{
    protected $userService;
    protected $role;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list()
    {
        $camp = Location::orderBy('name', 'asc')->whereIn('id', Auth::user()->allowed_camps_decoded)->first();

        return redirect('calendar/' . $camp->id);
    }

    public function index($id)
    {
        $location_id = $id;
        $is_agent = $this->userService->is_agent();

        $dates = request()->has('start_date_y') ? [
            request('start_date_y') . '-' . request('start_date_m'),
            request('end_date_y') . '-' . request('end_date_m'),
        ] : [
            Carbon::now()->format('Y-m'),
            Carbon::now()->addMonth(1)->format('Y-m')
        ];

        $total_inhouse = [];

        $tmp_1 = explode('-', $dates[0]);
        $start_date_y = $tmp_1[0];
        $start_date_m = $tmp_1[1];

        $tmp_2 = explode('-', $dates[1]);
        $end_date_y = $tmp_2[0];
        $end_date_m = $tmp_2[1];

        $first_date = Carbon::createFromFormat('Y-m-d', $start_date_y . '-' . $start_date_m . '-01')->firstOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date_y . '-' . $end_date_m . '-01')->lastOfMonth()->format('Y-m-d');

        $calendar = $this->generateDates($dates);

        // agent, filter the room
        $agent_rooms = $is_agent ? $this->userService->user()->rooms()->pluck('room_id')->toArray() : [];

        $camps = Location::orderBy('name', 'asc')->whereIn('id', $this->userService->user()->allowed_camps_decoded)->get();

        $rooms = Room::with(['rooms' => function ($q) use ($agent_rooms) {
            if (count($agent_rooms) > 0) {
                $q->whereIn('id', $agent_rooms);
            }
        }, 'prices' => function ($q) use ($first_date, $end_date) {
            $q
                ->where('date', '>=', $first_date)
                ->where('date', '<=', $end_date)
                ->where('season_type', 'BLOCK')
                ->select('room_id', 'date', 'season_type');
        }])
            ->where('calendar_visibility', true)
            ->where('location_id', $location_id)
            ->orderBy('sort', 'asc')
            ->get();

        $location = Location::where('id', $id)->first(['id', 'name']);

        $total_months = (count($calendar['months']) - 1);

        $first_month = $calendar['months'][0];
        $second_month = $calendar['months'][$total_months];

        $check_in = $calendar['days'][$first_month][0];
        $check_out = $calendar['days'][$second_month][count($calendar['days'][$second_month]) - 1];

        $cal_bookings = BookingRoom::with(['booking.guest.details', 'mainGuest.details.details.activeBookings', 'mainGuest.details.details', 'booking.other_guests.details', 'booking.payment', 'subroom', 'guests.details.details'])
            ->whereHas('booking', function ($q) use ($id, $is_agent) {
                $q
                    ->where('location_id', $id)
                    ->where(function ($subq) {
                        $subq
                            ->where('status', 'CONFIRMED')
                            ->orWhere('status', 'RESERVED')
                            ->orWhere('status', 'DRAFT');
                    });
            })
            ->whereHas('mainGuest.details.details', function ($q) {
                $q->whereNotNull('id');
            })
            ->where('to', '>=', $check_in)
            ->where('from', '<', $check_out)
            ->get();

        $pricing_calendars = $rooms->mapWithKeys(function ($room) {
            $prices = $room->prices->mapWithKeys(fn ($price) => [$price->date => $price->season_type]);
            return [$room->id => $prices];
        })->toArray();

        if ($cal_bookings) {
            $cals = [];
            $nights_offset = [];
            // try to break down by date
            foreach ($cal_bookings as $booking) {
                $subroom = $booking->subroom_id;
                $bed = $booking->bed;
                $booking_state = $booking->booking->status;
                $total_bookings = $booking->mainGuest->details->details->activeBookings->count();

                $assigned_guests = auth()->user()->hasPermissionTo('view booking') ? $booking->mainGuest->details->details->full_name : '--';
                if ($is_agent && (is_null($booking->booking->agent_id) || $booking->booking->agent_id != auth()->user()->id)) {
                    $assigned_guests = '';
                }
                $room_capacity = $booking->subroom->beds;
                $private_booking = $booking->is_private ? '<br />Private booking <i class="fa fa-lock"></i>' : '';
                $cell_height = $booking->is_private ? $room_capacity : 1;
                $ref = $booking->booking->ref;
                $state = $booking->booking->payment?->status;
                $nights = $booking->nights;
                $booking_id = $booking->id;
                $is_agent = $booking->booking->agent_id ? true : false;
                $is_external = 0;
                $gender = 'cell-male';

                $cals[$subroom] = $cals[$subroom] ?? [];
                $cals[$subroom][$bed] = $cals[$subroom][$bed] ?? [];
                $nights_offset[$booking_id] = $nights_offset[$booking_id] ?? $nights;

                $period = CarbonPeriod::create($booking->from->format('Y-m-d'), $booking->to->format('Y-m-d'), CarbonPeriod::EXCLUDE_END_DATE);

                foreach ($period as $index => $booking_date) {
                    $date_key = $booking_date->format('Y-m-d');
                    $cals[$subroom][$bed][$booking_id]['first'] = 1;

                    $cals[$subroom][$bed][$date_key] = [
                        'is_first' => $index == 0,
                        'is_last' => $period->count() == (intVal($index) + 1),
                        'guest' => $booking->mainGuest->booking_guest_id,
                        'assigned_guests' => $assigned_guests,
                        'room_capacity' => $room_capacity,
                        'private_booking' => $private_booking,
                        'is_external' => $is_external,
                        'is_private' => ($private_booking != ''),
                        'is_agent' => $is_agent,
                        'cell_height' => $cell_height,
                        'ref' => $ref,
                        'booking_id' => $booking->booking->id,
                        'booking_state' => $booking->booking->status_badge,
                        'state' => $state,
                        'booking_status' => $booking->booking->status,
                        'nights' => $nights,
                        'nights_offset' => $nights_offset[$booking_id],
                        'id' => $booking_id,
                        'gender' => $gender
                    ];

                    $nights_offset[$booking_id] -= 1;
                }
            }
        }

        return view('Booking.calendars.index', compact(
            'calendar',
            'cals',
            'rooms',
            'location_id',
            'id',
            'start_date_y',
            'end_date_y',
            'start_date_m',
            'end_date_m',
            'location',
            'total_inhouse',
            'camps',
            'pricing_calendars'
        ));
    }

    protected function generateDates($dates)
    {
        $first = new Carbon($dates[0]);
        $second = new Carbon($dates[1]);

        $start = $first->startOfMonth();
        $finish = $second->endOfMonth();

        $dates = [];
        $months = [];
        $total = 0;
        $period = CarbonPeriod::create($start, $finish);
        foreach ($period as $date) {
            $m = $date->format('M');
            if (!isset($dates[$m])) {
                $dates[$m] = [];
                array_push($months, $m);
            }
            array_push($dates[$date->format('M')], $date->format('Y-m-d'));
            $total += 1;
        }

        return [
            'months' => $months,
            'days' => $dates,
            'length' => $total
        ];
    }
}
