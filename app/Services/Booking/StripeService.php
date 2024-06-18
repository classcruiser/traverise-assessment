<?php

namespace App\Services\Booking;

use App\Models\Booking\Payment;
use App\Models\Booking\PaymentTransfer;
use App\Models\Booking\Profile;
use App\Models\Booking\StripeCharge;
use App\Models\Classes\ClassMultiPassPayment;
use App\Models\Classes\ClassPayment;
use App\Services\Classes\MultiPassService;
use App\Services\Classes\ShopService;
use Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;

class StripeService
{
    protected $client;

    public function __construct()
    {
        // ...
    }

    private function init()
    {
        $profile = Profile::where('tenant_id', tenant('id'))->first();

        if (!$profile->test_mode) {
            $this->client = new \Stripe\StripeClient(env('STRIPE_LIVE_SECRET_KEY'));
            \Stripe\Stripe::setApiKey(env('STRIPE_LIVE_SECRET_KEY'));
        } else {
            $this->client = new \Stripe\StripeClient(env('STRIPE_TEST_SECRET_KEY'));
            \Stripe\Stripe::setApiKey(env('STRIPE_TEST_SECRET_KEY'));
        }
    }

    public function start($payment_link)
    {
        $this->init();
        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $payment = Payment::with(['booking.rooms.room'])->where('link', $payment_link)->first();
        $currency = request()->has('currency') ? strtoupper(request('currency')) : 'EUR';

        // pay open balance instead of the total
        $total = floatval($payment->open_balance_with_fee);
        $rates = 1;

        $unique_id = Str::random(12);

        if ($payment->booking->rooms_count > 0) {
            $description = $payment->booking->location->name.' '.$payment->booking->rooms->first()->room->name.' from '.$payment->booking->check_in->format('d.m.Y').' to '.$payment->booking->check_out->format('d.m.Y');
            $image = url(asset('images/rooms/'. tenant('id') .'_room_'. $payment->booking->rooms->first()->room->id .'.jpg'));
        } else {
            $description = 'Payment for booking #'. $payment->booking->ref;
            $image = url(asset('images/thank-you.jpg'));
        }

        // get the booking details here
        $checkout_session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount_decimal' => $total * 100,
                    'product_data' => [
                        'name' => 'Booking #'.$payment->booking->ref,
                        'description' => $description,
                        'images' => [$image],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_creation' => null,
            'success_url' => url('payment/'.$payment_link.'/success/{CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('payment/'.$payment_link.'?cancel'),
            'payment_intent_data' => [
                'application_fee_amount' => 0,
                'metadata' => [
                    'amount' => $payment->open_balance_with_fee,
                    'payment_link' => $payment_link,
                    'tenant_id' => tenant('id'),
                    'booking_ref' => $payment->booking->ref,
                    'payment_rate' => $rates,
                    'unique_id' => $unique_id,
                ],
            ],
            'metadata' => [
                'amount' => $payment->open_balance_with_fee,
                'payment_link' => $payment_link,
                'tenant_id' => tenant('id'),
                'booking_ref' => $payment->booking->ref,
                'payment_rate' => $rates,
                'unique_id' => $unique_id,
            ],
        ], [
            'stripe_account' => !$profile->test_mode ? tenant('stripe_account_id') : 'acct_1MBdWnDEOopbXeBY',
        ]);

        $transfer = $payment->records()->create([
            'unique_id' => $unique_id,
            'methods' => 'stripe',
            'amount' => $payment->open_balance,
            'amount_paid' => null,
            'paid_at' => null,
            'verify_by' => null,
            'verified_at' => null,
            'is_manual' => 0,
            'created_at' => now()
        ]);

        return ['id' => $checkout_session->id];
    }

    public function success($payment_link, $session_id)
    {
        // create default transaction now because Stripe's webhook doesn't always go with the right order
        $this->init();
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $stripe_acc_id = !$profile->test_mode ? tenant('stripe_account_id') : 'acct_1MBdWnDEOopbXeBY';

        $session = $this->client->checkout->sessions->retrieve($session_id, [], [
            'stripe_account' => $stripe_acc_id
        ]);

        $payment = Payment::with(['booking.rooms.room'])->where('link', $payment_link)->first();

        $check = PaymentTransfer::where('unique_id', $session->metadata->unique_id)->count();

        if (!$check) {
            $transfer = $payment->records()->create([
                'unique_id' => $session->metadata->unique_id,
                'methods' => 'stripe',
                'amount' => floatVal($session->metadata->amount),
                'amount_paid' => null,
                'paid_at' => null,
                'verify_by' => null,
                'verified_at' => null,
                'is_manual' => 0,
                'session_id' => $session->id,
                'created_at' => now()
            ]);
        }

        return true;
    }

    public function refreshPayment($booking)
    {
        $booking->refresh();

        $booking->payment->update([
            'total' => $booking->grand_total,
        ]);

        if ($booking->payment->open_balance > 0) {
            if ('COMPLETED' == $booking->payment->status) {
                $booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'slate',
                    'action' => 'Update payment status',
                    'details' => '<b>System</b> updated payment status to <b>PARTIAL</b>',
                ]);

                $booking->payment->update([
                    'status' => 'PARTIAL',
                    'methods' => 'stripe',
                ]);
            }
        } else {
            $booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'Update payment status',
                'details' => '<b>System</b> updated payment status to <b>COMPLETED</b>',
                'ip_address' => request()->ip(),
            ]);

            $booking->payment->update([
                'status' => 'COMPLETED',
                'methods' => 'stripe',
            ]);
        }
    }

    public function getStripeCharges()
    {
        return StripeCharge::with(['booking.payment'])
            ->where('finish', 1)
            ->where('invoice_added', 0)
            ->where('fully_paid', 1)
            ->where('retry', '<=', 3)
            ->first();
    }

    protected function getTransactions()
    {
        return StripeCharge::with(['booking.payment'])
            ->where('finish', 0)
            ->whereNotNull('booking_id')
            ->first();
    }

    public function getChargeDetails($id)
    {
        return $this->client->charges->retrieve($id, ['expand' => ['balance_transaction']]);
    }

    public function getClientSecret(Request $request) : array
    {
        $tenant = Profile::where('tenant_id', tenant('id'))->first();
        $unique_id = Str::random(12);

        $is_test = app()->environment(['local']) || $tenant->test_mode;

        $key = $is_test ? config('stripe.test_secret_key') : config('stripe.live_secret_key');
        $stripe = new \Stripe\StripeClient($key);
        $name = null;
        $email = null;

        switch (request('type')) {
            case 'class':
                $payment = ClassPayment::with('booking.guest.details')->where('link', $request->payment_link)->first();
                $name = $payment->booking?->guest?->details?->full_name;
                $email = $payment->booking?->guest?->details?->email;
                $amount = request()->has('origin') && request('origin') == 'shop' ? ShopService::getTotalPrice() : $payment->open_balance;
                $amount_with_fee = $amount * 100;

                $payment->booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'slate',
                    'action' => 'attempting payment',
                    'details' => '<b>Guest</b> attempting payment with <b>Stripe</b>. Payment amount: <b>&euro; ' . $amount . '</b>',
                    'ip_address' => request()->ip(),
                ]);
                break;
            case 'multi-pass':
                $payment = ClassMultiPassPayment::find($request->multi_pass_payment_id);
                $name = $payment->guest->full_name;
                $email = $payment->guest->email;
                $amount = $payment->open_balance;
                $amount_with_fee = $payment->open_balance_with_fee * 100;
                break;
            default:
                $payment = Payment::with('booking.guest.details')->where('link', $request->payment_link)->first();
                $name = $payment->booking?->guest?->details?->full_name;
                $email = $payment->booking?->guest?->details?->email;
                $amount = $payment->open_balance;
                $amount_with_fee = $payment->open_balance_with_fee * 100;
                break;
        }

        $metadata = [
            'amount' => $amount,
            'payment_link' => $payment->link,
            'tenant_id' => tenant('id'),
            'payment_rate' => 1,
            'unique_id' => $unique_id,
            'guest_name' => $name,
            'guest_email' => $email,
            'type' => request()->has('type') ? request('type') : 'booking',
        ];

        if (session()->has('class.voucher')) {
            $metadata = [
                ...$metadata,
                'voucher' => session('class.voucher')['code'],
                'voucher_id' => session('class.voucher')['id'],
                'voucher_value' => ShopService::getDiscountValue(),
            ];
        }

        if (session()->has('class.multipass-credit')) {
            $metadata = [
                ...$metadata,
                'pass' => session('class.multipass-credit')['name'],
                'pass_value' => session('class.multipass-credit')['value'],
                'pass_id' => session('class.multipass-credit')['pass_id'],
                'pass_payment_id' => session('class.multipass-credit')['pass_payment_id'],
                'pass_type' => 'CREDIT',
                'pass_usage' => session('class.multipass-credit')['value'],
            ];
        }

        if (session()->has('class.multipass-session')) {
            $metadata = [
                ...$metadata,
                'pass' => session('class.multipass-session')['name'],
                'pass_value' => ShopService::getMultiPassSessionDiscount(),
                'pass_id' => session('class.multipass-session')['pass_id'],
                'pass_payment_id' => session('class.multipass-session')['pass_payment_id'],
                'pass_type' => 'SESSION',
                'pass_usage' => ShopService::getTotalEligibleMultiPassSession(),
            ];
        }

        if(isset($payment->booking)){
            $metadata['booking_ref'] = $payment->booking->ref;
        }

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amount_with_fee,
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'application_fee_amount' => 0,
            'metadata' => $metadata,
        ], [
            'stripe_account' => $is_test ? config('stripe.test_account') : $tenant->stripe_id,
        ]);

        return ['clientSecret' => $paymentIntent->client_secret];
    }
}
