<?php

namespace App\Http\Controllers;

use App\Mail\Booking\PaymentConfirmed;
use App\Mail\Guest\OtherGuestResetPassword;
use App\Models\Booking\Guest;
use App\Models\Booking\Payment;
use App\Models\Booking\PaymentStripe;
use App\Models\Booking\PaymentTransfer;
use App\Models\Booking\StripeCharge;
use App\Models\Classes\ClassMultiPassPayment;
use App\Models\Classes\ClassMultiPassPaymentRecord;
use App\Models\Classes\ClassPayment;
use App\Models\Classes\ClassPaymentRecord;
use App\Models\Tenant;
use App\Services\Booking\PaymentService;
use App\Services\Classes\ClassBookingService;
use App\Services\Classes\MultiPassService;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
    protected $client;
    protected $stripe_service;
    protected $secretKey;

    protected $type;

    protected $log;

    protected $booking;

    protected $payment;
    protected $eventObject;

    protected $stripeAccount;

    protected $tenant;

    public function __construct(StripeService $stripe_service)
    {
        $this->secretKey = config('app.env') == 'production' ? config('stripe.live_secret_key') : config('stripe.test_secret_key');
        $this->client = new StripeClient($this->secretKey);
        $this->stripe_service = $stripe_service;

        $this->log = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/stripe_webhook.log'),
        ]);
    }

    public function startOnboarding(string $id, string $account_id)
    {
        $tenant = Tenant::where('id', $id)->where('stripe_account_id', "acct_{$account_id}")->firstOrFail();

        $params = [
            'account' => $tenant->stripe_account_id,
            'refresh_url' => route('stripe.onboarding', ['id' => $id, 'account_id' => $account_id]),
            'return_url' => route('stripe.onboarding-finish', ['id' => $id, 'account_id' => $account_id]),
            'type' => 'account_onboarding'
        ];

        try {
            $response = $this->client->accountLinks->create($params);
        } catch (\Exception $e) {
            return response("Cannot connect to Stripe. Error: {$e->getMessage()}", 500);
        }

        if (!isset($response['url']) || $response['url'] == '') {
            return response("Cannot get onboarding URL from Stripe.", 500);
        }

        return redirect($response['url']);
    }

    public function finishOnboarding(string $id, string $account_id)
    {
        $tenant = Tenant::where('id', $id)->where('stripe_account_id', "acct_{$account_id}")->firstOrFail();

        $onboarding_status = $this->stripe_service->getOnboardingStatus($tenant->stripe_account_id);

        if (!$onboarding_status) {
            $tenant->update(['stripe_onboarding_process' => 0]);

            return response('ONBOARDING DONE. YOU MAY CLOSE THIS WINDOW.');
        }

        return response('ONBOARDING ON PROCESS');
    }

    public function webhook()
    {
        $this->log->info('=========== WEBHOOK STARTED =========== ');

        Stripe::setApiKey($this->secretKey);
        $endpoint_secret = config('stripe.webhook_secret');

        $payload = request()->getContent();
        $event = null;

        $this->log->debug('Payload ', ['payload' => $payload]);

        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            $this->log->error('Construct event is failed', $e->getMessage());
            abort(400);
        }

        $this->eventObject = $event->data->object;
        $this->stripeAccount = $event->account;

        if ($this->stripeAccount === env('STRIPE_TEST_ACCOUNT')) {
            // from a DEMO tenant
            $endpoint_secret = env('STRIPE_TEST_WEBHOOK_SECRET');
            Stripe::setApiKey(env('STRIPE_TEST_SECRET_KEY'));
        }

        if ($endpoint_secret && config('app.env') == 'production') {
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            try {
                $event = Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } catch (SignatureVerificationException $e) {
                $this->log->error('Signature is not valid', $e->getMessage());
                abort(400);
            }
        }

        $payment_link = $this->eventObject->metadata->payment_link;
        $this->type = $this->eventObject->metadata->type;
        $this->booking = null;

        $this->log->info('Order type is: ' . $this->type);
        $this->log->info('Event Type is: ' . $event->type);

        switch ($this->type) {
            case 'booking':
                $this->payment = Payment::withTrashed()
                    ->with(['booking.rooms.room', 'records'])
                    ->where('link', $payment_link)
                    ->first();
                if (!$this->payment) {
                    $this->log->info('Cannot find payment link:'. $payment_link);
                    return response(null, 200);
                }
                $this->booking = $this->payment->booking;
                break;

            case 'class':
                $this->payment = ClassPayment::withTrashed()
                    ->with(['records', 'booking'])
                    ->where('link', $payment_link)
                    ->first();
                if (!$this->payment) {
                    $this->log->info('Cannot find payment link:'. $payment_link);
                    return response(null, 200);
                }
                $this->booking = $this->payment->booking;
                break;

            case 'multi-pass':
                $this->payment = ClassMultiPassPayment::withTrashed()
                    ->with('records')
                    ->where('link', $payment_link)
                    ->first();
                if (!$this->payment) {
                    $this->log->info('Cannot find payment link:'. $payment_link);
                    return response(null, 200);
                }
                break;

            case null:
                $this->log->error('Payment type is null');
                return ['response' => 'Payment type is null', 'code' => 500];
        }

        if (!$this->payment) {
            $this->log->error('Payment not found');
            return ['response' => 'Payment not found', 'code' => 500];
        }

        switch ($event->type) {
            case 'payment_intent.created':
                return $this->paymentIntentCreated();

            case 'payment_intent.payment_failed':
                $this->payment->records()->where('session_id', $this->eventObject->id)->update([
                    'notes' => isset($this->eventObject->charges->data[0]) ? ($this->eventObject->charges->data[0]->failure_code . ' ' . $this->eventObject->charges->data[0]->failure_message) : '',
                    'status' => 'FAILED'
                ]);

                $this->log->info("Webhook completed successfully \n");
                return ['response' => null, 'code' => 200];

            case 'payment_intent.canceled':
                // cancel payment record
                $record = $this->payment->records()->where('session_id', $this->eventObject->id);
                if ($record) {
                    $record->update([
                        'notes' => is_array($this->eventObject->charges->data) && count($this->eventObject->charges->data) > 0 ? ($this->eventObject->charges->data[0]->failure_code . ' ' . $this->eventObject->charges->data[0]->failure_message) : 'automatic',
                        'status' => 'CANCELED'
                    ]);
                }

                $this->log->info("Webhook completed successfully \n");
                return ['response' => null, 'code' => 200];

            case 'charge.succeeded':
                $this->log->info("Webhook completed successfully \n");
                return ['response' => null, 'code' => 200];

            case 'payment_intent.succeeded':
                return $this->paymentIntentSucceededEvent();
                
            default:
                return ['response' => 'Unknown event type ' . $event->type, 'code' => 400];
        }

        return response(null, 200);
    }

    public function paymentIntentCreated(): array
    {
        // create payment transfer record here to make sure it is ALWAYS created
        $data = [
            'methods' => 'stripe',
            'unique_id' => $this->eventObject->metadata->unique_id,
            'amount' => $this->eventObject->metadata->amount,
            'amount_paid' => $this->eventObject->amount / 100,
            'paid_at' => null,
            'verify_by' => null,
            'verified_at' => null,
            'session_id' => $this->eventObject->id,
        ];

        switch ($this->type) {
            case 'booking':
                $data['is_manual'] = 0;
                $data['created_at'] = now();
                break;
            case 'class':
                $data['data'] = json_encode($this->eventObject);
                break;
            case 'multi-pass':
                $data['data'] = json_encode($this->eventObject);
                break;
        }

        // this is problematic for multi-pass
        $this->payment->records()->create($data);

        if ($this->booking) {
            $this->booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'attempting payment',
                'details' => '<b>PaymentIntent</b> with the ID of ' . $this->eventObject->id . ' is created',
            ]);
        }

        $this->log->info("Webhook completed successfully \n");
        return ['response' => null, 'code' => 200];
    }

    public function paymentIntentSucceededEvent()
    {
        $amount = floatVal($this->eventObject->metadata->amount);
        $amount_received = floatVal($this->eventObject->amount_received / 100);
        $unique_id = $this->eventObject->metadata->unique_id;

        switch ($this->type) {
            case 'booking':
                $record = PaymentTransfer::where('unique_id', $unique_id)->first();
                break;
            case 'class':
                $record = ClassPaymentRecord::where('unique_id', $unique_id)->first();
                break;
            case 'multi-pass':
                $record = ClassMultiPassPaymentRecord::with('payment')->where('unique_id', $unique_id)->first();
                break;
        }

        if (!$record) {
            $this->log->error('Payment record not found', [
                'type' => $this->type,
                'unique_id' => $unique_id,
            ]);
            return ['response' => 'Payment record not found', 'code' => 500];
        }

        $this->tenant = Tenant::where('id', $this->eventObject->metadata->tenant_id)->first();

        $pi_data = PaymentIntent::retrieve([
            'id' => $this->eventObject->id,
            'expand' => ['charges.data.balance_transaction']
        ], [
            'stripe_account' => $this->stripeAccount
        ]);

        $record->update([
            'notes' => null,
            'status' => $this->eventObject->status == 'succeeded' ? 'paid' : $this->eventObject->status,
            'paid_at' => now(),
            'verify_by' => 1,
            'verified_at' => now(),
            'amount_paid' => $amount_received,
            'data' => $pi_data
        ]);

        $guest = Guest::query()->where('tenant_id', $this->tenant->id)
            ->where('email', $this->eventObject->charges->data[0]->billing_details->email)
            ->first();

        if ($guest) {
            $guest->update([
                'stripe_customer_id' => $this->eventObject->charges->data[0]->customer,
            ]);
        }

        switch ($this->type)
        {
            case 'class':
                return $this->proceedClassType($record, $amount);

            case 'multi-pass':
                return $this->proceedMultiPassType($record);

            case 'booking':
                return $this->proceedBookingType($record, $amount, $amount_received);

            default:
                return ['response' => 'Unknown booking type ' . $this->type, 'code' => 400];
        }
    }

    public function proceedBookingType ($record, $amount, $amount_received)
    {
        $this->updateBookingPayment($record, $amount, $amount_received);

        DB::transaction(function () {
            $pi_id = $this->eventObject->id;
            $ch_id = $this->eventObject->charges->data[0]->id;

            $check = StripeCharge::where('pi_id', $pi_id)->where('ch_id', $ch_id)->count();

            if ($check <= 0) {
                StripeCharge::insert([
                    'pi_id' => $pi_id,
                    'ch_id' => $ch_id,
                    'booking_id' => null,
                    'finish' => 0,
                    'response' => '',
                    'created_at' => now()
                ]);
            }
        });

        $this->refreshPayment();

        $paymentService = new PaymentService();
        $paymentService->createSequentialInvoice($this->payment, 'booking');

        try {
            $this->log->info('Attempting to send payment confirmation email to '. $this->payment->booking->guest->details->email);
            Mail::to($this->payment->booking->guest->details->email, $this->payment->booking->guest->details->full_name)->send(new PaymentConfirmed($this->payment, $record));
        } catch (\Exception $e) {
            $this->log->error('Send PaymentConfirmed email is failed, message: ' . $e->getMessage());
            return ['response' => 'Send PaymentConfirmed email is failed', 'code' => 500];
        }

        $this->log->info("Webhook completed successfully \n");
        return ['response' => null, 'code' => 200];
    }

    public function updateBookingPayment($record, $amount, $amount_received)
    {
        PaymentStripe::updateOrCreate([
            'payment_id' => $this->payment->id,
            'stripe_id' => $this->eventObject->id,
        ], [
            'intent' => $this->eventObject->charges->data[0]->payment_intent,
            'payment_link' => $this->payment->link,
            'payment_transfer_id' => $record->id,
            'currency' => $this->eventObject->currency,
            'rates' => $this->eventObject->metadata->payment_rate,
            'mode' => 'payment',
            'method' => $this->eventObject->payment_method_types[0],
            'status' => $this->eventObject->status == 'succeeded' ? 'paid' : $this->eventObject->status,
            'amount' => $amount,
            'amount_paid' => $amount_received,
            'user_email' => $this->eventObject->charges->data[0]->billing_details->email,
        ]);

        $this->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished payment',
            'details' => '<b>Guest</b> successfully paid the booking with the <b>PaymentIntent</b> ID of ' . $this->eventObject->id . '.',
            'ip_address' => request()->ip(),
        ]);

        $this->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished booking',
            'details' => '<b>System</b> updated the booking status to <b>COMPLETED</b>.',
        ]);

        $this->payment->update([
            'deleted_at' => null,
        ]);
    }

    public function proceedClassType($record, $amount)
    {
        $paymentService = new PaymentService();
        $classBookingService = new ClassBookingService();
        $paymentService->createSequentialInvoice($this->payment, 'class');

        if ($this->eventObject->status == 'succeeded') {
            $record->update([
                'amount' => $amount,
            ]);

            $this->payment->update([
                'status' => 'COMPLETED',
                'methods' => 'stripe',
                'total' => $this->payment->total - ($this->eventObject->metadata->voucher_value ?? 0) - ($this->eventObject->metadata->pass_value ?? 0),
                'deleted_at' => null,
            ]);

            $this->payment->booking()->update([
                'status' => 'CONFIRMED',
                'deleted_at' => null,
            ]);

            $this->payment->booking->refresh();

            if ($this->eventObject->metadata->voucher_id) {
                $this->payment->booking()->update([
                    'discount_value' => (float)$this->eventObject->metadata->voucher_value,
                    'class_multi_passes_id' => (int)$this->eventObject->metadata->voucher_id,
                ]);
            }

            if ($this->eventObject->metadata->pass_id) {
                $this->payment->booking()->update([
                    'discount_value' => $this->eventObject->metadata->pass_value,
                    'class_multi_passes_id' => (int)$this->eventObject->metadata->pass_id,
                    'class_multi_pass_payment_id' => (int)$this->eventObject->metadata->pass_payment_id,
                ]);

                // update remaining usage
                $class_payment = ClassMultiPassPayment::find($this->eventObject->metadata->pass_payment_id);
                if ($class_payment) {
                    $class_payment->update([
                        'remaining' => ($class_payment->remaining - $this->eventObject->metadata->pass_usage) < 0 ? 0 : ($class_payment->remaining - $this->eventObject->metadata->pass_usage),
                    ]);
                }
            }

            $this->booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'finished payment',
                'details' => '<b>Guest</b> successfully paid the booking using <b>PaymentIntent</b> with the ID of ' . $this->eventObject->id . '.',
                'ip_address' => request()->ip(),
            ]);
        }

        $this->payment->refresh();

        // send email to guest
        $sendStatus = $classBookingService->sendBookingConfirmedEmail($this->payment->booking);

        if (!$sendStatus['status']) {
            $this->log->error('Send booking confirmation email FAILED, error message: ', $sendStatus['message']);
        }

        $this->log->info("Webhook completed successfully \n");
        return ['response' => null, 'code' => 200];
    }

    public function proceedMultiPassType($record)
    {
        $order = $record->payment;

        $paymentService = new PaymentService();
        $paymentService->createSequentialInvoice($this->payment, 'multi-pass');

        if ($this->eventObject->status == 'succeeded') {
            $this->payment->update([
                'status' => 'COMPLETED',
                'methods' => 'stripe',
                'deleted_at' => null,
            ]);

            $this->payment->update([
                'remaining' => MultiPassService::getRemainingUsage($this->payment)
            ]);
        }

        $order->refresh();

        $sendStatus = MultiPassService::sendConfirmationEmail($order);
        if (!$sendStatus['status']) {
            $this->log->error('Send confirmation email FAILED, error message: ', $sendStatus['message']);
        }

        if ($this->payment->is_other_guest) {
            MultiPassService::sendEmailToOtherGuest($this->payment);
        }

        $this->log->info("Webhook completed successfully \n");
        return ['response' => null, 'code' => 200];
    }

    public function refreshPayment()
    {
        $this->payment->booking->refresh();

        $this->payment->booking->payment->update([
            'total' => $this->payment->booking->grand_total,
        ]);

        if ($this->payment->booking->payment->open_balance_with_commission > 0) {
            if ('COMPLETED' == $this->payment->booking->payment->status) {
                $this->payment->booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'slate',
                    'action' => 'Update payment status',
                    'details' => '<b>System</b> updated payment status to <b>PARTIAL</b>',
                ]);

                $this->payment->booking->payment->update([
                    'status' => 'PARTIAL',
                    'methods' => 'stripe',
                ]);
            }
        } else {
            $this->payment->booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'Update payment status',
                'details' => '<b>System</b> updated payment status to <b>COMPLETED</b>',
                'ip_address' => request()->ip(),
            ]);

            $this->payment->booking->payment->update([
                'status' => 'COMPLETED',
                'methods' => 'stripe',
            ]);
        }
    }
}
