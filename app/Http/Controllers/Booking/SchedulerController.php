<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Services\Booking\BookingService;
use App\Services\Booking\RoomService;
use App\Services\Booking\UserService;
use Carbon\Carbon;

class SchedulerController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $roomService;
    protected $key;

    public function __construct(BookingService $bookingService, UserService $userService, RoomService $roomService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->roomService = $roomService;
        $this->key = '5d103a663d28ddc9aa2af5f7b958e52c';
    }

    /**
     * CHECK RESERVED BOOKINGS AND UPDATE TO ABANDONED IF MORE THAN 15 MINS.
     */
    public function checkReservedBookings()
    {
        if (!$this->validateKey(request('key'))) {
            return response('ERROR', 401);
        }

        $bookings = Booking::where('status', 'RESERVED')->get(['id', 'status']);

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->created_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > 15) {
                $booking->update([
                    'status' => 'ABANDONED',
                ]);
                $booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'danger',
                    'action' => 'Abandon booking',
                    'details' => 'System update the booking to ABANDONED because guest did not confirmed the booking in time.',
                    'ip_address' => request()->ip(),
                ]);
            }
        }

        return response('OK');
    }

    /**
     * CHECK EXPIRED DRAFT BOOKINGS AND SET TO EXPIRE.
     */
    public function checkDraftBookings()
    {
        if (!$this->validateKey(request('key'))) {
            return response('ERROR', 401);
        }

        $bookings = Booking::where('status', 'DRAFT')
            ->where('expiry', '<', date('Y-m-d 00:00:01'))
            ->update([
                'status' => 'EXPIRED',
            ])
        ;

        return response('OK');
    }

    protected function validateKey($key)
    {
        return $key == $this->key;
    }
}
