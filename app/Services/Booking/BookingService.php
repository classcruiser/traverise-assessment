<?php

namespace App\Services\Booking;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingAddon;
use App\Models\Booking\BookingGuest;
use App\Models\Booking\BookingRoom;
use App\Models\Booking\BookingRoomDiscount;
use App\Models\Booking\BookingRoomGuest;
use App\Models\Booking\BookingTransfer;
use App\Models\Booking\EmailHistory;
use App\Models\Booking\Extra;
use App\Models\Booking\Guest;
use App\Models\Booking\Location;
use App\Models\Booking\Profile;
use App\Models\Booking\Room;
use App\Models\Booking\RoomInfo;
use App\Models\Booking\SpecialOffer;
use App\Models\Booking\User;
use App\Models\Booking\PaymentTransfer;
use App\Models\Booking\TransferExtra;
use App\Models\Booking\UserHistory;
use App\Services\UtilService;
use App\Services\Booking\TaxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class BookingService
{
    protected $pages = 15;

    protected $roomService;
    protected $userService;
    protected $paymentService;

    public function __construct(UserService $userService, PaymentService $paymentService, RoomService $roomService)
    {
        $this->userService = $userService;
        $this->roomService = $roomService;
        $this->paymentService = $paymentService;
    }

    public function dashboard()
    {
        $date = Carbon::now();
        $start = $date->startOfWeek()->format('Y-m-d');
        $end = $date->endOfWeek()->format('Y-m-d');

        return null;
    }

    public function newGuest($ref)
    {
        $booking = Booking::with(['rooms.subroom', 'guests', 'guest'])->where('ref', $ref)->first();
        $countries = DB::table('country_codes')->orderBy('country_name')->get();
        $role = 1;

        $is_deleted = !is_null($booking->deleted_at);

        $agents = $this->userService->getAgentList();

        return view('Booking.bookings.new-guest', compact('booking', 'countries', 'role', 'agents', 'is_deleted'));
    }

    /**
     * UPDATE BOOKING OVERVIEW PRICES.
     *
     * @param string $id
     * @param mixed  $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function updateBookingPrices($id, $request)
    {
        $booking = Booking::with(['rooms.subroom', 'rooms.discounts', 'guests', 'guest', 'transfers', 'rooms.addons', 'payment'])->where('ref', $id)->first();

        // update room & duration discount price
        $room_prices = $request->room_price;
        $duration_discounts = $request->duration_discount;
        $addons = $request->addon;
        $transfers = $request->transfers;
        $offers = $request->offer;
        $commission = floatval($request->commission);
        $old_commission = floatval($request->old_commission);

        $history = collect([]);

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking->status === 'CONFIRMED') {
            $createSnapshot = false;
            foreach ($room_prices as $id => $price) {
                $room = $booking->rooms->where('id', $id)->first();

                if (floatval($price) != floatval($room->price)) {
                    $createSnapshot = true;
                    break;
                }

                if ($room->addons->count() > 0) {
                    foreach ($room->addons as $addon) {
                        $room_addon = $room->addons->where('id', $addon->id)->first();

                        if (floatval($room_addon->price) != floatval($addons[$addon->id]['price'])) {
                            $createSnapshot = true;
                            break;
                        }
                    }
                }

                if ($createSnapshot) {
                    break;
                }
            }

            if (!$createSnapshot && $request->has('transfers')) {
                foreach ($transfers as $id => $price) {
                    $transfer = $booking->transfers->where('id', $id)->first();

                    if (floatval($transfer->price) != floatval($price['price'])) {
                        $createSnapshot = true;
                        break;
                    }
                }
            }

            if ($createSnapshot) {
                $this->storePDFInvoiceHistory($booking);
            }
        }

        foreach ($room_prices as $id => $price) {
            $room = $booking->rooms->where('id', $id)->first();

            if (floatval($price) != floatval($room->price)) {
                $history->push('Changed <b>'.$room->subroom->name.'</b> price from <b>&euro;'.$room->price.'</b> to <b>&euro;'.$price.'</b>.');
            }

            $room->update([
                'price' => $this->convertCommaToDecimal($price),
            ]);

            if ($room->addons->count() > 0) {
                foreach ($room->addons as $addon) {
                    $room_addon = $room->addons->where('id', $addon->id)->first();

                    if (floatval($room_addon->price) != floatval($addons[$addon->id]['price'])) {
                        $history->push('Changed <b>'.$room->subroom->name.'</b> addon price ('.$room_addon->details->name.') from <b>&euro;'.$room_addon->price.'</b> to <b>&euro;'.$addons[$addon->id]['price'].'</b>.');
                    }

                    $room_addon->update([
                        'price' => $this->convertCommaToDecimal($addons[$addon->id]['price']),
                    ]);
                }
            }

            if ($room->discounts->count() > 0) {
                foreach ($room->discounts as $offer) {
                    $room_offer = $room->discounts->where('id', $offer->id)->first();

                    $room_offer->update([
                        'discount_value' => $this->convertCommaToDecimal($offers[$offer->id]),
                    ]);
                }
            }
        }

        if ($request->has('transfers')) {
            foreach ($transfers as $id => $price) {
                $transfer = $booking->transfers->where('id', $id)->first();

                if (floatval($transfer->price) != floatval($price['price'])) {
                    $history->push('Changed <b>'.$transfer->details->name.'</b> price from <b>&euro;'.$transfer->price.'</b> to <b>&euro;'.$price['price'].'</b>.');
                }

                $transfer->update([
                    'price' => $this->convertCommaToDecimal($price['price']),
                ]);
            }
        }

        if ($request->has('offers')) {
            foreach ($offers as $id => $value) {
                $offer = $booking->discounts->where('id', $id)->first();

                $transfer->update([
                    'price' => $this->convertCommaToDecimal($price['price']),
                ]);
            }
        }

        $tax_total = TaxService::calculateTotalExclusiveTax($booking);

        $booking->payment->update([
            'total' => $booking->grand_total,
            'processing_fee' => request('processing_fee', $booking->payment->calculateProcessingFee()),
        ]);

        if ($old_commission != $commission) {
            $booking->histories()->create([
                'user_id' => auth()->user()->id,
                'info_type' => 'slate',
                'action' => 'Update commission value',
                'details' => '<b>'.auth()->user()->name.'</b> updated commission from <b>&euro;'.$old_commission.'</b> to <b>&euro;'.$commission.'</b>',
                'ip_address' => request()->ip(),
            ]);

            $booking->update([
                'agent_commission' => $commission,
            ]);
        }

        if ($history->count() > 0) {
            $history = $history->toArray();

            $booking->histories()->create([
                'user_id' => auth()->user()->id,
                'info_type' => 'slate',
                'action' => 'Update booking overview price',
                'details' => '<b>'.auth()->user()->name.'</b> updated booking overview. '.implode('<br />', $history),
                'ip_address' => request()->ip(),
            ]);
        }

        $this->paymentService->refreshPayment($booking);

        return response('OK');
    }

    public function preparePDFInvoice(string $ref, PaymentTransfer $payment_transfer = null, string $invoice_number = null, bool $is_final = true)
    {
        $booking = Booking::with([
            'rooms.subroom', 'rooms.addons.details', 'rooms.discounts.offer', 'guests',
            'guest', 'transfers.details', 'discounts', 'histories', 'payment.records.user',
        ])
            ->withCount(['rooms', 'transfers', 'guests'])
            ->where('ref', $ref)->first();

        $role = 1;
        $index = null;

        $total_paid_amount = $booking->payment->total_paid;

        if ($invoice_number) {
            $index = $invoice_number - 1;
            $invoice_number = $booking->payment->invoice .'-'. $invoice_number;

            $total_paid_amount = $booking->payment->records[$index]->amount;
        }

        $profile = Profile::where('tenant_id', $booking->tenant_id)->first();
        $subtotal = $booking->subtotal_with_discount + $booking->payment->processing_fee;

        $data = [
            'price' => $booking->subtotal_with_discount + $booking->payment->processing_fee,
            'addons' => $booking->total_addons_price_with_applicable_tax,
            'non_taxable_addons' => $booking->total_addons_price - $booking->total_addons_price_with_applicable_tax,
            'total_paid' => $total_paid_amount,
        ];

        $tax = TaxService::getActiveTaxes($booking);

        if (request()->has('preview')) {
            return view('Booking.bookings.pdf-invoice', compact(
                'tax', 'booking', 'role', 'profile', 'payment_transfer', 'invoice_number', 'is_final', 'index'
            ));
        }

        return [
            'pdf' => PDF::loadView('Booking.bookings.pdf-invoice', compact(
                'tax', 'booking', 'role', 'profile', 'payment_transfer', 'invoice_number', 'is_final', 'index'
            )),
            'filename' => $booking->ref.'-'.Str::slug($booking->location->name).'.pdf',
        ];
    }

    public function storePDFInvoiceHistory(Booking $booking): void
    {
        $ref = $booking->ref;
        $bookingHistory = $booking->histories()->create([
            'user_id' => null,
            'info_type' => 'slate',
            'action' => 'Generate Invoice (automatic)',
            'details' => '<b>System</b> created snapshot of current invoice before updating booking',
            'ip_address' => null,
        ]);

        $directory = 'invoice/'.$ref;
        if (! Storage::directoryExists($directory)) {
            Storage::makeDirectory($directory);
        }

        $storagePage = Storage::path($directory.'/invoice-'.$bookingHistory->id.'.pdf');
        $pdf = $this->preparePDFInvoice($ref);
        $pdf['pdf']->save($storagePage);
    }

    public function cancelBooking($ref)
    {
        $booking = Booking::where('ref', $ref)->firstOrFail();

        $booking->update([
            'cancel_reason' => request('reason'),
            'status' => 'CANCELLED',
            'tax_visible' => 1,
        ]);

        $booking->cancellation()->create([
            'user_id' => auth()->user()->id,
            'reason' => request('reason'),
            'cancellation_fee' => request('fee'),
            'ref' => 'C-'. time() .'-'. strtoupper(Str::random(3))
        ]);

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'danger',
            'action' => 'Cancel booking',
            'details' => '<b>'.auth()->user()->name.'</b> cancelled booking',
        ]);

        return redirect(route('tenant.bookings.show', ['ref' => $ref]));
    }

    public function editGuest($ref, $booking_guest_id)
    {
        $booking = Booking::with(['rooms.subroom', 'guests', 'guest'])->where('ref', $ref)->first();

        $guest = $booking->guests->where('id', $booking_guest_id)->first()->details;

        if ('' == $guest->birthdate) {
            $guest->birthdate = '0000-00-00';
        }

        $birthdate = explode('-', $guest->birthdate);

        $role = 1;

        $countries = DB::table('country_codes')->orderBy('country_name', 'asc')->get();

        $agents = $this->userService->getAgentList();

        $is_deleted = !is_null($booking->deleted_at);

        return view('Booking.bookings.edit-guest', compact('booking', 'guest', 'birthdate', 'booking_guest_id', 'role', 'countries', 'agents', 'is_deleted'));
    }

    public function updateGuest($ref, $booking_guest_id, Request $request)
    {
        $guest = Guest::find(request('guest_id'));

        $guest->update($request->only([
            'fname', 'lname', 'title', 'company', 'email', 'phone', 'street', 'zip', 'city', 'country', 'agent_id',
        ]));

        $guest->update([
            'birthdate' => $request->birthdate_year.'-'.$request->birthdate_month.'-'.$request->birthdate_day,
        ]);

        request()->session()->flash('messages', 'Guest updated!');

        return redirect(Route('tenant.bookings.editGuest', [ 'ref' => $ref, 'booking_guest_id' => $booking_guest_id ]) .'?'.time());
    }

    public function removeGuest($id, $booking_guest_id)
    {
        $booking = Booking::with(['rooms.subroom', 'guests', 'guest'])->where('ref', $id)->first();

        // first get the booking_guests
        $booking_guest = BookingGuest::with(['booking'])->find($booking_guest_id);

        // find booking_rooms_guests
        $booking_room_guest = BookingRoomGuest::where('booking_guest_id', $booking_guest->id);

        $booking_room_ids = $booking_room_guest->get()->pluck('booking_room_id')->all();

        DB::beginTransaction();

        if (is_array($booking_room_ids) && count($booking_room_ids) > 0) {
            $booking_room_guest->forceDelete();
            $booking_rooms = BookingRoom::find($booking_room_ids);

            foreach ($booking_rooms as $booking_room) {
                // find booking_addons
                $addons = BookingAddon::where('booking_room_id', $booking_room->id)->get();

                if ($addons) {
                    foreach ($addons as $addon) {
                        $extra_id = $addon->extra_id;
                    }
                }

                $booking_room->addons()->delete();
                $booking_room->discounts()->delete();
                // delete booking room
                $booking_room->forceDelete();
            }
        }

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'warning',
            'action' => 'Remove guest',
            'details' => '<b>'.auth()->user()->name.'</b> removed guest '.$booking_guest->details->full_name,
            'ip_address' => request()->ip(),
        ]);

        $booking_guest->delete();

        // refresh booking stay dates
        $this->refreshBookingStayDates($booking->id);

        DB::commit();

        return redirect('bookings/'.$id);
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
        $bed_type = request('bedType');
        $dates = $this->convertDates(request('dates'));
        $durationDiscount = request('durationDiscount');
        $final_price = request('finalPrice');
        $location = request('location');
        $normal_price = request('normalPrice');
        $occupied_beds = request('occupiedBeds');
        $price = request('price');
        $private_booking = request('privateBooking');
        $subroom_id = request('subroom');

        $duration = $dates['duration'];

        $booking = Booking::with(['rooms.subroom', 'guests', 'guest', 'payment'])->where('ref', $ref)->first();

        $subroom = RoomInfo::with(['room'])->where('id', $subroom_id)->first();

        $room = $subroom->room;
        $default_bed_type = json_decode($room->bed_type, true);

        $capacity = $subroom->beds;

        if (count($occupied_beds) >= $capacity) {
            return response()->json([
                'response' => 'error',
                'message' => 'Room is full. Please select other room',
            ], 500);
        }

        $free_bed = '';

        /*
        if ($subroom->room->room_type == 'Private') {
          $private_booking = 1;
        }*/

        if ($room->is_dorm && $subroom->ota_reserved) {
            for ($i = $capacity; $i >= 1; --$i) {
                if ('' == $free_bed && (!in_array($i, $occupied_beds))) {
                    $free_bed = $i;
                }
            }

            $free_bed = '' == $free_bed ? $capacity : $free_bed;
        } else {
            foreach (range(1, $capacity) as $bed) {
                if ('' == $free_bed && (!in_array($bed, $occupied_beds))) {
                    $free_bed = $bed;
                }
            }
        }

        if ($booking->rooms()->count() <= 0) {
            // update booking
            $booking->update([
                'location_id' => $location,
                'check_in' => $dates['start'],
                'check_out' => $dates['end'],
            ]);
        }

        $guest = BookingGuest::with(['details'])->find($booking_guest_id);

        DB::beginTransaction();

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking->status === 'CONFIRMED') {
            $this->storePDFInvoiceHistory($booking);
        }

        // add new booking_rooms
        $booking_room = BookingRoom::create([
            'booking_id' => $booking->id,
            'room_id' => $subroom->room->id,
            'subroom_id' => $subroom->id,
            'bed' => $free_bed,
            'bed_type' => $bed_type,
            'bathroom' => $subroom->room->bathroom_type,
            'from' => $dates['start'],
            'to' => $dates['end'],
            'is_private' => $private_booking,
            'guest' => 1,
            'price' => ($final_price == $price ? $normal_price : $price),
            'duration_discount' => ($final_price == $price ? $durationDiscount : 0),
        ]);

        // add new booking_room_guests
        BookingRoomGuest::create([
            'booking_room_id' => $booking_room->id,
            'booking_guest_id' => $booking_guest_id,
        ]);

        $stay_dates = Carbon::createFromFormat('Y-m-d', $dates['start'])->format('d.m.Y').' - '.Carbon::createFromFormat('Y-m-d', $dates['end'])->format('d.m.Y');

        $booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Assign room for guest : '.$guest->details->id,
            'details' => '<b>'.auth()->user()->name.'</b> assigned room '.$subroom->name.' bed '.$free_bed.' - ('.$bed_type.' bed) '.($private_booking ? '(Private booking)' : '').' (<b>'.$stay_dates.'</b>) for guest (<b>'.$guest->details->full_name.'</b>)',
            'ip_address' => request()->ip(),
        ]);

        // find addon with add_default
        $default_addons = Extra::where('add_default', 1)
            ->whereHas('rooms', function ($q) use ($subroom) {
                $q->where('room_id', $subroom->room->id);
            })
            ->get()
        ;

        if ($default_addons) {
            foreach ($default_addons as $addon) {
                $addon_text = '';
                if (!$addon->is_flexible && 'Fixed' == $addon->rate_type) {
                    BookingAddon::create([
                        'booking_room_id' => $booking_room->id,
                        'extra_id' => $addon->id,
                        'guests' => 1,
                        'amount' => 1,
                        'check_in' => $dates['start'],
                        'check_out' => $dates['end'],
                        'price' => $addon->base_price,
                    ]);
                    $addon_text = $addon->name;
                } else {
                    $addon_price = $addon->base_price * $duration;
                    $booking_addon = BookingAddon::create([
                        'booking_room_id' => $booking_room->id,
                        'extra_id' => $addon->id,
                        'guests' => 1,
                        'amount' => ($duration + 1),
                        'check_in' => $dates['start'],
                        'check_out' => $dates['end'],
                        'price' => $addon_price,
                    ]);
                    $addon_text = $addon->name.' ('.($duration + 1).') days';
                }

                $booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'slate',
                    'action' => 'Adding add-on (automatic)',
                    'details' => '<b>System</b> adding addon (<b>'.$addon_text.'</b>) for <b>'.$subroom->name.'</b> room',
                    'ip_address' => request()->ip(),
                ]);
            }
        }

        // find special offer
        $this->addEligibleSpecialOffer($booking_room, $dates);

        // add transfer only if we don't have yet
        if ($booking->transfers->count() <= 0) {
            $default_transfers = TransferExtra::with(['prices'])
                ->where('add_default', 1)
                ->where('default_min_nights', '<=', $duration)
                ->whereHas('rooms', function ($q) use ($subroom) {
                    $q->where('room_id', $subroom->room->id);
                })
                ->get()
            ;

            if ($default_transfers) {
                foreach ($default_transfers as $transfer) {
                    if ($transfer->is_complimentary && $duration >= $transfer->complimentary_min_nights) {
                        $price = 0;
                    } else {
                        $price = $transfer->prices->where('guest', 1)->first()->price;
                    }

                    BookingTransfer::create([
                        'booking_id' => $booking->id,
                        'transfer_extra_id' => $transfer->id,
                        'flight_number' => 'TBA',
                        'flight_time' => null,
                        'guests' => 1,
                        'price' => $price,
                    ]);
                }
            }
        }

        // refresh booking stay dates
        $this->refreshBookingStayDates($booking->id);

        // update payment
        $this->paymentService->refreshPayment($booking);

        DB::commit();

        return response()->json([
            'response' => 'success',
            'url' => Route('tenant.bookings.show', [ 'ref' => $booking->ref ]), // harusnya ke halaman guest ini
        ], 200);
    }

    public function deleteBooking($ref)
    {
        $booking = Booking::where('ref', $ref)->first();

        DB::beginTransaction();

        UserHistory::create([
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'email' => auth()->check() ? auth()->user()->email : null,
            'action' => 'DELETE_BOOKING',
            'description' => (auth()->check() ? auth()->user()->name : 'Book Now page') .' deletes booking #'. $ref,
            'ip_address' => request()->ip(),
        ]);

        if ($booking) {
            /*
            ** no need to delete the model relationships
            ** as we need to keep the data for reporting purpose

            if ($booking->rooms) {
                foreach ($booking->rooms as $room) {
                    $room->discounts()->delete();
                    $room->guests()->delete();
                    $room->addons()->delete();
                }

                $booking->rooms()->delete();
            }

            if ($booking->payment) {
                $booking->payment->records()->delete();
                $booking->payment()->delete();
            }

            if ($booking->histories) {
                $booking->histories()->delete();
            }

            if ($booking->notes) {
                $booking->notes()->delete();
            }

            if ($booking->guests) {
                $booking->guests()->delete();
            }

            if ($booking->transfers) {
                $booking->transfers()->delete();
            }

            if ($booking->discounts) {
                $booking->discounts()->delete();
            }

            if ($booking->emails) {
                $booking->emails()->delete();
            }
            */

            $booking->delete();
        }

        DB::commit();
    }

    public function addEligibleSpecialOffer($booking_room, $dates)
    {
        $date_start = strtotime($dates['start']);
        $date_end = strtotime($dates['end']);
        $total_guests = $booking_room->booking->total_guests;

        $offers = SpecialOffer::whereHas('rooms', function ($q) use ($booking_room) {
            return $q->where('room_id', $booking_room->room_id);
        })->get();

        if ($offers->count() > 0) {
            // loop each offers to see if we can use it
            foreach ($offers as $offer) {
                $eligible = 0;
                $stay = $offer->stay_type;
                $stay_dates = explode(' - ', $offer->stay_value);
                $stay_dates_start = strtotime($stay_dates[0]);
                $stay_dates_end = strtotime($stay_dates[1]);
                $booked_dates = $offer->booked_between ? explode(' - ', $offer->booked_between) : null;
                $booked_date_start = $offer->booked_between ? strtotime($booked_dates[0]) : null;
                $booked_date_end = $offer->booked_between ? strtotime($booked_dates[1]) : null;

                // stay condition
                if ('Whole stay in' == $stay) {
                    if ($date_start >= $stay_dates_start && $date_end <= $stay_dates_end) {
                        $eligible = 1;
                    }
                } elseif ('Check-In date in' == $stay) {
                    if ($date_start >= $stay_dates_start && $date_start <= $stay_dates_end) {
                        $eligible = 1;
                    }
                } elseif ('Check-Out date in' == $stay) {
                    if ($date_end >= $stay_dates_start && $date_end <= $stay_dates_end) {
                        $eligible = 1;
                    }
                }

                // abort the offer once it failed the first condition
                if (0 == $eligible) {
                    continue;
                }

                // booked between
                if ($booked_dates) {
                    $eligible = 0;
                    $created_date = strtotime($booking_room->created_at);

                    if ($created_date >= $booked_date_start && $created_date <= $booked_date_end) {
                        $eligible = 1;
                    } else {
                        // abort the offer for second condition
                        continue;
                    }
                }

                if ($offer->min_guest) {
                    $eligible = 0;
                    if ($total_guests >= $offer->min_guest) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->max_guest) {
                    $eligible = 0;
                    if ($total_guests <= $offer->min_guest) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->min_stay) {
                    $eligible = 0;
                    if ($booking_room->nights >= $offer->min_stay) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->max_stay) {
                    $eligible = 0;
                    if ($booking_room->nights >= $offer->max_stay) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                // is okay
                if ($eligible) {
                    if ('Percent' == $offer->discount_type) {
                        $total = round(($booking_room->price - $booking_room->duration_discount) * ($offer->discount_value / 100));
                    }
                    if ('Fixed' == $offer->discount_type) {
                        $total = $offer->discount_value;
                    }

                    $discount = BookingRoomDiscount::firstOrCreate([
                        'booking_room_id' => $booking_room->id,
                        'special_offer_id' => $offer->id,
                    ], [
                        'discount_value' => $total,
                    ]);
                }
            }
        }
    }

    public function checkSpecialOffer($room_id, $dates, $guests, $availability)
    {
        $date_start = strtotime($dates['start']);
        $date_end = strtotime($dates['end']);
        $total_guests = $guests;
        $price = $availability['price'];
        $duration = $availability['duration'];

        $offers = SpecialOffer::whereHas('rooms', function ($q) use ($room_id) {
            return $q->where('room_id', $room_id);
        })->get();

        if ($offers->count() > 0) {
            // loop each offers to see if we can use it
            foreach ($offers as $offer) {
                $eligible = 0;
                $stay = $offer->stay_type;
                $stay_dates = explode(' - ', $offer->stay_value);
                $stay_dates_start = strtotime($stay_dates[0]);
                $stay_dates_end = strtotime($stay_dates[1]);
                $booked_dates = $offer->booked_between ? explode(' - ', $offer->booked_between) : null;
                $booked_date_start = $offer->booked_between ? strtotime($booked_dates[0]) : null;
                $booked_date_end = $offer->booked_between ? strtotime($booked_dates[1] .' 23:59:59') : null;

                // stay condition
                if ('Whole stay in' == $stay) {
                    if ($date_start >= $stay_dates_start && $date_end <= $stay_dates_end) {
                        $eligible = 1;
                    }
                } elseif ('Check-In date in' == $stay) {
                    if ($date_start >= $stay_dates_start && $date_start <= $stay_dates_end) {
                        $eligible = 1;
                    }
                } elseif ('Check-Out date in' == $stay) {
                    if ($date_end >= $stay_dates_start && $date_end <= $stay_dates_end) {
                        $eligible = 1;
                    }
                }

                // abort the offer once it failed the first condition
                if (0 == $eligible) {
                    continue;
                }

                // booked between
                if ($booked_dates) {
                    $eligible = 0;
                    $created_date = strtotime(date('Y-m-d H:i:s'));

                    if ($created_date >= $booked_date_start && $created_date <= $booked_date_end) {
                        $eligible = 1;
                    } else {
                        // abort the offer for second condition
                        continue;
                    }
                }

                if ($offer->min_guest) {
                    $eligible = 0;
                    if ($total_guests >= $offer->min_guest) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->max_guest) {
                    $eligible = 0;
                    if ($total_guests <= $offer->min_guest) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->min_stay) {
                    $eligible = 0;
                    if ($duration >= $offer->min_stay) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                if ($offer->max_stay) {
                    $eligible = 0;
                    if ($duration < $offer->max_stay) {
                        $eligible = 1;
                    } else {
                        continue;
                    }
                }

                // is okay
                if ($eligible) {
                    if ('Percent' == $offer->discount_type) {
                        $total = number_format($price * ($offer->discount_value / 100), 2);
                        $type = $offer->discount_value.'%';
                    }
                    if ('Fixed' == $offer->discount_type) {
                        $total = $offer->discount_value;
                        $type = '&euro;'.$total;
                    }

                    return [
                        'discount' => floatVal($total),
                        'offer' => $offer->name,
                        'type' => $type,
                        'value' => $offer->discount_value,
                    ];
                }
            }
        }

        return null;
    }

    public function editGuestRoom($ref, $booking_guest_id, $booking_room_id)
    {
        $role = 1;

        $booking = Booking::with([
            'rooms.subroom', 'rooms.addons.details', 'rooms.discounts.offer', 'guests',
            'guest', 'transfers.details', 'discounts', 'histories',
        ])
            ->withCount(['rooms', 'transfers', 'guests'])
            ->where('ref', $ref)
            ->first();

        $guest = $booking->guests->where('id', $booking_guest_id)->first()->details;

        $booking_room = $booking->rooms->where('id', $booking_room_id)->first();

        $addons = Extra::whereHas('rooms', function ($q) use ($booking_room) {
            $q->where('room_id', $booking_room->room_id);
        })
            ->orderBy('rate_type', 'asc')
            ->orderBy('name', 'asc')
            ->get([
                'id', 'name', 'rate_type', 'unit_name', 'base_price', 'is_flexible', 'min_stay',
                'min_guests', 'max_guests', 'min_units', 'max_units', 'sort', 'week_question'
            ]);

        $has_board = 0;

        $board = null;

        $locations = Location::get();

        $agents = $this->userService->getAgentList();

        $is_deleted = !is_null($booking->deleted_at);

        $weeks = $booking->check_in->diffInWeeks($booking->check_out);

        return view('Booking.bookings.edit-guest-room', compact(
            'booking', 'guest', 'booking_room', 'booking_guest_id', 'booking_room_id',
            'locations', 'addons', 'role', 'agents', 'board', 'has_board', 'is_deleted',
            'weeks'
        ));
    }

    public function replaceGuestRoom($ref, $booking_guest_id, $booking_room_id, $request)
    {
        $bed_type = request('bedType');
        $subroom_id = request('subroomID');
        $room_id = request('roomID');
        $occupied_beds = request('occupiedBeds');
        $keep_current_price = request('keepOldPrice');
        $price = request('price');
        $normal_price = request('normalPrice');
        $final_price = request('finalPrice');
        $location = request('location');
        $durationDiscount = request('durationDiscount');
        $private_booking = request('privateBooking');

        $dates = $this->convertDates(request('dates'));
        $duration = $dates['duration'];

        $subroom = RoomInfo::with(['room'])->where('id', $subroom_id)->first();
        $guest = BookingGuest::with(['details'])->find($booking_guest_id);
        $capacity = $subroom->room->capacity;

        $free_bed = '';

        foreach (range(1, $capacity) as $bed) {
            if ('' == $free_bed && (!in_array($bed, $occupied_beds))) {
                $free_bed = $bed;
            }
        }

        $booking_room = BookingRoom::with(['booking'])->find($booking_room_id);

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking_room->booking->status === 'CONFIRMED') {
            $this->storePDFInvoiceHistory($booking_room->booking);
        }

        $old_room_id = $booking_room->room_id;

        $duration_discount = $durationDiscount;

        if ($keep_current_price) {
            $duration_discount = $booking_room->duration_discount;
        } else {
            if ($price == $final_price) {
                $price = $normal_price;
                $duration_discount = $durationDiscount;
            } else {
                $duration_discount = 0;
            }
        }

        $default_addons = Extra::where('add_default', 1)
            ->whereHas('rooms', function ($q) use ($subroom) {
                $q->where('room_id', $subroom->room->id);
            })
            ->get()
        ;

        if ($default_addons) {
            foreach ($default_addons as $addon) {
                if (!$addon->is_flexible && 'Fixed' == $addon->rate_type) {
                    BookingAddon::firstOrCreate([
                        'booking_room_id' => $booking_room->id,
                        'extra_id' => $addon->id,
                    ], [
                        'guests' => 1,
                        'amount' => 1,
                        'price' => $addon->base_price,
                    ]);
                } else {
                    $addon_price = $addon->base_price * $duration;
                    BookingAddon::firstOrCreate([
                        'booking_room_id' => $booking_room->id,
                        'extra_id' => $addon->id,
                    ], [
                        'guests' => 1,
                        'amount' => ($duration + 1),
                        'price' => $addon_price,
                    ]);
                }
            }
        }

        $booking_room->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Replace room',
            'details' => '<b>'.auth()->user()->name.'</b> replaced room <b>'.$booking_room->subroom->name.'</b> with <b>'.$subroom->name.'</b> (bed '.$free_bed.') for guest <b>'.$guest->details->full_name.'</b>',
            'ip_address' => request()->ip(),
        ]);

        $booking_room->update([
            'room_id' => $subroom->room->id,
            'subroom_id' => $subroom->id,
            'bed' => $free_bed,
            'bed_type' => $bed_type,
            'bathroom' => $subroom->room->bathroom_type,
            'from' => $dates['start'],
            'to' => $dates['end'],
            'guest' => 1,
            'price' => $price,
            'is_private' => $private_booking,
            'duration_discount' => $duration_discount,
        ]);

        $latest_check_in = BookingRoom::where('booking_id', $booking_room->booking->id)->orderBy('from', 'asc')->first(['id', 'from']);
        $latest_check_out = BookingRoom::where('booking_id', $booking_room->booking->id)->orderBy('to', 'desc')->first(['id', 'to']);

        $booking_room->booking->update(['check_in' => $latest_check_in->from]);
        $booking_room->booking->update(['check_out' => $latest_check_out->to]);

        // find eligible offer
        $this->addEligibleSpecialOffer($booking_room, $dates);

        $this->paymentService->refreshPayment($booking_room->booking);

        // update surf planner if exist
        // if ($booking_room->booking->surf_planner_users->count() > 0) {
        //     // ...
        //     $payload = [
        //         'check_in' => $dates['start'],
        //         'check_out' => $dates['end'],
        //         'email' => $guest->details->email,
        //         'booking_id' => $booking_room->booking->id,
        //     ];

        //     /** SURFPLANNER
        //     SurfPlannerUser::create([
        //         'booking_id' => $booking_room->booking->id,
        //         'type' => 'update-dates',
        //         'check_in' => $dates['start'],
        //         'email' => $guest->details->email,
        //         'url' => env('SURFPLANNER_URL').'/api/update-dates',
        //         'payload' => json_encode($payload),
        //         'is_completed' => 0,
        //         'method' => 'POST',
        //     ]);
        //     */
        // }

        return response()->json([
            'response' => 'success',
            'url' => Route('tenant.bookings.show', [ 'ref' => $booking_room->booking->ref ]), // harusnya ke halaman guest ini
        ], 200);
    }

    /**
     * REMOVE BOOKING ROOM.
     *
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function removeBookingRoom($request)
    {
        DB::beginTransaction();

        $booking_room = BookingRoom::with(['booking'])->find($request->bookingRoomID);

        $id = $booking_room->booking_id;

        // generate and store existing invoice to storage if this booking already confirmed
        if ($booking_room->booking->status === 'CONFIRMED') {
            $this->storePDFInvoiceHistory($booking_room->booking);
        }

        $booking_room->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'danger',
            'action' => 'Removing booking room',
            'details' => '<b>'.auth()->user()->name.'</b> removed room <b>'.$booking_room->subroom->name.'</b>',
            'ip_address' => request()->ip(),
        ]);

        // find booking_rooms_guests
        BookingRoomGuest::where('booking_room_id', $request->bookingRoomID)->forceDelete();

        // find booking_addons
        $addons = BookingAddon::where('booking_room_id', $request->bookingRoomID)->get();

        if ($addons) {
            foreach ($addons as $addon) {
                $extra_id = $addon->extra_id;
            }
        }

        BookingAddon::where('booking_room_id', $request->bookingRoomID)->forceDelete();

        // find booking_room_discounts
        BookingRoomDiscount::where('booking_room_id', $request->bookingRoomID)->forceDelete();

        // delete booking room
        $booking_room->forceDelete();

        $this->refreshBookingStayDates($id);

        DB::commit();

        return response([
            'status' => 'success',
            'url' => Route('tenant.bookings.show', [ 'ref' => $booking_room->booking->ref ]),
        ]);
    }

    public function updateQuestionnaireAnswer($request)
    {
        BookingAddon::where('id', $request->addon_id)->update([
            'questionnaire_answers' => $request->answers
        ]);

        return response('OK');
    }

    public function refreshBookingStayDates($id)
    {
        $rooms = BookingRoom::where('booking_id', $id)->get();

        if ($rooms->count() >= 1) {
            $earliest = $rooms->sortBy('from')->first()->from;
            $latest = $rooms->sortBy('to')->last()->to;

            // update booking
            Booking::find($id)->update([
                'check_in' => $earliest,
                'check_out' => $latest,
            ]);
        }
    }

    public function convertDates($dates)
    {
        $tmp = explode(' - ', $dates);
        $start = Carbon::createFromFormat('d.m.Y', $tmp[0]);
        $end = Carbon::createFromFormat('d.m.Y', $tmp[1]);
        $duration = $start->diffInDays($end);

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'duration' => intval($duration),
        ];
    }

    public function updateRoomPrice()
    {
        $id = request('id');
        $booking_id = request('booking_id');
        $booking_room_id = request('booking_room_id');
        $booking_guest_id = request('booking_guest_id');
        $addons = request('addon');
        $offers = request('special_offer');
        $price = request('price');
        $duration_discount = request('duration_discount');
        $bed_type = request('bed_type');

        $history = collect([]);
        $createPdfSnapshot = false;

        $booking = Booking::with(['rooms.addons', 'guests'])->find($booking_id);

        $room = $booking->rooms()->where('id', $booking_room_id)->first();

        if ($bed_type != $room->bed_type) {
            $history->push('Changed <b>'.$room->subroom->name.'</b> bed type from <b>'.$room->bed_type.'</b> to <b>'.$bed_type.'</b>.');
        }

        if (floatval($price) != floatval($room->price)) {
            $history->push('Changed <b>'.$room->subroom->name.'</b> price from <b>&euro;'.$room->price.'</b> to <b>&euro;'.$price.'</b>.');
            $createPdfSnapshot = true;
        }

        if (floatval($duration_discount) != floatval($room->duration_discount)) {
            $history->push('Changed <b>'.$room->subroom->name.'</b> duration discount from <b>&euro;'.$room->duration_discount.'</b> to <b>&euro;'.$duration_discount.'</b>.');
            $createPdfSnapshot = true;
        }

        if (!$createPdfSnapshot) {
            if ($addons && count($addons) > 0) {
                foreach ($addons as $id => $value) {
                    $booking_addon = BookingAddon::find($id);

                    if (floatval($booking_addon->price) != floatval($value['price'])) {
                        $createPdfSnapshot = true;
                        break;
                    }
                }
            }

            if (!$createPdfSnapshot && $offers && count($offers) > 0) {
                foreach ($offers as $id => $value) {
                    $booking_discount = BookingRoomDiscount::find($id);

                    if (floatval($booking_discount->discount_value) != floatval($value)) {
                        $createPdfSnapshot = true;
                        break;
                    }
                }
            }
        }

        // generate and store existing invoice to storage if this booking already confirmed
        if ($createPdfSnapshot && $booking->status === 'CONFIRMED') {
            $this->storePDFInvoiceHistory($booking);
        }

        $room->update([
            'price' => $this->convertCommaToDecimal(request('price')),
            'duration_discount' => $this->convertCommaToDecimal(request('duration_discount')),
            'bed_type' => request('bed_type'),
        ]);

        if ($addons && count($addons) > 0) {
            foreach ($addons as $id => $value) {
                $booking_addon = BookingAddon::find($id);

                if (floatval($booking_addon->price) != floatval($value['price'])) {
                    $history->push('Changed <b>'.$room->subroom->name.'</b> addon price ('.$booking_addon->details->name.') from <b>&euro;'.$booking_addon->price.'</b> to <b>&euro;'.$value['price'].'</b>.');
                }

                if (isset($value['amount']) && intval($booking_addon->amount) != intval($value['amount'])) {
                    $history->push('Changed <b>'.$room->subroom->name.'</b> board rental days from <b>'.$booking_addon->amount.'</b> days to <b>'.$value['amount'].'</b> days.');
                    $booking_addon->update([
                        'amount' => intval($value['amount']),
                    ]);
                }

                $booking_addon->update([
                    'guests' => $value['guests'],
                    'price' => $this->convertCommaToDecimal($value['price']),
                ]);
            }
        }

        if ($offers && count($offers) > 0) {
            foreach ($offers as $id => $value) {
                $booking_discount = BookingRoomDiscount::find($id);

                if (floatval($booking_discount->discount_value) != floatval($value)) {
                    $history->push('Changed <b>'.$room->subroom->name.'</b> special offer price ('.$booking_discount->offer->name.') from <b>&euro;'.$booking_discount->price.'</b> to <b>&euro;'.$value.'</b>.');
                }

                $booking_discount->update([
                    'discount_value' => $this->convertCommaToDecimal($value),
                ]);
            }
        }

        if ($history->count() > 0) {
            $history = $history->toArray();

            $booking->histories()->create([
                'user_id' => auth()->user()->id,
                'info_type' => 'slate',
                'action' => 'Update booking room price',
                'details' => '<b>'.auth()->user()->name.'</b> updated booking room. '.implode('<br />', $history),
                'ip_address' => request()->ip(),
            ]);
        }

        $this->paymentService->refreshPayment($booking);

        return response('OK');
    }

    /**
     * SEND BOOKING CONFIRMATION EMAIL.
     *
     * @param object $booking
     * @param mixed  $email
     */
    public function sendConfirmationEmail($booking, $email = '')
    {
        if ('' == $email) {
            $recipient = $booking->guest->details->email;
            $name = $booking->guest->details->full_name;
        } else {
            $recipient = $email;
            $name = '';
        }

        Mail::to($recipient, $name)->send(new \App\Mail\Booking\OrderConfirmed($booking));

        return true;
    }

    /**
     * SEND BOOKING PENDING EMAIL.
     *
     * @param object $booking
     * @param mixed  $email
     */
    public function sendPendingEmail($booking, $email = '')
    {
        $recipient = $booking->guest->details->email;
        $name = $booking->guest->details->full_name;

        Mail::to($recipient, $name)->send(new \App\Mail\Booking\OrderPending($booking));
    }

    /**
     * GENERATE BOOKING REF NUMBER.
     *
     * @return string
     */
    public function generateBookingRef()
    {
        //$today = date("Ymd");
        //$today = mt_rand(100000000, 599999999);
        $domain = DB::table('domains')->where('tenant_id', tenant('id'))->first();

        return Str::upper(substr($domain->domain, 0, 3) .'-'. time() .'-'. Str::random(3));
    }

    /**
     * CALCULATE FLEXIBLE ADDONS PRICE.
     *
     * @param object $request
     * @param mixed  $addon
     * @param mixed  $amount
     * @param mixed  $guest
     * @param array  $seasons
     *
     * @return float
     */
    public function calculateAddon($addon, $amount, $guest, $seasons = null): float
    {
        $base_price = $addon->base_price;
        $total = 0;

        if ($addon->rate_type == 'Fixed') {
            $total = $base_price;
        }

        if ($addon->rate_type == 'Day') {
            $total = $base_price * intval($amount);
            if ($addon->prices_count > 0) {
                $addon_price = $addon->prices->where('min_amount', $amount)->first();

                if ($addon_price) {
                    $total = $addon_price->price * $addon_price->min_amount;
                }
            }
        }

        $total = $total * intval($guest);

        return floatval(round($total, 2));
    }

    /**
     * CALCULATE FLEXIBLE TRANSFER PRICE.
     *
     * @param mixed  $transfer
     * @param mixed  $duration
     * @param mixed  $guest
     *
     * @return float
     */
    public function calculateTransfer($transfer, $duration, $guest): float
    {
        $transfers_price = $transfer->prices->where('guest', intval($guest))->first();

        $total = 0;

        if ($transfer->is_complimentary == 1 && $transfer->complimentary_min_nights <= $duration) {
            $total = floatval(0);
        } else {
            $total = floatval(round($transfers_price->price ? $transfers_price->price : 0, 2));
        }

        return $total;
    }

    /**
     * PERFORM SEARCH.
     *
     * @param object $bookings
     * @param object $request
     * @param boolean $deleted
     *
     * @return object
     */
    public function filterBookings($bookings, Request $request, bool $is_deleted = false)
    {
        if ($request->has('email') && '' != $request->email) {
            $bookings->whereHas('guests.details', function ($q) use ($request) {
                $q
                    ->where('email', 'LIKE', '%'.$request->email.'%')
                ;
            });
        }

        if ($request->has('stay_dates') && '' != $request->stay_dates) {
            $dates = explode(' - ', $request->stay_dates);
            if (count($dates) == 2) {
                $checkin_date_from = date('Y-m-d', strtotime($dates[0]));
                $checkout_date_to = date('Y-m-d', strtotime($dates[1]));
    
                $bookings->where(function ($query) use ($checkin_date_from, $checkout_date_to) {
                    $query->where(function ($q) use ($checkin_date_from, $checkout_date_to) {
                        $q->where('check_in', '<=', $checkout_date_to)
                          ->where('check_out', '>=', $checkin_date_from);
                    });
                });
            }
        }

        if ($request->has('guest_name') && '' != $request->guest_name) {
            $bookings->whereHas('guests.details', function ($q) use ($request) {
                $q
                    ->where('fname', 'LIKE', '%'.$request->guest_name.'%')
                    ->orWhere('lname', 'LIKE', '%'.$request->guest_name.'%')
                ;
            });
        }

        if ($request->has('voucher_code') && $request->voucher_code != '') {
            $bookings->where('voucher', $request->voucher_code);
        }

        if ($request->has('country') && '' != $request->country) {
            $bookings->whereHas('guests.details', function ($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->has('payment_status') && '' != $request->payment_status) {
            $bookings->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->has('min_price') && '' != $request->min_price) {
            $bookings->whereHas('payment', function ($q) use ($request) {
                $q->where('total', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price') && '' != $request->max_price) {
            $bookings->whereHas('payment', function ($q) use ($request) {
                $q->where('total', '<=', $request->max_price);
            });
        }

        if ($request->has('min_guest') && '' != $request->min_guest) {
            $bookings->has('guests', '>=', $request->min_guest);
        }

        if ($request->has('max_guest') && '' != $request->max_guest) {
            $bookings->has('guests', '<=', $request->max_guest);
        }

        if ($request->has('method') && '' != $request->method) {
            $bookings->whereHas('payment', function ($q) use ($request) {
                $q->where('methods', $request->method);
            });
        }

        if ($request->has('camps') && is_array($request->camps)) {
            $bookings->whereIn('location_id', $request->camps);
        }

        if ($request->has('room') && '' != $request->room) {
            $bookings->whereHas('rooms', function ($q) use ($request) {
                $q->where('room_id', intval($request->room));
            });
        }

        if ($request->has('user') && '' != $request->user) {
            $user = User::find($request->user);
            $column = $user->hasRole('Agent') ? 'agent_id' : 'source_id';

            $bookings->where($column, intval($request->user));
        }

        if ($request->has('channel') && '' != $request->channel) {
            $bookings->where('channel', $request->channel);
        }

        if ($request->has('channelExclude') && '' != $request->channelExclude) {
            $bookings->where(function ($q) use ($request) {
                if ($request->has('status') && in_array('DRAFT', $request->status)) {
                    $q
                        ->where('channel', '!=', $request->channelExclude)
                        ->orWhere('status', 'DRAFT');
                } else {
                    $q
                        ->where('channel', '!=', $request->channelExclude);
                }
            });
        }

        if ($request->has('statusExclude') && '' != $request->statusExclude) {
            $bookings->where('status', '!=', $request->statusExclude);
        }

        if ($request->has('opportunity') && '' != $request->opportunity) {
            $bookings->where('opportunity', $request->opportunity);
        }

        if ($request->has('status') && '' != $request->status && $request->status != 'CANCELLED') {
            $bookings
                ->when(is_array($request->status), fn ($q) => $q->whereIn('status', $request->status))
                ->when(!is_array($request->status), fn ($q) => $q->where('status', $request->status));
        } else {
            if (!$request->has('id')) {
                $bookings
                    ->when(!$is_deleted, function ($q) {
                        $q
                            ->where('status', '!=', 'DRAFT')
                            ->where('status', '!=', 'PENDING')
                            ->where('status', '!=', 'RESERVED')
                            ->where('status', '!=', 'EXPIRED')
                            ->where('status', '!=', 'ABANDONED');
                    });
            }
        }

        if ($request->has('booking_date_from') && UtilService::isValidDate($request->booking_date_from) && '' != $request->booking_date_from) {
            $bookings->where('created_at', '>=', date('Y-m-d', strtotime($request->booking_date_from)));
        }

        if ($request->has('booking_date_to') && UtilService::isValidDate($request->booking_date_to) && '' != $request->booking_date_to) {
            $bookings->where('created_at', '<=', date('Y-m-d', strtotime($request->booking_date_to)).' 23:59:59');
        }

        if ($request->has('checkin_date_from') && UtilService::isValidDate($request->checkin_date_from) && '' != $request->checkin_date_from) {
            $bookings->where('check_in', '>=', date('Y-m-d', strtotime($request->checkin_date_from)));
        }

        if ($request->has('checkin_date_to') && UtilService::isValidDate($request->checkin_date_to) && '' != $request->checkin_date_to) {
            $bookings->where('check_in', '<=', date('Y-m-d', strtotime($request->checkin_date_to)).' 23:59:59');
        }

        if ($request->has('checkout_date_from') && UtilService::isValidDate($request->checkout_date_from) && '' != $request->checkout_date_from) {
            $bookings->where('check_out', '>=', date('Y-m-d', strtotime($request->checkout_date_from)));
        }

        if ($request->has('checkout_date_to') && UtilService::isValidDate($request->checkout_date_to) && '' != $request->checkout_date_to) {
            $bookings->where('check_out', '<=', date('Y-m-d', strtotime($request->checkout_date_to)).' 23:59:59');
        }

        if ($request->has('has_addons') && $request->has_addons != '') {
            $bookings->whereHas('rooms.addons', function ($q) use ($request) {
                $q->whereIn('extra_id', $request->has_addons);
            });
        }

        return $bookings;
    }

    /**
     * GET TOTAL STAY.
     *
     * @param string $email
     *
     * @return int
     */
    public function getTotalStay($email)
    {
        $bookings = Booking::with(['guest.details'])
            ->whereHas('guests.details', function ($q) use ($email) {
                $q->where('email', $email);
            })
            ->where('status', 'CONFIRMED')
            ->get()
        ;

        $stay = 1;
        $last_checkout = '';

        foreach ($bookings as $booking) {
            if ('' == $last_checkout) {
                $stay = 1;
                $last_checkout = $booking->check_out;
            } else {
                $start = new Carbon($last_checkout);
                $end = new Carbon($booking->check_in);
                $now = new Carbon();

                // make sure we only calculate the booking that's already checked out!!
                if ($end->diffInDays($start) >= 30 && $now->gt($end)) {
                    ++$stay;
                }

                $last_checkout = $end->format('Y-m-d');
            }
        }

        return $stay;
    }

    public function getTotalStayByBookings($guest_bookings)
    {
        $bookings = collect();

        foreach ($guest_bookings as $guest_booking) {
            if ('CONFIRMED' == $guest_booking->booking?->status) {
                $bookings->push($guest_booking->booking);
            }
        }

        $stay = 1;
        $last_checkout = '';

        foreach ($bookings as $booking) {
            if ('' == $last_checkout) {
                $stay = 1;
                $last_checkout = $booking->check_out;
            } else {
                $start = new Carbon($last_checkout);
                $end = new Carbon($booking->check_in);
                $now = new Carbon();

                // make sure we only calculate the booking that's already checked out!!
                if ($end->diffInDays($start) >= 30 && $now->gt($end)) {
                    ++$stay;
                }

                $last_checkout = $end->format('Y-m-d');
            }
        }

        $return = [];

        if (1 == $stay) {
            $return = [
                'text' => '-',
                'number' => null,
                'html' => '-',
            ];
        } elseif (2 == $stay) {
            $return = [
                'text' => '3*',
                'number' => 2,
                'html' => str_repeat('<i class="fa fa-fw fa-star text-danger"></i>', 3),
            ];
        } elseif (3 == $stay) {
            $return = [
                'text' => '4*',
                'number' => 3,
                'html' => str_repeat('<i class="fa fa-fw fa-star text-warning"></i>', 4),
            ];
        } elseif ($stay > 3) {
            $return = [
                'text' => '5*',
                'number' => $stay,
                'html' => str_repeat('<i class="fa fa-fw fa-star text-success"></i>', 5),
            ];
        }

        return $return;
    }

    public function recalculateSPGrandTotal()
    {
        $total_addon = session()->has('sp_extras') && session('sp_extras') ? session('sp_extras')->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        }) : 0;

        return (intval(session('sp_price')) * intval(session('sp_guest'))) + floatval($total_addon);
    }

    public function recalculateGrandTotal(): float
    {
        $room = session('room');

        $total_addon = $room['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        $total_transfer = $room['transfers']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });

        return floatVal($room['accommodation_price']) + floatVal($room['tax']) + floatval($total_addon) + floatval($total_transfer);
    }

    public function assignBedToRoom($ref): array
    {
        $booking = Booking::with(['rooms.room'])->where('ref', $ref)->first();

        if (!$booking) {
            return [
                'result' => 'failed',
                'message' => 'Booking is not found'
            ];
        }

        $rooms = $booking->rooms;
        $room_keys = [];
        $response = [];
        $queries = [];
        $index = [];
        $exclude_ids = [];

        foreach ($rooms as $room) {
            // find how many room we need
            $key = 'room_' . $room->room_id . '_' . $room->from->format('Ymd') . '_' . $room->to->format('Ymd');
            if (!isset($room_keys[$key])) {
                $index[$key] = 0;
                $room_keys[$key] = [
                    'total' => 0,
                    'room_id' => $room->room_id
                ];
            }

            $room_keys[$key]['total'] += $room->is_private ? $room->subroom->beds : 1;
            array_push($exclude_ids, $room->id);
        }

        foreach ($rooms as $room) {
            $key = 'room_' . $room->room_id . '_' . $room->from->format('Ymd') . '_' . $room->to->format('Ymd');
            $data = $this->getEmptySpaceRoomId($room->from, $room->to, $room->room_id, $room_keys[$key]['total'], $exclude_ids);

            if (!$data['is_available']) {
                // early exit when finding unavailable room
                return [
                    'result' => 'failed',
                    'message' => $data['name'] . ' is not available on ' . $room->from->format('d.m.Y') . ' to ' . $room->to->format('d.m.Y')
                ];
            }

            array_push($queries, [
                'booking_room_id' => $room->id,
                'room_id' => $data['room_id'],
                'subroom_id' => $data['available_rooms'][$index[$key]]['id'],
                'bed' => $data['available_rooms'][$index[$key]]['bed'],
            ]);

            $index[$key]++;
        }

        if (count($queries) > 0) {
            foreach ($queries as $query) {
                BookingRoom::find($query['booking_room_id'])->update([
                    'room_id' => $query['room_id'],
                    'subroom_id' => $query['subroom_id'],
                    'bed' => $query['bed'],
                ]);
            }
        }

        return ['result' => 'success'];
    }

    public function getEmptySpaceRoomId($check_in, $check_out, $room_id, $guest, $exclude_ids = [])
    {
        $date_start = new Carbon($check_in);
        $date_end = new Carbon($check_out);

        $dates = [
            'start' => $date_start->format('Y-m-d'),
            'end' => $date_end->format('Y-m-d'),
            'duration' => $date_start->diffInDays($date_end),
        ];

        $rooms = Room::orderBy('sort', 'asc')
            ->with([
                'rooms',
                'prices',
                'progressive_prices:id,room_id,beds,amount',
                'location:id,max_discount,min_discount,duration_discount',
            ])
            ->where('id', $room_id)
            ->get([
                'id', 'capacity', 'default_price', 'location_id', 'empty_fee_low', 'empty_fee_main', 'empty_fee_peak',
                'room_type', 'price_type', 'limited_threshold', 'name', 'allow_private', 'bed_type'
            ])
        ;

        $occupancy = $this->roomService->getRoomOccupancy($dates['start'], $dates['end'], $rooms, $guest, $exclude_ids);

        if ($occupancy) {
            $rooms_list = $this->roomService->buildRoomList($rooms, $occupancy, $dates, null, null, 1);
            $rooms_list = $rooms_list->sortBy('pos')->all();
            $availability = $this->roomService->getAvailabilityList($rooms_list, $guest, true);
        } else {
            $availability = [];
        }

        return isset($availability[$room_id]) ? $availability[$room_id] : false;
    }

    /**
     * Find email history by slug and booking id
     *
     * @param string $slug
     * @param int    $booking_id
     *
     * @return EmailHistory
     */
    public function getEmailHistory($slug, $booking_id)
    {
        return EmailHistory::whereType($slug)
            ->where('booking_id', $booking_id)
            ->first();
    }

    public function calculateTax($percentage, $amount): float
    {
        $add = floatVal(100 + $percentage);

        return (($amount / $add) * $percentage);
    }

    public function calculateDPTax(float $percentage, float $booking_price, float $amount, float $price): float
    {
        $difference = UtilService::calculatePercentageDifference($booking_price, $price);

        $taxable_amount = $amount * $difference / 100;

        $tax = $this->calculateTax($percentage, $taxable_amount);

        return (round($tax, 2));
    }

    public function displayTaxInfo($session, $location): string
    {
        $total_addon = is_array($session['addons']) || is_a($session['addons'], 'Illuminate\Support\Collection') ? $this->getTotalAddons($session) : floatVal($session['addons']);

        $price = $session['accommodation_price'] ?? $session['price'];
        $hotel_tax = $this->calculateTax($location->hotel_tax, $price);
        $goods_tax = $this->calculateTax($location->goods_tax, $total_addon);

        $vat = number_format($hotel_tax + $goods_tax, 2);

        $hotel_tax_percent = number_format($location->hotel_tax);
        $goods_tax_percent = number_format($location->goods_tax);

        if ($total_addon > 0) {
            return 'Price contains <b>&euro;'. $vat .'</b> VAT ('. $hotel_tax_percent .'% from &euro;'. number_format($price, 2) .' and '. $goods_tax_percent .'% from &euro;'. number_format($total_addon) .')';
        } else {
            return 'Price contains <b>&euro;'. $vat .'</b> VAT ('. $hotel_tax_percent .'% from &euro;'. number_format($price, 2) .')';
        }
    }

    public function getTaxInfo($session, $location): array
    {
        $total_addon = is_array($session['addons']) || is_a($session['addons'], 'Illuminate\Support\Collection') ? $this->getTotalAddonsWithApplicableTax($session) : floatVal($session['addons']);

        $price = $session['accommodation_price'] ?? $session['price'];
        $hotel_tax = $this->calculateTax($location->hotel_tax, $price);
        $goods_tax = $this->calculateTax($location->goods_tax, $total_addon);
        $non_taxable_addons = $session['non_taxable_addons'] ?? 0;
        $dp_tax = null;

        $vat = number_format($hotel_tax + $goods_tax, 2);

        $hotel_tax_percent = number_format($location->hotel_tax);
        $goods_tax_percent = number_format($location->goods_tax);

        if ($session['total_paid']) {
            $dp_tax = [
                'hotel_tax' => $this->calculateDPTax($location->hotel_tax, ($price + $total_addon + $non_taxable_addons), $session['total_paid'], $price),
                'goods_tax' => $this->calculateDPTax($location->goods_tax, ($price + $total_addon + $non_taxable_addons), $session['total_paid'], $total_addon),
            ];
        }

        if ($total_addon > 0) {
            return [
                'vat' => $vat,
                'hotel_tax_percent' => $hotel_tax_percent,
                'hotel_tax' => $hotel_tax,
                'goods_tax_percent' => $goods_tax_percent,
                'goods_tax' => $goods_tax,
                'dp_tax' => $dp_tax
            ];
        } else {
            return [
                'vat' => $vat,
                'hotel_tax_percent' => $hotel_tax_percent,
                'hotel_tax' => $hotel_tax,
                'dp_tax' => $dp_tax
            ];
        }
    }

    public function getTotalAddons($session)
    {
        if (!isset($session['addons'])) {
            return 0;
        }

        return $session['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });
    }

    // TO DO
    public function getTotalAddonsWithApplicableTax(array $session)
    {
        if (!isset($session['addons'])) {
            return 0;
        }

        return $session['addons']->reduce(function ($total, $item) {
            $total += floatval($item['total']);

            return $total;
        });
    }

    public function formatPhoneNumber($number)
    {
        $number = str_replace('+', '', $number);

        return str_replace(' ', '', $number);
    }

    public function convertFromIDR($value)
    {
        $rate = 16075;

        return round($value / $rate);
    }

    public function convertFromEUR($value)
    {
        $rate = 16075;

        return round($value * $rate);
    }

    public function convertCommaToDecimal($value)
    {
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }

    public function generateQRcode($url)
    {
        return null;
    }

    public function generateCheckinDetailHTML($booking)
    {
        if (!$booking) {
            return;
        }

        $html = '<div class="row">';
        $html .= '<div class="col-md-8">REF: <strong>#' . $booking->ref . '</strong></div>';
        $html .= '<div class="col-md-4 text-right">';
        $html .= '<span class="badge ' . $booking->status_badge . ' text-uppercase font-size-sm">';
        $html .= $booking->booking_status . '</span></div>';
        $html .= '</div><hr>';

        $guestCount = $booking->guests->count();
        for ($i = 0; $i < $guestCount; $i++) {
            $notLastRecord = ($guestCount - 1) > $i;
            $marginRow = $notLastRecord ? '' : ' mb-4';
            $guest = $booking->guests[$i];
            $html .= '<div class="row mb-1"><div class="col-md-12">';

            if (! blank($guest->check_in_at)) {
                $html .= '<i class="fas fa-fw fa-circle-check text-success mr-1"></i>';
                $html .= '<label class="font-weight-bold text-uppercase mb-0">' . $guest->details->full_name;
                $html .= '<span class="text-success ml-3">' . $guest->check_in_at->format('d.m H:i') . '</span>';
                $html .= '</label>';
            } else {
                $html .= '<div class="form-check">';
                $html .= '<input class="form-check-input" type="checkbox" name="guests" id="guest' . $guest->id
                            . '" value="' . $guest->id . '">';
                $html .= '<label class="form-check-label font-weight-bold text-uppercase" for="guest' . $guest->id
                            . '">' . $guest->details->full_name . '</label>';
                $html .= '</div>';
            }

            $html .= '</div></div>';

            $html .= '<div class="row' . $marginRow . '">';
            $html .= '<div class="col-md-8">';

            foreach ($guest->rooms as $room_info) {
                $html .= '<p class="mb-0"><strong>' . $room_info->room->subroom->name . '</strong> '
                            . $room_info->room->bed_type . '</p>';
                if ($room_info->room->addons->count() > 0) {
                    $html .= '<ul class="pl-3 mb-0">';
                    foreach ($room_info->room->addons as $addon) {
                        $html .= '<li>' . $addon->details->name . '</li>';
                    }
                    $html .= '</ul>';
                }
            }

            $html .= '</div>';
            $html .= '<div class="col-md-4 font-weight-bold text-right">' . $booking->check_in->format('d.m') . ' - '
                        . $booking->check_out->format('d.m') . '</div>';
            $html .= '</div>';

            if ($notLastRecord) {
                $html .= '<hr class="my-2">';
            }
        }

        return $html;
    }

    public function generateAddonDetailHTML($booking)
    {
        if (!$booking) {
            return;
        }

        $html = '<div class="row">';
        $html .= '<div class="col-md-8">REF: <strong>#' . $booking->ref . '</strong></div>';
        $html .= '<div class="col-md-4 text-right">';
        $html .= '<span class="badge ' . $booking->status_badge . ' text-uppercase font-size-sm">';
        $html .= $booking->booking_status . '</span></div>';
        $html .= '</div><hr>';

        $guestCount = $booking->guests->count();
        for ($i = 0; $i < $guestCount; $i++) {
            $htmlAddon = '';
            $addonIds = [];
            $guest = $booking->guests[$i];
            $addonChecked = false;
            $addonTime = now();
            foreach ($guest->rooms as $room_info) {
                if ($room_info->room->addons->count() > 0) {
                    $htmlAddon .= '<p class="mb-0"><strong>' . $room_info->room->subroom->name . '</strong> '
                                    . $room_info->room->bed_type . '</p>';
                    $htmlAddon .= '<ul class="pl-3 mb-0">';
                    foreach ($room_info->room->addons as $addon) {
                        $htmlAddon .= '<li>' . $addon->details->name . '</li>';
                        $addonIds[] = $addon->id;

                        if (! blank($addon->check_in_at)) {
                            $addonChecked = true;
                            $addonTime = $addon->check_in_at;
                        }
                    }
                    $htmlAddon .= '</ul>';
                }
            }

            if (!blank($guest->check_in_at) && !blank($htmlAddon)) {
                $notLastRecord = ($guestCount - 1) > $i;
                $marginRow = $notLastRecord ? '' : ' mb-4';

                $html .= '<div class="row mb-1"><div class="col-md-12">';

                if ($addonChecked) {
                    $html .= '<i class="fas fa-fw fa-circle-check text-success mr-1"></i>';
                    $html .= '<label class="font-weight-bold text-uppercase mb-0">' . $guest->details->full_name;
                    $html .= '<span class="text-success ml-3">' . $addonTime->format('d.m H:i') . '</span>';
                    $html .= '</label>';
                } else {
                    $html .= '<div class="form-check">';
                    $html .= '<input class="form-check-input" type="checkbox" name="guests" id="guest' . $guest->id
                                . '" value="' . $guest->id . '">';
                    $html .= '<input type="hidden" id="addon' . $guest->id . '" value="'
                                . Arr::join($addonIds, ',') . '">';
                    $html .= '<label class="form-check-label font-weight-bold text-uppercase" for="guest' . $guest->id
                                . '">' . $guest->details->full_name . '</label>';
                    $html .= '</div>';
                }

                $html .= '</div></div>';

                $html .= '<div class="row' . $marginRow . '">';
                $html .= '<div class="col-md-8">' . $htmlAddon . '</div>';
                $html .= '<div class="col-md-4 font-weight-bold text-right">' . $booking->check_in->format('d.m')
                            . ' - ' . $booking->check_out->format('d.m') . '</div>';
                $html .= '</div>';

                if ($notLastRecord) {
                    $html .= '<hr class="my-2">';
                }
            }
        }

        return $html;
    }

    public function guestCheckIn($guests)
    {
        BookingGuest::whereIn('id', $guests)->update(['check_in_at' => now()]);
    }

    public function addonCheckIn($guests)
    {
        foreach ($guests as $guest) {
            $bookingGuest = BookingGuest::with(['rooms', 'rooms.room', 'rooms.room.addons'])
                            ->where('id', $guest['id'])
                            ->first();

            if ($bookingGuest) {
                foreach ($bookingGuest->rooms as $room_info) {
                    foreach ($room_info->room->addons as $addon) {
                        if (in_array($addon->id, $guest['addons'])) {
                            BookingAddon::where('id', $addon->id)->update(['check_in_at' => now()]);
                        }
                    }
                }
            }
        }
    }
}
