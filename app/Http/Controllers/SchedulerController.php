<?php

namespace App\Http\Controllers;

use App\Models\Booking\Booking;
use App\Models\Classes\ClassBooking;
use App\Models\Classes\ClassMultiPassPayment;
use App\Services\Booking\BookingService;
use App\Services\Booking\RoomService;
use App\Services\Booking\UserService;
use App\Services\Classes\ClassBookingService;
use App\Services\Classes\MultiPassService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SchedulerController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $roomService;
    protected $key;
    protected $ips;

    public function __construct(BookingService $bookingService, UserService $userService, RoomService $roomService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->roomService = $roomService;
        $this->key = '034bd02a-993c-4557-a1ab-b94bb1a71c89';

        // only allow access from this ip addresses
        $this->ips = ['178.62.239.205'];
    }

    protected function validateRequest()
    {
        if (strtoupper(config('app.env')) != 'PRODUCTION') {
            return;
        }

        if (request('key') != $this->key) {
            abort(403);
        }

        return;
    }

    public function index()
    {
        $this->validateRequest();

        // run maintenance functions ..
        $this->checkReservedBookings();
        $this->checkDraftBookings();
        $this->checkReservedMultiPassBookings();
        $this->checkAbandonedMultiPassBookings();
        $this->checkReservedClassBookings();
        $this->checkAbandonedClassBookings();
        $this->checkUnpaidBookings();

        return 'OK';
    }

    /**
     * CHECK RESERVED BOOKINGS AND UPDATE TO ABANDONED IF MORE THAN 15 MINS.
     */
    public function checkReservedBookings()
    {
        $bookings = Booking::where('status', 'RESERVED')->get(['id', 'status', 'created_at']);

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
     * CHECK RESERVED BOOKINGS AND UPDATE TO ABANDONED IF MORE THAN 15 MINS.
     */
    public function checkReservedMultiPassBookings()
    {
        $bookings = ClassMultiPassPayment::where('status', 'RESERVED')->get(['id', 'status', 'created_at']);

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->created_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > 15) {
                $booking->update([
                    'status' => 'ABANDONED',
                ]);
            }
        }

        return response('OK');
    }

    /**
     * CHECK ABANDONED BOOKINGS AND DELETE IF MORE THAN 2 HOURS
     */
    public function checkAbandonedMultiPassBookings()
    {
        $bookings = ClassMultiPassPayment::with('guest')->where('status', 'ABANDONED')->get();

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->updated_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > 120) {
                MultiPassService::sendAbandonedBooking($booking);

                $booking->delete();
            }
        }

        return response('OK');
    }

    /**
     * CHECK RESERVED BOOKINGS AND UPDATE TO ABANDONED IF MORE THAN 15 MINS.
     */
    public function checkReservedClassBookings()
    {
        $bookings = ClassBooking::with('payment')->where('status', 'RESERVED')->get(['id', 'status', 'created_at']);

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->created_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > 10) {
                $booking->update([
                    'status' => 'ABANDONED',
                ]);
            }
        }

        return response('OK');
    }

    public function checkAbandonedClassBookings()
    {
        $bookings = ClassBooking::with(['payment', 'guest', 'sessions'])->where('status', 'ABANDONED')->get();

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->updated_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > 10) {
                ClassBookingService::sendAbandonedBooking($booking);

                $booking->delete();
            }
        }

        return response('OK');
    }

    /**
     * CHECK EXPIRED DRAFT BOOKINGS AND SET TO EXPIRE.
     */
    public function checkDraftBookings()
    {
        $bookings = Booking::where('status', 'DRAFT')
            ->where('expiry', '<', date('Y-m-d 00:00:01'))
            ->update([
                'status' => 'EXPIRED',
            ]);

        return response('OK');
    }

    public function checkUnpaidBookings()
    {
        $bookings = Booking::where('status', 'CONFIRMED')
            ->where('source_type', 'Guest')
            ->where('channel', 'Online')
            ->with(['tenant', 'profile', 'payment'])
            ->whereHas('profile', function ($query) {
                $query->where('unpaid_booking_deletion_in', '>', 0);
            })
            ->whereDoesntHave('payment.records')
            ->withCount(['payment_records'])
            ->get(['id', 'status', 'created_at']);

        foreach ($bookings as $booking) {
            $start = new Carbon($booking->created_at);
            $now = new Carbon();

            if ($now->diffInMinutes($start) > ($booking->profile->unpaid_booking_deletion_in * 60)) {
                $booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'danger',
                    'action' => 'Delete booking',
                    'details' => 'System deletes the booking because it is in unpaid state for more than 24 hours',
                    'ip_address' => request()->ip(),
                ]);

                $booking->delete();
            }
        }

        return response('OK');
    }
}
