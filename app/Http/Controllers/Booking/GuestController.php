<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Actions\GenerateGuestIds;
use App\Models\Booking\Booking;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingChecklist;
use App\Models\Booking\Guest;
use App\Services\Booking\BookingService;
use App\Services\Booking\InventoryService;
use App\Services\Booking\UserService;
use App\Mail\Booking\Checklist;

use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GuestController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $inventoryService;

    public function __construct(BookingService $bookingService, UserService $userService, InventoryService $inventoryService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $bs = $this->bookingService;

        $guests = Guest::with(['bookings.booking'])
            ->whereHas('bookings.booking.guest', function ($q) {
                $q->whereNotNull('id');
            })
            ->where('email', 'NOT LIKE', '%@guest.%')
            ->where('email', 'NOT LIKE', '%@test.%')
            ->withCount(['bookings'])
            ->orderBy('bookings_count', 'desc');

        if ($this->userService->is_agent()) {
            $guests = $guests->where('agent_id', auth()->user()->id);
        }

        $guests = $guests->paginate(50);

        return view('Booking.guests.index', compact('guests', 'bs'));
    }

    public function show($id)
    {
        $guest = Guest::with(['bookings.booking'])
            ->whereHas('bookings.booking.guest', function ($q) {
                $q->whereNotNull('id');
            })
            ->withCount(['bookings']);

        if ($this->userService->is_agent()) {
            $guest = $guest->where('agent_id', auth()->user()->id);
        }

        $guest = $guest->findOrFail($id);

        return view('Booking.guests.show', compact('guest'));
    }

    public function quickSearch()
    {
        $q = request('q');
        $page = request('page');
        $ref = request()->has('ref') ? request('ref') : null;
        $exclude = collect([]);

        if ($ref) {
            $booking = Booking::with(['guests'])->where('ref', $ref)->first();
            if ($booking->guests->count() > 0) {
                $exclude = $booking->guests->pluck('guest_id');
            }
        }

        $guests = Guest::query()
            ->where(function ($query) use ($q) {
                $query
                    ->where('fname', 'LIKE', '%'.$q.'%')
                    ->orWhere('lname', 'LIKE', '%'.$q.'%')
                    ->orWhere('email', 'LIKE', '%'.$q.'%')
                    ->orWhere('country', 'LIKE', '%'.$q.'%')
                    ->orWhere('company', 'LIKE', '%'.$q.'%')
                    ->orWhere('city', 'LIKE', '%'.$q.'%');

                if (intval($q) > 0) {
                    $query->orWhere('client_id', intval($q));
                }
            });

        if ($this->userService->is_agent()) {
            $guests = $guests->where('agent_id', auth()->user()->id);
        }

        $guests = $guests->get(['id', 'fname', 'lname', 'email']);

        $results = [
            'results' => [],
        ];

        if ($guests) {
            foreach ($guests as $guest) {
                if (!$exclude->containsStrict($guest->id)) {
                    array_push($results['results'], [
                        'id' => $guest->email,
                        'text' => $guest->title.' '.$guest->full_name.' - '.$guest->email,
                        'guest_id' => $guest->id
                    ]);
                }
            }
        }

        return response()->json($results);
    }

    public function checkGuest($type, $ref)
    {
        $col = 'check-in' == $type ? 'in_list_parsed' : 'out_list_parsed';
        $col_date = 'check-in' == $type ? 'check_in' : 'check_out';

        $booking = Booking::overview()
            ->where('ref', $ref)
            ->withCount(['rooms'])
            ->first()
        ;

        $role = $this->userService->user()->role_id;

        return view('Booking.guests.check', compact('booking', 'type', 'ref', 'role', 'col', 'col_date'));
    }

    public function updateCheckGuest($type, $ref)
    {
        $booking = Booking::overview()
            ->where('ref', $ref)
            ->first()
        ;

        if (!request()->has('checklist')) {
            return redirect(Route('tenant.guests.checkGuest', [ 'type' => $type, 'ref' => $ref ]));
        }

        DB::beginTransaction();

        $checklists = request('checklist');

        $col = 'check-in' == $type ? 'in_list' : 'out_list';
        $col_date = 'check-in' == $type ? 'check_in' : 'check_out';

        foreach ($checklists as $room_id => $list) {
            $data = [];
            foreach ($list as $name => $state) {
                array_push($data, $name);
            }

            $data = json_encode($data);

            if ('check-out' == $type && 'kima_bottle' == $name) {
                $bottle = BookingAddon::where('booking_room_id', $room_id)->where('extra_id', 6)->count();
                if ($bottle > 0) {
                    // send transaction to inventory
                    $this->inventoryService->registerItemTransaction($booking, 'kima_bottle');
                }
            }

            BookingChecklist::updateOrCreate([
                'booking_id' => $booking->id,
                'booking_room_id' => $room_id,
            ], [
                $col => $data,
                $col_date => date('Y-m-d H:i:s'),
                'user_id' => Auth::user()->id,
                'notes' => '',
            ]);
        }

        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => Auth::user()->id,
            'action' => 'Guest '.('check-in' == $type ? 'checked in' : 'checked out'),
            'info_type' => 'info',
            'details' => '<b>Guest</b> has '.('check-in' == $type ? 'checked in' : 'checked out'),
            'ip_address' => request()->ip(),
        ]);

        // send email
        Mail::to('info@kimasurf.com', 'KIMA SURF')->send(new Checklist($booking, $type));

        DB::commit();

        return redirect('guests/'.$type.'/'.$ref);
    }

    /**
     * Generate id for all existing guests.
     *
     * @param  \App\Actions\GenerateGuestIds  $generateGuestIds
     * @return \Illuminate\Http\Response
     */
    public function generateIds(GenerateGuestIds $generateGuestIds)
    {
        $generateGuestIds->handle();

        return redirect()->route('tenant.guests');
    }
}
