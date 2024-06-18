<?php

namespace App\Http\Controllers\Booking;

use App\Exports\Booking\PaymentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\TransferConfirmation;
use App\Mail\Booking\PaymentConfirmed;
use App\Mail\Booking\PaymentSubmitted;
use App\Models\Booking\Location;
use App\Models\Booking\LocationPaymentMethod;
use App\Models\Booking\Payment;
use App\Models\Booking\Profile;
use App\Models\Booking\PaymentTransfer;
use App\Models\Booking\User;
use App\Services\Booking\BookingService;
use App\Services\Booking\FileService;
use App\Services\Booking\PaymentService;
use App\Services\Booking\UserService;
use App\Services\Booking\StripeService;
use App\Services\Booking\TaxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    protected $bookingService;
    protected $paymentService;
    protected $userService;
    protected $fileService;
    protected $stripeService;
    protected $methods;

    public function __construct(
        BookingService $bookingService,
        PaymentService $paymentService,
        FileService $fileService,
        StripeService $stripeService,
        UserService $userService
    ) {
        $this->bookingService = $bookingService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
        $this->fileService = $fileService;
        $this->methods = [
            'banktransfer' => 'Bank Transfer',
            'creditcard' => 'Credit Card',
            'cash' => 'Cash',
            'stripe' => 'Stripe',
            'paypal' => 'Paypal',
        ];
    }

    public function index($id)
    {
        $payment = Payment::whereLink($id)->with(['booking.rooms'])->firstOrFail();

        $location = $payment->booking->location;

        if ($this->isPaymentCompleted($payment)) {
            return $this->redirectToInvoice($payment);
        }

        if ($this->hasPendingPayment($payment)) {
            $profile = Profile::where('tenant_id', tenant('id'))->first();

            return view('Booking.payments.pending-payment', [
                'method' => $this->methods[$payment->methods],
                'profile' => $profile,
            ]);
        }

        $booking = $payment->booking;

        $session_data = [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'payment_link' => $booking->payment->link,
            'name' => $booking->guest->details->full_name,
            'email' => $booking->guest->details->email,
            'amount' => $payment->open_balance,
            'amount_with_fee' => $payment->open_balance_with_fee,
            'deposit' => $payment->total_paid,
            'pending' => false,
        ];

        session($session_data);

        $today = date('Y-n-d');
        $dates = explode('-', $today);

        $data_cc = [
            'price' => round($booking->subtotal_with_discount + $booking->processing_fee, 2),
            'addons' => $booking->total_addons_price,
        ];

        $data_bank = [
            'price' => round($booking->subtotal_with_discount, 2),
            'addons' => $booking->total_addons_price,
        ];

        $total_paid = $this->paymentService->totalPaid($payment);

        $tax_info_cc = $this->bookingService->displayTaxInfo($data_cc, $booking->location);
        $tax_info_bank = $this->bookingService->displayTaxInfo($data_bank, $booking->location);

        $payment_methods = LocationPaymentMethod::with('details')->where('location_id', $location->id)->where('is_active', 1)->get();

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $paypal = $payment_methods->where('name', 'Paypal')->first();
        $stripe = $payment_methods->where('name', 'Stripe')->first();

        $payments = [
            'paypal' => [
                'active' => $paypal && $paypal->is_active,
            ],
            'stripe' => [
                'active' => $stripe && $stripe->is_active
            ]
        ];

        if ($paypal && $paypal->is_active) {
            $payments['paypal'] = array_merge([
                ...$payments['paypal'],
                ...$paypal->details->mapWithKeys(fn ($detail) => [$detail->key_name => $detail->key_value])->toArray(),
            ]);
        }

        $tax = TaxService::getActiveTaxes($booking);

        $booking_taxes = TaxService::calculateBookingTaxes($tax, $booking);

        return view('Booking.payments.index', compact(
            'booking', 'payment', 'dates', 'id', 'total_paid', 'profile',
            'tax_info_cc', 'tax_info_bank', 'payment_methods', 'payments',
            'tax', 'booking_taxes'
        ));
    }

    public function processBanktransfer(TransferConfirmation $request)
    {
        return $this->processTransfer($request);
    }

    public function processTransfer($request)
    {
        $payment = Payment::whereLink($request->payment_link)->with(['booking.rooms'])->first();
        $payment->update(['methods' => $request->payment_type]);
        $transfer = $payment->records()->create(request()->only([
            'bank_name', 'account_number', 'iban_code', 'account_owner',
        ]));

        $transfer->update([
            'paid_at' => $request->date_year . '-' . $request->date_month . '-' . $request->date_day,
            'payment_id' => $payment->id,
            'methods' => $request->payment_type,
            'amount' => null,
            'amount_paid' => null,
        ]);

        if ($request->proof) {
            $response = (new FileService())->upload(
                request('proof'),
                '/tenancy/assets/records/',
                $transfer->methods . '_' . $transfer->id . '.jpg'
            );
        }

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        if ($profile->contact_email != '' && !is_null($profile->contact_email)) {
            try {
                Mail::to($profile->contact_email)->send(new PaymentSubmitted($payment));
                // send email transfer
            } catch (\Exception $e) {}
        }

        return 'OK';
    }

    public function addRecord(Request $request)
    {
        $payment = Payment::find($request->id);
        $paid_at = Carbon::createFromFormat('d.m.Y', $request->paid_at)->format('Y-m-d');

        $new = $payment->records()->create($request->only([
            'methods',
        ]));

        $new->update([
            'amount' => $this->bookingService->convertCommaToDecimal($request->amount),
            'paid_at' => $paid_at,
            'verify_by' => auth()->user()->id,
            'verified_at' => date('Y-m-d h:i:s'),
            'is_manual' => 1,
        ]);

        $payment->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Add manual payment',
            'details' => '<b>' . auth()->user()->name . '</b> add manual payment (<b>' . strtoupper($new->methods) . '</b>) <b>&euro;' . $new->amount . '</b>',
            'ip_address' => request()->ip(),
        ]);

        if (!$payment->methods) {
            $payment->update([
                'methods' => $new->methods,
            ]);
        }

        $payment->refresh();

        $this->paymentService->createSequentialInvoice($payment);

        $payment->update([
            'status' => 'PARTIAL',
        ]);

        $this->paymentService->refreshPayment($payment->booking);

        return redirect('bookings/' . $payment->booking->ref . '?' . time());
    }

    public function invoice($invoice)
    {
        $payment = Payment::whereLink($invoice)->with(['booking.rooms'])->firstOrFail();
        $booking = $payment->booking;

        $total_paid = $this->paymentService->totalPaid($payment);

        //return (new \App\Mail\Booking\PaymentConfirmed($transfer->payment, $transfer))->render();

        $data = [
            'price' => $booking->subtotal_with_discount + $booking->paid_processing_fee,
            'addons' => $booking->total_addons_price,
        ];

        $tax_info = $this->bookingService->displayTaxInfo($data, $booking->location);

        $tax = TaxService::getActiveTaxes($booking);

        $booking_taxes = TaxService::calculateBookingTaxes($tax, $booking);

        return view('Booking.payments.invoice', compact('payment', 'booking', 'tax_info', 'total_paid', 'tax', 'booking_taxes'));
    }

    public function getRecord($id)
    {
        return $this->paymentService->getPaymentRecord($id);
    }

    public function sendConfirmedPaymentEmail(Request $request)
    {
        $id = $request->id;
        $transfer = PaymentTransfer::with(['payment.booking.guest'])->find($id);
        $payment = $transfer->payment;
        $booking = $payment->booking;
        $index = request('index', 1);

        $recipient = $booking->source_type == 'Agent' ? $booking->agent->email : $booking->guest->details->email;
        $name = $booking->source_type == 'Agent' ? $booking->agent->full_name : $booking->guest->details->full_name;

        Mail::to($recipient, $name)->send(new PaymentConfirmed(payment: $payment, transfer: $transfer, index: $index, is_final: false));

        $payment->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Send email',
            'details' => '<b>' . auth()->user()->name . '</b> send confirmed payment email to <b>' . $payment->booking->guest->details->email . '</b>',
            'ip_address' => request()->ip(),
        ]);

        return response([
            'status' => 'success',
        ]);
    }

    /**
     * Verify the payment.
     *
     * @param  Request  $request
     * @return Response
     */
    public function verifyPayment(Request $request)
    {
        $method = $request->methods;
        $id = $request->id;
        $amount = $this->bookingService->convertCommaToDecimal($request->amount);
        $transfer = PaymentTransfer::find($id);
        $payment = Payment::find($transfer->payment_id);
        $payment_id = $transfer->payment_id;
        $total_paid = intval($payment->total_paid) + intval($amount);

        $payment_status = intval($total_paid) >= intval($payment->total) ? 'COMPLETED' : 'PARTIAL';

        $transfer->update([
            'amount' => $amount,
            'verify_by' => auth()->user()->id,
            'verified_at' => date('Y-m-d h:i:s'),
        ]);

        $payment->update([
            'status' => $payment_status,
            'methods' => $method,
        ]);

        $payment->refresh();

        $this->paymentService->createSequentialInvoice($payment);

        Mail::to($payment->booking->guest->details->email, $payment->booking->guest->details->full_name)
            ->send(new \App\Mail\Booking\PaymentConfirmed(payment: $payment, transfer: $transfer, index: request('index', 1), is_final: false));

        $payment->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Verify payment',
            'details' => '<b>' . auth()->user()->name . '</b> verified payment with amount of &euro;' . $amount,
            'ip_address' => request()->ip(),
        ]);

        return [
            'status' => 'success',
            'url' => '/bookings/' . $payment->booking->ref,
        ];
    }

    public function updatePaymentRecord(Request $request)
    {
        $id = $request->id;
        $amount = $request->amount;

        $transfer = PaymentTransfer::find($id);
        $payment = Payment::find($transfer->payment_id);

        $payment->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'slate',
            'action' => 'Update payment',
            'details' => '<b>' . auth()->user()->name . '</b> updated payment record from <b>&euro; ' . $transfer->amount . '</b> to <b>&euro; ' . $amount . '</b>',
            'ip_address' => request()->ip(),
        ]);

        $transfer->update([
            'amount' => $amount,
        ]);

        $this->paymentService->refreshPayment($payment->booking);

        return response([
            'status' => 'success',
        ]);
    }

    public function startStripe($payment_link)
    {
        return response()->json($this->stripeService->start($payment_link));
    }

    public function successStripe($payment_link, $session_id)
    {
        $this->stripeService->success($payment_link, $session_id);

        $payment = Payment::with('booking')->where('link', $payment_link)->firstOrFail();

        //$this->bookingService->sendParkingQRCode($payment->booking);

        return redirect()->route('tenant.payment.thank-you', ['id' => $payment_link]);
    }

    public function thankYou($id)
    {
        return view('Booking.payments.thank-you', ['id' => $id]);
    }

    public function thankYouBank()
    {
        return view('Booking.payments.thank-you-bank');
    }

    public function isPaymentCompleted($payment)
    {
        return 'COMPLETED' == $payment->status;
    }

    public function hasPendingPayment($payment)
    {
        return $payment->methods && ('FINISHED' != $payment->status && 'PARTIAL' != $payment->status);
    }

    public function redirectToInvoice($payment)
    {
        return redirect()->to('invoice/' . $payment->link);
    }

    public function indexAdmin(Request $request)
    {
        $locations = Location::with(['rooms'])->orderBy('name', 'asc')->get();
        $is_agent = $this->userService->is_agent();

        $users = User::orderBy('name')->get();

        $payments = PaymentTransfer::with(['payment.booking.location', 'payment.booking.guest.details', 'user'])
            ->whereHas('payment.booking', function ($q) use ($is_agent) {
                return $q
                    ->when($is_agent, fn ($query) => $query->where('agent_id', auth()->user()->id))
                    ->where('status', 'CONFIRMED');
            })
            ->orderBy('created_at', 'desc');

        if ($request->has('ref') && '' != $request->ref) {
            $payments->whereHas('payment.booking', function ($q) {
                return $q->where('ref', request('ref'));
            });
        }

        // search here
        $payments = $this->paymentService->filterPayments($payments, $request);

        if ($request->has('export')) {
            $date = date('ymd_His');

            return Excel::download(new PaymentExport($payments), 'PAYMENT_REPORT_' . $date . '.xlsx');
        }

        $payments = $payments->paginate(25);

        return view('Booking.payments.admin.index', compact('payments', 'locations', 'users'));
    }

    public function deleteRecord($id, $ref)
    {
        $record = PaymentTransfer::with(['payment'])->find($id);
        $payment_id = $record->payment->id;
        $check = public_path('storage/transfers/' . $record->methods . '_' . $record->id . '.jpg');
        if (file_exists($check)) {
            @unlink($check);
        }

        $record->payment->booking->histories()->create([
            'user_id' => auth()->user()->id,
            'info_type' => 'danger',
            'action' => 'Delete payment record',
            'details' => '<b>' . auth()->user()->name . '</b> deletes <b>' . strtoupper($record->methods) . '</b> payment record with amount of <b>&euro;' . ($record->amount) . '</b>',
            'ip_address' => request()->ip(),
        ]);

        $record->delete();
        $payment = Payment::with(['booking'])->withCount('records')->find($payment_id);

        if ($payment->records_count <= 0) {
            $payment->update(['methods' => null]);
        }

        $this->paymentService->refreshPayment($payment->booking);

        return redirect('bookings/' . $ref . '?' . time());
    }

    public function assignInvoiceNumber()
    {
        // get all payments with one or more records
        $payments = Payment::query()
            ->where('tenant_id', tenant('id'))
            ->withCount('records')
            ->get()
            ->where('records_count', '>=', 1)
            ->sortBy(fn ($record) => $record->created_at)
            ->values();

        /**
         * @var \App\Models\Booking\Payment $payment
         */
        foreach ($payments as $payment) {
            $this->paymentService->createSequentialInvoice($payment);
        }

        echo 'OK';
    }

    public function getStripeClientSecret(Request $request) : Response
    {
        $response = $this->stripeService->getClientSecret($request);

        return response($response, 200);
    }
}
