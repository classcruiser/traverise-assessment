<?php

namespace App\Http\Controllers\Booking;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Booking\User;
use Illuminate\Http\Request;
use App\Models\Booking\Extra;
use App\Models\Booking\Guest;
use App\Models\Booking\Booking;
use App\Models\Booking\Profile;
use Illuminate\Validation\Rule;
use App\Models\Booking\Discount;
use App\Models\Booking\Location;
use Illuminate\Support\Facades\DB;
use App\Models\Booking\BookingNote;
use App\Models\Booking\BookingRoom;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingGuest;
use App\Services\Booking\TaxService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Booking\Questionnaire;
use App\Models\Booking\TransferExtra;
use App\Services\Booking\MailService;
use App\Services\Booking\UserService;
use Illuminate\Support\Facades\Cache;
use App\Exports\Booking\BookingsExport;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\PaymentTransfer;
use Illuminate\Support\Facades\Storage;
use App\Exports\Booking\CancelledExport;
use App\Services\Booking\BookingService;
use App\Services\Booking\PaymentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking\BookingRoomDiscount;
use App\Services\Booking\AutomatedEmailService;
use App\Http\Requests\Booking\AdminGuestProfile;
use App\Http\Requests\Booking\SendBookingLinkRequest;

class BookingController extends Controller
{
    protected $bookingService;
    protected $userService;
    protected $paymentService;
    protected $surfplanner;

    public function __construct(BookingService $bookingService, UserService $userService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
        $this->surfplanner = env('SURFPLANNER_URL');
    }

    /**
     * BOOKING INDEX PAGE.
     *
     * @param object $request
     *
     * @return Illuminate\Http\View
     */
    public function index(Request $request)
    {
        $locations = Location::with(['rooms'])->orderBy('name', 'asc')->get();

        $users = User::orderBy('name')->get();

        if ($request->has('export')) {
            $bookings = Booking::with(['location', 'specialPackage', 'guest', 'guests.details', 'guests.rooms.room.subroom', 'guests.rooms.room.addons.details', 'rooms.room', 'rooms.subroom', 'payment.records.user'])
                ->whereHas('guest.details', function ($q) {
                    $q->whereNotNull('id');
                })
                ->withCount(['guests']);
        } else {
            $bookings = Booking::with(['location', 'specialPackage', 'guest.details', 'guests', 'rooms.room', 'rooms.subroom', 'payment'])
                ->whereHas('guest.details', function ($q) {
                    $q->whereNotNull('id');
                })
                ->withCount(['guests']);
        }

        if ($request->has('ref') && '' != $request->ref) {
            $bookings->where('ref', $request->ref);
        }

        // search here
        $bookings = $this->bookingService->filterBookings($bookings, $request);

        if ($this->userService->is_agent()) {
            $bookings->where(function ($q) {
                $q
                    ->where('source_id', $this->userService->user()->id)
                    ->orWhere('agent_id', $this->userService->user()->id)
                ;
            });
        }

        if ($request->has('export')) {
            if (!auth()->user()->can('export bookings')) {
                return redirect(route('tenant.bookings'));
            }

            $date = date('ymd_His');
            $questionnaires = Questionnaire::pluck('title');

            return Excel::download(
                new BookingsExport($bookings, $this->bookingService, $questionnaires),
                'SEARCH_RESULT_' . $date . '.xlsx'
            );
        }

        $bookings = $bookings->orderBy('created_at', 'desc')
            ->paginate(25);

        $sortDirection = $request->has('dir') ? ('asc' == $request->dir ? 'desc' : 'asc') : 'asc';

        $countries = Cache::remember('countries', 3600, function () {
            return DB::table('country_codes')->orderBy('country_name', 'asc')->get();
        });

        $addons = Extra::orderBy('name', 'asc')->get(['id', 'name']);

        $role = auth()->user()->role_id;

        return view('Booking.bookings.index', compact(
            'bookings', 'locations', 'users', 'sortDirection', 'countries', 'role', 'addons'
        ));
    }

    /**
     * BOOKING DRAFT PAGE.
     *
     * @param object $request
     *
     * @return Illuminate\Http\View
     */
    public function draft(Request $request)
    {
        $bookings = Booking::with(['location', 'guest.details', 'guests', 'guest.groups', 'rooms.room', 'rooms.subroom'])
            ->where(function ($q) {
                $q->where('status', 'DRAFT')
                    ->orWhere('status', 'EXPIRED')
          ;
            })
            ->where(function ($q) {
                $q
                    ->whereIn('location_id', json_decode($this->userService->user()->allowed_camps, true))
                    ->orWhere('location_id', null);
            })
            ->orderBy('created_at', 'desc')
            ->withCount(['guests']);

        // is agent ?
        if ($this->userService->is_agent()) {
            $bookings = $bookings->where(function ($q) {
                $q->where('source_id', $this->userService->user()->id)->orWhere('agent_id', $this->userService->user()->id);
            });
        }

        $bookings = $bookings->paginate(25);

        return view('Booking.bookings.draft', compact('bookings'));
    }

    /**
     * BOOKING PENDING/ABANOONED/RESERVED PAGE.
     *
     * @param object $request
     *
     * @return Illuminate\Http\View
     */
    public function pending(Request $request)
    {
        $bookings = Booking::with(['location', 'specialPackage', 'guest.details', 'guests', 'guest.groups', 'rooms.room', 'rooms.subroom'])
            ->where(function ($q) {
                $q->where('status', 'PENDING')
                    ->orWhere('status', 'ABANDONED')
                    ->orWhere('status', 'RESERVED');
            })
            ->whereHas('guest', function ($q) {
                $q->whereNotNull('id');
            })
            ->whereIn('location_id', json_decode($this->userService->user()->allowed_camps, true))
            ->orderBy('created_at', 'desc')
            ->withCount(['guests']);

        if ($this->userService->is_agent()) {
            $bookings = $bookings->where(function ($q) {
                $q->where('source_id', $this->userService->user()->id)->orWhere('agent_id', $this->userService->user()->id);
            });
        }

        $bookings = $bookings->paginate(25);

        return view('Booking.bookings.pending', compact('bookings'));
    }

    /**
     * BOOKING TRASH.
     *
     * @param object $request
     *
     * @return Illuminate\Http\View
     */
    public function trash(Request $request)
    {
        $bookings = Booking::withTrashed()
            ->with(['location', 'specialPackage', 'guest.details', 'histories', 'guests', 'guest.groups', 'rooms.room', 'rooms.subroom'])
            ->where(function ($q) {
                $q
                    ->where('status', 'CANCELLED')
                    ->orWhere('deleted_at', '!=', null);
            })
            /*
            ->whereHas('histories', function ($q) use ($request) {
                $sub = $q->where('action', 'Cancel booking');
                if ($request->has('cancel_date_start')) {
                    $cancel_date_start = Carbon::createFromFormat('d.m.Y', $request->cancel_date_start)->format('Y-m-d');
                    $sub = $sub->where('created_at', '>=', $cancel_date_start.' 00:00:00');
                }
                if ($request->has('cancel_date_end')) {
                    $cancel_date_end = Carbon::createFromFormat('d.m.Y', $request->cancel_date_end)->format('Y-m-d');
                    $sub = $sub->where('created_at', '<=', $cancel_date_end.' 23:59:59');
                }

                return $sub;
            })*/
            ->whereIn('location_id', json_decode($this->userService->user()->allowed_camps, true))
            ->orderBy('created_at', 'desc')
            ->withCount(['guests']);

        if ($request->has('ref') && '' != $request->ref) {
            $bookings->where(function ($q) use ($request) {
                $q->where('ref', $request->ref);
            });
        }

        // search here
        $bookings = $this->bookingService->filterBookings($bookings, $request, true);

        if ($this->userService->is_agent()) {
            $bookings = $bookings->where(function ($q) {
                $q->where('source_id', $this->userService->user()->id)->orWhere('agent_id', $this->userService->user()->id);
            });
        }

        if ($request->has('export')) {
            $date = date('ymd_His');

            return Excel::download(new CancelledExport($bookings, $this->userService->user()->role_id), 'DELETED_RESULT_'.$date.'.xlsx');
        }

        $bookings = $bookings->paginate(25);

        return view('Booking.bookings.trash', compact('bookings'));
    }

    /**
     * DOWNLOAD PDF INVOICE.
     *
     * @param mixed $ref
     *
     * @return Illuminate\Http\Redirect
     */
    public function downloadPDFInvoice($ref)
    {
        $pdf = $this->bookingService->preparePDFInvoice($ref);

        if (request()->has('preview')) {
            return $pdf;
        }

        return $pdf['pdf']->download($pdf['filename']);
    }

    public function downloadInvoicePayment(string $ref, PaymentTransfer $payment_transfer, int $index)
    {
        $pdf = $this->bookingService->preparePDFInvoice(ref: $ref, payment_transfer: $payment_transfer, invoice_number: $index, is_final: false);

        if (request()->has('preview')) {
            return $pdf;
        }

        return $pdf['pdf']->download($pdf['filename']);
    }

    /**
     * CREATE NEW BOOKING.
     *
     * @return Illuminate\Http\Redirect
     */
    public function create()
    {
        $ref = $this->bookingService->generateBookingRef();
        $is_agent = $this->userService->is_agent();

        // create draft booking
        $booking = Booking::create([
            'ref' => $ref,
            'source_type' => $is_agent ? 'Agent' : 'user',
            'channel' => 'Dashboard',
            'opportunity' => 'Sale',
            'source_id' => auth()->user()->id,
            'expiry' => '2090-12-31 23:59:59',
            'expire_at' => 99,
            'check_in' => Carbon::now(),
            'check_out' => Carbon::now()->addHours(7),
            'voucher' => null,
            'origin' => request()->getHost(),
            'agent_id' => $is_agent ? auth()->user()->id : null,
            'tax_visible' => 1,
        ]);

        // history
        $booking->histories()->create([
            'booking_id' => $booking->id,
            'user_id' => auth()->user()->id,
            'action' => 'Create new draft booking',
            'info_type' => 'info',
            'details' => '<b>' . auth()->user()->name . '</b> created new draft booking #<b>' . $booking->ref . '</b>',
            'ip_address' => request()->ip(),
        ]);

        $this->paymentService->createPayment($booking);

        return redirect(route('tenant.bookings.newGuest', [ 'ref' => $booking->ref ]));
    }

    /**
     * SHOW BOOKING OVERVIEW.
     *
     * @param int $id
     *
     * @return Illuminate\Http\View
     */
    public function show($ref)
    {
        $booking = Booking::with([
            'rooms.subroom', 'rooms.addons.details.questionnaire.type',
            'rooms.addons.details.questionnaire.answers',
            'rooms.discounts.offer', 'guests', 'cancellation',
            'guest', 'transfers.details', 'discounts', 'histories', 'payment.records.user',
        ])
            ->withTrashed()
            ->withCount(['rooms', 'transfers', 'guests', 'payment_records'])
            ->where('ref', $ref)
            ->firstOrFail();

        $agents = $this->userService->getAgentList();

        if (request()->has('createQRCode')) {
            $booking->createQRCode();
        }

        if ($this->userService->is_agent() && (is_null($booking->agent_id) || $booking->agent_id != auth()->user()->id)) {
            return redirect(route('tenant.bookings'));
        }

        if (request()->has('mail')) {
            return (new \App\Mail\Booking\OrderConfirmed($booking))->render();
        }

        if (request()->has('pmail')) {
            $payment = ($booking->payment->records->first());
            return (new \App\Mail\Booking\PaymentConfirmed($payment->payment, $payment))->render();
        }

        $role = 1;

        $tax = TaxService::getActiveTaxes($booking);

        $booking_taxes = TaxService::calculateBookingTaxes($tax, $booking);

        $loc = Location::with(['rooms'])->find($booking->location_id);

        $rooms = $loc ? $loc->rooms()->pluck('id')->toArray() : [];

        $transfers = TransferExtra::whereHas('rooms', fn ($q) => $q->whereIn('room_id', $rooms))->get();

        $is_deleted = !is_null($booking->deleted_at);

        $tax_totals = [];

        return view('Booking.bookings.show', compact(
            'booking', 'role', 'agents', 'is_deleted', 'tax', 'tax_totals', 'booking_taxes', 'transfers'
        ));
    }

    public function previewEmail(string $ref)
    {
        $booking = Booking::with([
            'rooms.subroom', 'rooms.addons.details.questionnaire.type',
            'rooms.addons.details.questionnaire.answers',
            'rooms.discounts.offer', 'guests', 'cancellation',
            'guest', 'transfers.details', 'discounts', 'histories', 'payment.records.user',
        ])
            ->withTrashed()
            ->withCount(['rooms', 'transfers', 'guests', 'payment_records'])
            ->where('ref', $ref)
            ->firstOrFail();

        return new \App\Mail\Booking\OrderConfirmed($booking);
    }

    public function details($ref)
    {
        $temp = explode('-', $ref);
        $booking_ref = $temp[0] . '-' . $temp[1] . '-' . $temp[2];
        $booking_id = $temp[3];

        $booking = Booking::with([
            'rooms.subroom', 'rooms.addons.details', 'rooms.discounts.offer', 'guests',
            'guests.rooms', 'guests.rooms.room', 'guests.rooms.room.subroom', 'guests.rooms.room.addons',
            'surf_planner_users', 'guest', 'transfers.details', 'discounts', 'histories', 'payment.records.user',
        ])
            ->withCount(['rooms', 'transfers', 'guests'])
            ->where('ref', $booking_ref)
            ->where('id', $booking_id)
            ->firstOrFail();

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $has_checked_in = false;

        if ($booking->payment->status != 'COMPLETED') {
            $booking->update([
                'has_check_in' => true,
                'checked_in_at' => now()
            ]);

            $has_checked_in = $booking->has_check_in && !is_null($booking->checked_in_at);
        }

        if (request()->ajax()) {
            $allowedStatus = ['COMPLETED', 'PARTIAL'];
            if (! $booking->check_in->lessThanOrEqualTo(today()) || !in_array($booking->booking_status, $allowedStatus)) {
                $booking = null;
            }

            if (request()->has('checkin')) {
                $html = $this->bookingService->generateCheckinDetailHTML($booking);
            }

            if (request()->has('addons')) {
                $html = $this->bookingService->generateAddonDetailHTML($booking);
            }

            return response($html);
        }

        return view('Booking.bookings.details', [
            'ref' => $ref,
            'booking' => $booking,
            'profile' => $profile,
            'has_checked_in' => $has_checked_in,
        ]);
    }

    /**
     * DELETE BOOKING.
     *
     * @param int $id
     *
     * @return Illuminate\Http\Redirect
     */
    public function deleteBooking($ref)
    {
        $this->bookingService->deleteBooking($ref);

        $guest = request()->has('guest') ? request('guest') : null;

        if ($guest) {
            return redirect(Route('tenant.guests.show', ['id' => $guest]));
        }

        return redirect()->back();
    }

    /**
     * CANCEL BOOKING.
     *
     * @param string $id
     *
     * @return Illuminate\Http\View
     */
    public function cancelBooking($ref)
    {
        return $this->bookingService->cancelBooking($ref);
    }

    /**
     * UPDATE BOOKING OVERVIEW PRICES.
     *
     * @param string $id
     *
     * @return Illuminate\Http\Redirect
     */
    public function updateBookingPrices($ref, Request $request)
    {
        return $this->bookingService->updateBookingPrices($ref, $request);
    }

    /**
     * NEW GUEST PAGE.
     *
     * @param string $id
     *
     * @return Illuminate\Http\View
     */
    public function newGuest($ref)
    {
        return $this->bookingService->newGuest($ref);
    }

    /**
     * EDIT GUEST PAGE.
     *
     * @param string $id
     * @param int    $booking_guest_id
     *
     * @return Illuminate\Http\View
     */
    public function editGuest($ref, $booking_guest_id)
    {
        return $this->bookingService->editGuest($ref, $booking_guest_id);
    }

    /**
     * UPDATE GUEST.
     *
     * @param string $id
     * @param int    $booking_guest_id
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function updateGuest($ref, $booking_guest_id, AdminGuestProfile $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => [
                'required_unless:is_agent,on',
                //'email' => 'unique:guests,email,17'
                Rule::unique('guests', 'email')
                    ->where(fn (\Illuminate\Database\Query\Builder $query) => $query->where('tenant_id', tenant('id')))
                    ->ignore(intVal(request('guest_id')))
            ]
        ]);

        if ($validated->fails()) {
            return back()->withErrors($validated->messages());
        }

        return $this->bookingService->updateGuest($ref, $booking_guest_id, $request);
    }

    /**
     * REMOVE GUEST.
     *
     * @param string $id
     * @param int    $booking_guest_id
     *
     * @return Illuminate\Http\Redirect
     */
    public function removeGuest($ref, $booking_guest_id)
    {
        return $this->bookingService->removeGuest($ref, $booking_guest_id);
    }

    /**
     * NEW GUEST ROOM.
     *
     * @param string $id
     * @param int    $booking_guest_id
     *
     * @return Illuminate\Http\View
     */
    public function newGuestRoom($ref, $booking_guest_id)
    {
        $role = auth()->user()->role_id;

        $booking = Booking::with(['rooms.subroom', 'guests', 'guest'])->where('ref', $ref)->first();

        //  $allowed_camps = json_decode(auth()->user()->allowed_camps, true);

        //  $locations = Location::whereIn('id', $allowed_camps)->orderBy('name', 'asc')->get();

        $locations = Location::orderBy('name', 'asc')->get();

        if (4 == Auth::user()->role_id) {
            // disable camp id 3 and 4
            $locations = $locations->filter(function ($location) {
                return 3 != $location->id && 4 != $location->id;
            });
        }

        $check_in = Carbon::now()->format('d.m.Y');
        $check_out = Carbon::now()->addDays(7)->format('d.m.Y');

        if ($booking->check_in && $booking->check_out) {
            $check_in = date('d.m.Y', strtotime($booking->check_in));
            $check_out = date('d.m.Y', strtotime($booking->check_out));
        }

        $guest = $booking->guests->where('id', $booking_guest_id)->first()->details;

        $agents = $this->userService->getAgentList();

        $is_deleted = !is_null($booking->deleted_at);

        return view('Booking.bookings.new-guest-room', compact('booking', 'guest', 'booking_guest_id', 'locations', 'check_in', 'check_out', 'role', 'agents', 'is_deleted'));
    }

    /**
     * SAVE GUEST ROOM.
     *
     * @param string $id
     * @param int    $booking_guest_id
     *
     * @return Illuminate\Http\Redirect
     */
    public function saveGuestRoom($ref, $booking_guest_id)
    {
        return $this->bookingService->saveGuestRoom($ref, $booking_guest_id);
    }

    /**
     * EDIT GUEST ROOM.
     *
     * @param string $id
     * @param int    $booking_guest_id
     * @param int    $booking_room_id
     *
     * @return Illuminate\Http\View
     */
    public function editGuestRoom($ref, $booking_guest_id, $booking_room_id)
    {
        return $this->bookingService->editGuestRoom($ref, $booking_guest_id, $booking_room_id);
    }

    /**
     * REPLACE GUEST ROOM.
     *
     * @param string $id
     * @param int    $booking_guest_id
     * @param int    $booking_room_id
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function replaceGuestRoom($ref, $booking_guest_id, $booking_room_id, Request $request)
    {
        return $this->bookingService->replaceGuestRoom($ref, $booking_guest_id, $booking_room_id, $request);
    }

    /**
     * APPROVE BOOKING.
     *
     * @param string $id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function approveBooking($ref, Request $request)
    {
        $response = $this->bookingService->assignBedToRoom($ref);

        if ($response['result'] == 'failed') {
            return response()->json([
                'status' => 'failed',
                'message' => $response['message'],
                'url' => '/'
            ]);
        }

        $booking = Booking::with(['guests.details', 'location'])->where('ref', $ref)->firstOrFail();
        $deposit_due = $booking->location->deposit_due;
        $deposit_date = Carbon::now()->addDays($deposit_due);
        $email = boolval($request->email);

        DB::beginTransaction();

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'success',
            'action' => 'Approving booking',
            'details' => '<b>'.auth()->user()->name.'</b> approved booking. Change status from <b>'.$booking->status.'</b> to <b>CONFIRMED</b>',
            'ip_address' => request()->ip(),
        ]);

        $booking->update([
            'status' => 'CONFIRMED',
            'deposit_expiry' => $deposit_date,
        ]);

        $booking->payment->update([
            'status' => ($booking->payment->total_paid >= $booking->grand_total ? 'COMPLETED' : 'DUE'),
            'deposit_due_date' => $deposit_date,
            'processing_fee' => $booking->processing_fee,
        ]);

        $booking->histories()->create([
            'user_id' => null,
            'info_type' => 'slate',
            'action' => 'Set Due Date',
            'details' => '<b>System</b> set due date to '.$deposit_date->format('d.m.Y'),
            'ip_address' => request()->ip(),
        ]);

        if ('Agent' == $booking->source_type || null != $booking->agent_id) {
            $booking->update([
                'agent_commission' => $booking->commission,
            ]);

            MailService::sendAgentApprovalEmail($booking);
        }

        if ($booking->source_type != 'Agent') {
            try {
                AutomatedEmailService::checkAndSendEmailWhenBookingIsApproved($booking);
            } catch (\Exception $e) {
                Log::error('Cannot send auto email when booking is approved', $e->getMessage());
            }
        }

        if ($email) {
            $booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'Sent booking confirmation email',
                'details' => '<b>System</b> sent booking confirmation email to <b>'.$booking->guest->details->email.'</b>',
                'ip_address' => request()->ip(),
            ]);

            MailService::sendConfirmationEmail($booking);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'url' => '/bookings/'.$booking->ref,
        ]);
    }

    /**
     * RESEND CONFIRMATION EMAIL.
     *
     * @param object $request
     * @param int    $id
     */
    public function resendConfirmationEmail(Request $request)
    {
        $booking = Booking::find($request->id);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Resend booking confirmation email',
            'details' => '<b>'.auth()->user()->name.'</b> resend booking confirmation email to <b>'.$request->email.'</b>',
            'ip_address' => request()->ip(),
        ]);

        MailService::sendConfirmationEmail($booking, $request->email);
    }

    /**
     * SEND BOOKING LINK EMAIL.
     *
     * @param object $request
     */
    public function sendBookingLink(SendBookingLinkRequest $request)
    {
        $validated = $request->validated();
        $booking = Booking::find($validated['booking_id']);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Send payment link',
            'details' => '<b>'.auth()->user()->name.'</b> send booking link to <b>'.$request->email.'</b>',
            'ip_address' => request()->ip(),
        ]);

        MailService::sendBookingLink($booking, $validated['email']);
    }


    /**
     * REMOVE BOOKING ROOM.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function removeBookingRoom(Request $request)
    {
        return $this->bookingService->removeBookingRoom($request);
    }

    public function updateQuestionnaireAnswer(Request $request)
    {
        $this->bookingService->updateQuestionnaireAnswer($request);

        return back();
    }

    /**
     * ADD NEW GUEST TO DATABASE.
     *
     * @param string $ref
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function insertGuest($ref, AdminGuestProfile $request)
    {
        $booking = Booking::with(['rooms.subroom', 'guests', 'guest'])->where('ref', $ref)->first();

        $birthdate = $request->birthdate_year.'-'.$request->birthdate_month.'-'.$request->birthdate_day;

        if (auth()->user()->hasRole('Agent')) {
            $request->merge([
                'email' => $this->generateAgentEmail()
            ]);
        }

        $check = Guest::where('email', $request->email)->count();

        if ($check) {
            return redirect()->back()->withErrors(['email' => 'Email already exists in the system.']);
        }

        $guest = Guest::create($request->only([
            'fname', 'lname', 'title', 'company', 'email', 'phone', 'street', 'zip', 'city', 'country', 'agent_id'
        ]));

        $guest->update([
            'birthdate' => $birthdate,
            'marketing_flag' => 0,
        ]);

        $group_id = 0;

        // add this guest to booking_guests table
        if ($booking->guest) {
            // set as main guest
            $group_id = $booking->guest->id;
        }

        $booking_guest = BookingGuest::create([
            'guest_id' => $guest->id,
            'booking_id' => $booking->id,
            'group_id' => $group_id,
        ]);

        return redirect(route('tenant.bookings.newGuestRoom', [ 'ref' => $booking->ref, 'booking_guest_id' => $booking_guest->id ]));
    }

    protected function generateAgentEmail() : string
    {
        $agent_email = explode('@', auth()->user()->email);

        $domain = $agent_email[1];

        return 'guest'. time() .'@'. $domain;
    }

    /**
     * REPLACE BOOKING ROOM'S GUEST.
     *
     * @param string $ref
     *
     * @return Illuminate\Http\Response
     */
    public function replaceGuest($ref)
    {
        $email = request('email');
        $id = request('id');

        $guest = Guest::where('email', $email)->first();

        $booking = BookingGuest::find($id)->update([
            'guest_id' => $guest->id,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => '/bookings/'.$ref,
        ]);
    }

    /**
     * ADD GUEST TO BOOKING ROOM.
     *
     * @param string $ref
     *
     * @return Illuminate\Http\Response
     */
    public function addGuest($ref)
    {
        $booking = Booking::with(['guest'])->where('ref', $ref)->first();

        $email = request('email');

        $guest = Guest::where('email', $email)->first();

        $group_id = $booking->guest ? $booking->guest->id : 0;

        $bg = new BookingGuest([
            'guest_id' => $guest->id,
            'booking_id' => $booking->id,
            'group_id' => $group_id,
        ]);

        $booking->guest()->save($bg);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Adding guest',
            'info_type' => 'info',
            'details' => '<b>'.auth()->user()->name.'</b> adding guest (<b>'.$guest->full_name.'</b>)',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'url' => route('tenant.bookings.newGuestRoom', [ 'ref' => $booking->ref, 'booking_guest_id' => $bg->id ]),
        ]);
    }

    /**
     * UPDATE SINGLE ROOM PRICE.
     *
     * @return Illuminate\Http\Response
     */
    public function updateRoomPrice()
    {
        return $this->bookingService->updateRoomPrice();
    }

    /**
     * UPDATE AGENT.
     *
     * @param string $ref
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateAgent($ref, Request $request)
    {
        $booking = Booking::where('ref', $ref)->first();
        $agent = $request->agent;

        $old_agent = '---' == $booking->agent->name ? 'No agent' : $booking->agent->name;

        $booking->update([
            'agent_id' => $agent,
        ]);

        $booking->refresh();

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Update agent',
            'info_type' => 'info',
            'details' => '<b>'.auth()->user()->name.'</b> changed agent from <b>'.$old_agent.'</b> to <b>'.$booking->agent->name.'</b>',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'date' => $booking->expiry->format('d.m.Y H:i'),
        ]);
    }

    /**
     * UPDATE EXPIRY DATE / TIME.
     *
     * @param string $ref
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateExpiry($ref, Request $request)
    {
        $booking = Booking::where('ref', $ref)->first();
        $expiry = $request->expired_at;

        $booking->update([
            'expire_at' => request('expired_at'),
            'expiry' => 99 != $expiry ? $booking->created_at->addHours(request('expired_at')) : '2090-12-31 23:59:59',
        ]);

        $booking->refresh();

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Update booking expiration',
            'info_type' => 'info',
            'details' => '<b>'.auth()->user()->name.'</b> update expiry date to <b>'.$booking->expiry->format('d.m.Y H:i') .'</b>',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'date' => $booking->expiry->format('d.m.Y H:i'),
        ]);
    }

    /**
     * UPDATE DEPOSIT DUE DATE.
     *
     * @param string $ref
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateDuedate($ref, Request $request)
    {
        $booking = Booking::with(['payment'])->where('ref', $ref)->first();

        $booking->payment->update([
            'deposit_due_date' => Carbon::createFromFormat('d.m.Y', request('due_date'))->format('Y-m-d'),
        ]);

        return response()->json([
            'status' => 'success',
            'date' => $booking->payment->deposit_due_date->format('d.m.Y'),
        ]);
    }

    /**
     * UPDATE TAX VISIBILITY
     *
     * @param string $ref
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateTaxVisibility($ref, Request $request)
    {
        $booking = Booking::where('ref', $ref)->first();
        $expiry = $request->expired_at;

        $booking->update([
            'tax_visible' => request('tax_visibility'),
        ]);

        $booking->refresh();

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Toggle tax visibility',
            'info_type' => 'info',
            'details' => '<b>'.auth()->user()->name.'</b> toggle tax visibility to <b>'.($booking->tax_visible ? 'ON' : 'OFF') .'</b>',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * ADD DISCOUNT.
     *
     * @param string $id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function addDiscount($ref, Request $request)
    {
        $name = request('name');
        $type = request('discount_type');
        $value = request('discount_value');
        $apply = request('apply_to');

        try {
            DB::beginTransaction();

            $booking = Booking::where('ref', $ref)->first();

            if ($booking->status === 'CONFIRMED') {
                $this->bookingService->storePDFInvoiceHistory($booking);
            }

            $discount = Discount::create([
                'booking_id' => $booking->id,
                'name' => $name,
                'category' => 'normal_discount',
                'type' => $type,
                'apply_to' => $apply,
                'value' => $value,
            ]);

            $discount_value = 'Percent' == $type ? $value.'%' : '&euro;'.$value;
            $discount_apply = 'ROOM' == $apply ? 'Room price only' : 'Full price';

            $booking->histories()->create([
                'booking_id' => $booking->id,
                'user_id' => auth()->user()->id,
                'action' => 'Add discount',
                'info_type' => 'slate',
                'details' => '<b>'.auth()->user()->name.'</b> adding discount <b>'.$name.'</b> (<b>'.$type.'</b> - <b>'.$discount_value.'</b>) applied to <b>'.$discount_apply.'</b>',
                'ip_address' => request()->ip(),
            ]);

            $this->paymentService->refreshPayment($booking);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * UPDATE DISCOUNT.
     *
     * @param string $id
     * @param int    $discount_id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateDiscount($ref, $discount_id, Request $request)
    {
        $type = request('discount_type');
        $value = request('discount_value');
        $apply = request('apply_to');

        try {
            DB::beginTransaction();

            $booking = Booking::where('ref', $ref)->first();

            $discount = Discount::find($discount_id);

            $old_discount_value = 'Percent' == $discount->type ? $discount->value.'%' : '&euro;'.$discount->value;
            $old_discount_apply = 'ROOM' == $discount->apply ? 'Room price only' : 'Full price';

            $discount_value = 'Percent' == $type ? $value.'%' : '&euro;'.$value;
            $discount_apply = 'ROOM' == $apply ? 'Room price only' : 'Full price';

            $type_change = $discount->type != $type ? 'Changed discount type from <b>'.$discount->type.'</b> to <b>'.$type.'</b>. ' : '';
            $apply_change = $discount->apply_to != $apply ? 'Changed Apply to from <b>'.$old_discount_apply.'</b> to <b>'.$discount_apply.'</b>. ' : '';
            $value_change = floatval($discount->value) != floatval($value) ? 'Changed discount value from <b>'.$old_discount_value.'</b> to <b>'.$discount_value.'</b>' : '';

            // generate and store existing invoice to storage if this booking already confirmed
            if ($booking->status === 'CONFIRMED') {
                $this->bookingService->storePDFInvoiceHistory($booking);
            }

            $booking->histories()->create([
                'booking_id' => $booking->id,
                'user_id' => auth()->user()->id,
                'action' => 'Edit discount',
                'info_type' => 'slate',
                'details' => '<b>'.auth()->user()->name.'</b> edited discount: '.$type_change.$apply_change.$value_change,
                'ip_address' => request()->ip(),
            ]);

            $discount->update([
                'name' => $request->name,
                'type' => $type,
                'apply_to' => $apply,
                'value' => $value,
            ]);

            $this->paymentService->refreshPayment($booking);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * REMOVE DISCOUNT.
     *
     * @param string $id
     * @param int    $discount_id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function removeDiscount($ref, $discount_id)
    {
        try {
            DB::beginTransaction();

            $booking = Booking::where('ref', $ref)->first();
            $discount = Discount::find($discount_id);

            // generate and store existing invoice to storage if this booking already confirmed
            if ($booking->status === 'CONFIRMED') {
                $this->bookingService->storePDFInvoiceHistory($booking);
            }

            $booking->histories()->create([
                'booking_id' => $booking->id,
                'user_id' => auth()->user()->id,
                'action' => 'Remove discount',
                'info_type' => 'slate',
                'details' => '<b>'.auth()->user()->name.'</b> removed discount <b>'.$discount->name.'</b>',
                'ip_address' => request()->ip(),
            ]);

            $discount->forceDelete();

            $this->paymentService->refreshPayment($booking);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * ADD TRANSFER.
     *
     * @param string $id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function addTransfer($ref, Request $request)
    {
        $number = '' != request('flight_number') ? request('flight_number') : 'TBA';
        $transfer_id = request('transfer_id');
        $booking_id = request('booking_id');
        $guests = request('guests');
        $time = '' != request('flight_time') ? Carbon::createFromFormat('d.m.Y H:i', request('flight_time'))->format('Y-m-d H:i:s') : null;
        $price = request('price');
        $booking = Booking::where('ref', $ref)->first();

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking->status === 'CONFIRMED') {
            $this->bookingService->storePDFInvoiceHistory($booking);
        }

        DB::beginTransaction();

        $transfer = BookingTransfer::create([
            'booking_id' => $booking_id,
            'transfer_extra_id' => $transfer_id,
            'flight_number' => $number,
            'flight_time' => $time,
            'price' => ('' == $price ? 0 : $price),
            'guests' => ('' == $guests ? 1 : $guests),
        ]);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Add transfer',
            'details' => '<b>'.auth()->user()->name.'</b> add <b>'.$transfer->details->name.'</b> transfer ('.$number.', '.$time.', &euro;'.$price.', '.$guests.' guests))',
            'ip_address' => request()->ip(),
        ]);

        $this->paymentService->refreshPayment($booking);

        DB::commit();

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * UPDATE TRANSFER.
     *
     * @param int    $id
     * @param int    $booking_transfer_id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function updateTransfer($ref, $booking_transfer_id, Request $request)
    {
        $number = request('flight_number');
        $time = '' != request('flight_time') ? Carbon::createFromFormat('d.m.Y H:i', request('flight_time'))->format('Y-m-d H:i:s') : null;
        $price = request('price');
        $guests = request('guests');
        $booking = Booking::where('ref', $ref)->first();

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking->status === 'CONFIRMED') {
            $this->bookingService->storePDFInvoiceHistory($booking);
        }

        $transfer = BookingTransfer::find($booking_transfer_id);
        $transfer->update([
            'flight_number' => $number,
            'flight_time' => $time,
            'price' => $price,
            'guests' => $guests,
        ]);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Add transfer',
            'details' => '<b>'.auth()->user()->name.'</b> update <b>'.$transfer->details->name.'</b> transfer ('.$number.', '.$time.', &euro;'.$price.', '.$guests.' guests)',
            'ip_address' => request()->ip(),
        ]);

        $this->paymentService->refreshPayment($booking);

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * REMOVE TRANSFER.
     *
     * @param int    $id
     * @param int    $booking_transfer_id
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function removeTransfer($ref, $booking_transfer_id)
    {
        $booking = Booking::where('ref', $ref)->first();
        $transfer = BookingTransfer::find($booking_transfer_id);

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking->status === 'CONFIRMED') {
            $this->bookingService->storePDFInvoiceHistory($booking);
        }

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Remove transfer',
            'details' => '<b>'.auth()->user()->name.'</b> removed <b>'.$transfer->details->name.'</b> transfer',
            'ip_address' => request()->ip(),
        ]);

        $transfer->forceDelete();

        return redirect(Route('tenant.bookings.show', [ 'ref' => $ref ]) .'?'.time());
    }

    /**
     * INSERT ADDON TO BOOKING ROOM.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function insertAddon(Request $request)
    {
        $addon_id = request('addonID');
        $guest_id = request('guestID');
        $booking_room_id = request('bookingRoomID');
        $price = request('price');
        $ref = request('ref');
        $amount = request('amount');
        $weeks = request('weeks');

        try {
            DB::beginTransaction();

            $booking_room = BookingRoom::find($booking_room_id);
            $addon = Extra::find($addon_id);

            // generate and store existing invoice to storage if this booking already confirmed
            if ($booking_room->booking->status === 'CONFIRMED') {
                $this->bookingService->storePDFInvoiceHistory($booking_room->booking);
            }

            $until = $addon->is_flexible ? $booking_room->from->addDays($amount)->format('Y-m-d') : $booking_room->until;

            $booking_addon = BookingAddon::create([
                'extra_id' => $addon_id,
                'booking_room_id' => $booking_room_id,
                'guests' => 1,
                'amount' => $amount,
                'price' => $price,
                'check_in' => $booking_room->from,
                'check_out' => $until,
                'info' => $weeks && $weeks > 0 ? 'Starts in '. (new \NumberFormatter('en_US', \NumberFormatter::ORDINAL))->format(intVal($weeks)) .' week' : null,
            ]);

            $booking_room->booking->histories()->create([
                'booking_id' => $booking_room->booking_id,
                'user_id' => auth()->user()->id,
                'action' => 'Add addon',
                'info_type' => 'slate',
                'details' => '<b>'.auth()->user()->name.'</b> adding addon <b>'.$booking_addon->details->name.'</b> (<b>&euro;'.$booking_addon->price.'</b>) to <b>'.$booking_room->subroom->name.'</b>',
                'ip_address' => request()->ip(),
            ]);

            $booking = Booking::find($booking_room->booking->id);

            $this->paymentService->refreshPayment($booking);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response([
            'status' => 'success',
            'url' => Route('tenant.bookings.editGuestRoom', [ 'ref' => $ref, 'booking_guest_id' => $guest_id, 'roomid' => $booking_room_id ]),
        ]);
    }

    /**
     * CALCULATE FLEXIBLE ADDONS PRICE.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function calculateAddon(Request $request)
    {
        $id = $request->id;
        $amount = $request->amount;

        $addon = Extra::with(['prices'])->find($id);

        $total = $this->bookingService->calculateAddon($addon, $amount, 1);

        return response([
            'status' => 'success',
            'price' => intval(round($total)),
        ], 200);
    }

    /**
     * REMOVE ADDON.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function removeAddon(Request $request)
    {
        $addon_id = request('addonID');
        $guest_id = request('guestID');
        $booking_room_id = request('bookingRoomID');

        try {
            DB::beginTransaction();

            $booking_room = BookingRoom::find($booking_room_id);

            $addon = BookingAddon::with(['details'])->find($addon_id);

            // generate and store existing invoice to storage if this booking already confirmed
            if ($addon->booking_room->booking->status === 'CONFIRMED') {
                $this->bookingService->storePDFInvoiceHistory($addon->booking_room->booking);
            }

            $extra_id = $addon->extra_id;

            $addon->booking_room->booking->histories()->create([
                'user_id' => auth()->user()->id,
                'info_type' => 'slate',
                'action' => 'Removed addon',
                'details' => '<b>'.auth()->user()->name.'</b> removed <b>'.$addon->details->name.'</b> addon.',
                'ip_address' => request()->ip(),
            ]);

            $addon->forceDelete();

            $booking = Booking::find($booking_room->booking->id);

            $this->paymentService->refreshPayment($booking);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response([
            'status' => 'success',
            'extra_id' => $extra_id,
        ], 200);
    }

    /**
     * REMOVE SPECIAL OFFER.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Response
     */
    public function removeSpecialOffer(Request $request)
    {
        $offer_id = request('offerID');
        $guest_id = request('guestID');
        $booking_room_id = request('bookingRoomID');

        BookingRoomDiscount::find($offer_id)->delete();

        return response([
            'status' => 'success',
        ], 200);
    }

    /**
     * GET INTERNAL NOTES.
     *
     * @param int $id
     *
     * @return array
     */
    public function getInternalNotes($id)
    {
        $notes = BookingNote::where('booking_id', $id)->orderBy('created_at', 'desc')->get();

        if ($notes) {
            $notes = $notes->map(function ($item, $key) {
                return [
                    'date' => $item->created_at->format('F d, H:i'),
                    'user' => $item->user->name ?? 'System',
                    'message' => $item->message,
                ];
            });
        }

        return $notes;
    }

    /**
     * POST INTERNAL NOTES.
     *
     * @param int $id
     *
     * @return array
     */
    public function postInternalNotes(Request $request)
    {
        $note = BookingNote::create($request->only([
            'booking_id',
            'message',
        ]));

        $note->update([
            'user_id' => auth()->user()->id,
        ]);

        return 'OK';
    }

    /**
     * QUICK ROOM MOVE.
     *
     * @param object $request
     */
    public function quickRoomMove(Request $request)
    {
        $booking_room_id = $request->bookingRoomID;
        $booking_guest_id = $request->guestId;
        $nights = $request->nights;
        $target = explode('_', $request->target);
        $data = [
            'start_date' => $target[0],
            'location_id' => $target[1],
            'room_id' => $target[2],
            'subroom_id' => $target[3],
            'bed' => $target[4],
        ];

        $end_date = Carbon::createFromFormat('Y-m-d', $data['start_date'])->addDays($nights)->format('Y-m-d');

        $booking_room = BookingRoom::with(['booking.surf_planner_users'])->find($booking_room_id);

        $guest = BookingGuest::with(['details'])->find($booking_guest_id);

        $old = [
            'room' => $booking_room->room->name,
            'subroom' => $booking_room->subroom->name,
            'from' => $booking_room->from->format('d.m.Y'),
            'to' => $booking_room->from->format('d.m.Y'),
            'bed' => $booking_room->bed,
        ];

        $booking_room->update([
            'from' => $data['start_date'],
            'to' => $end_date,
            'room_id' => $data['room_id'],
            'subroom_id' => $data['subroom_id'],
            'bed' => $data['bed'],
        ]);

        $booking_room->refresh();

        $this->bookingService->refreshBookingStayDates($booking_room->booking->id);

        $booking_room->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Moved room (drag and drop)',
            'details' => '<b>'.auth()->user()->name.'</b> moved room from <b>'.$old['room'].' - '.$old['subroom'].'</b>: <b>'.$old['from'].' - '.$old['to'].'</b> bed <b>'.$old['bed'].'</b> to <b>'.$booking_room->room->name.' - '.$booking_room->subroom->name.'</b>: <b>'.$booking_room->from->format('d.m.Y').' - '.$booking_room->to->format('d.m.Y').'</b> bed <b>'.$booking_room->bed.'</b>.',
            'ip_address' => request()->ip(),
        ]);

        if ($booking_room->booking->surf_planner_users->count() > 0) {
            // ...
            $payload = [
                'check_in' => $data['start_date'],
                'check_out' => $end_date,
                'email' => $guest->details->email,
                'booking_id' => $booking_room->booking->id,
            ];
        }
    }

    public function mailPreview($ref)
    {
        $booking = Booking::where('ref', $ref)->first();

        return new \App\Mail\OrderConfirmed($booking);
    }

    /**
     * DOWNLOAD INVOICE SNAPSHOT.
     *
     * @param mixed $ref
     *
     * @return Illuminate\Http\Redirect
     */
    public function downloadInvoiceSnapshot($ref, $history_id)
    {
        $path = 'invoice/'.$ref.'/invoice-'.$history_id.'.pdf';
        $fileName = $ref.'-invoice_snapshot-'.$history_id.'.pdf';

        if (Storage::exists($path)) {
            return Storage::download($path, $fileName);
        }

        return redirect()->back();
    }

    /**
     * CHECK IN BOOKING.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function accommodationCheckIn(Request $request)
    {
        $validatedData = $request->validate([
            'guests' => 'required|array',
        ]);

        try {
            $this->bookingService->guestCheckIn($validatedData['guests']);
        } catch (\Throwable $th) {
            return response()->json([], 400);
        }

        return response()->json([], 204);
    }

    /**
     * ADDON CHECK IN BOOKING.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function addonCheckIn(Request $request)
    {
        $validatedData = $request->validate([
            'guests' => 'required|array',
        ]);

        try {
            $this->bookingService->addonCheckIn($validatedData['guests']);
        } catch (\Throwable $th) {
            return response()->json([], 400);
        }

        return response()->json([], 204);
    }
}
