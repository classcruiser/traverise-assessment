<?php

namespace App\Services\Booking;

use App\Mail\Booking\PaymentConfirmed;
use App\Mail\Guest\OtherGuestResetPassword;
use App\Models\Booking\LocationPaymentMethod;
use App\Models\Booking\PaypalTransaction;
use App\Models\Classes\ClassMultiPassPayment;
use App\Services\Classes\ClassBookingService;
use App\Services\Classes\ClassPaymentService;
use App\Services\Classes\MultiPassService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalService
{
    protected $client;
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->client = new PayPalClient;
        $this->bookingService = $bookingService;
    }

    public function create(array $data)
    {
        $tenant_id = tenant('id');
        $config = $this->preparePaypalConfig($tenant_id, $data['location_id']);

        $this->client->setApiCredentials($config);
        $token = $this->client->getAccessToken();
        $this->client->setAccessToken($token);

        $order = $this->client->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'order_' . $data['ref'],
                    'amount' => [
                        'value' => $data['amount'],
                        'currency_code' => 'EUR'
                    ],
                    'description' => $data['description']
                ]
            ],
        ]);

        $paypal = PaypalTransaction::create([
            'payment_model' => $data['payment_model'],
            'payment_id' => $data['payment_id'],
            'order_id' => $order['id'],
            'status' => 'PENDING',
            'data' => json_encode($data),
        ]);

        $paypal->payment?->booking?->histories()->create([
            'user_id' => null,
            'info_type' => 'slate',
            'action' => 'attempting payment',
            'details' => '<b>Guest</b> attempting payment with <b>PayPal</b>. Payment amount: <b>&euro; ' . $data['amount'] . '</b>',
            'ip_address' => request()->ip(),
        ]);

        if ($data['payment_model'] == 'class_multi_pass_payments') {
            // set status to DUE so it doesn't get deleted

            $paypal->payment->records()->create([
                'session_id' => session()->getId(),
                'unique_id' => $order['id'],
                'methods' => 'paypal',
                'amount' => $paypal->payment->total,
                'amount_paid' => null,
                'paid_at' => null,
                'verify_by' => null,
                'verified_at' => null,
                'data' => $paypal->data,
                'status' => null,
            ]);

            $paypal->payment()->update([
                'status' => 'DUE'
            ]);
        }

        return $order;
    }

    public function capture($order_id, PaypalTransaction $paypal)
    {
        // get transaction order_id to get tenant_id
        $transaction = PaypalTransaction::where('order_id', $order_id)->firstOrFail();
        $tenant_id = $transaction->payment_model == 'class_multi_pass_payments' ? $transaction->payment->tenant_id : $transaction->payment->booking->tenant_id;
        $location_id = $transaction->payment_model == 'class_multi_pass_payments' ? $transaction->payment->location_id : $transaction->payment->booking->location_id;
        $config = $this->preparePaypalConfig($tenant_id, $location_id);

        $this->client->setApiCredentials($config);
        $token = $this->client->getAccessToken();
        $this->client->setAccessToken($token);

        $response = $this->client->capturePaymentOrder($order_id);

        $already_captured = !isset($response['status']) && $response['error']['details'][0]['issue'] == 'ORDER_ALREADY_CAPTURED';

        if ($already_captured || (isset($response['status']) && $response['status'] == 'COMPLETED')) {

            if (!$already_captured) {
                switch ($paypal->payment_model) {
                    case 'payments':
                        $this->completeBookingPayment ($paypal, $response);
                        break;

                    case 'class_payments':
                        $this->completeClassPayment ($paypal, $response);
                        break;

                    case 'class_multi_pass_payments':
                        $this->completeMultiPassPayment ($paypal, $response, $tenant_id);
                        break;
                }

                $paypal->update([
                    'status' => 'COMPLETED',
                    'data' => json_encode($response),
                ]);
            }

            $paypal->payment()->update(['status' => 'COMPLETED', 'methods' => 'paypal', 'total' => $amount ?? $paypal->payment->total]);
            if ($paypal->payment->booking) {
                $paypal->payment->booking()->update(['status' => 'CONFIRMED']);
            }
        }

        return $response;
    }

    protected function completeBookingPayment (PaypalTransaction $paypal, $response)
    {
        $paymentService = new PaymentService;
        $metadata = json_decode($paypal->data, true);
        $amount = $paypal->payment->open_balance;

        $paymentService->createSequentialInvoice($paypal->payment);

        $record = $paymentService->saveRecord($paypal->payment, [
            'payment_type' => 'paypal',
            'amount' => $metadata['amount_without_fee'] ?? $amount,
            'amount_paid' => $amount,
            'status' => 'paid',
            'data' => $response,
            'unique_id' => $paypal->order_id,
        ]);

        $paypal->payment->refresh();

        Mail::to($paypal->payment->booking->guest->details->email, $paypal->payment->booking->guest->details->full_name)->send(new PaymentConfirmed($paypal->payment, $record));

        $paypal->payment->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished payment',
            'details' => '<b>Guest</b> successfully paid the booking using <b>PayPal</b>',
            'ip_address' => request()->ip(),
        ]);

        $paypal->payment->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished booking',
            'details' => '<b>System</b> updated the booking status to <b>COMPLETED</b>.',
        ]);
    }

    protected function completeClassPayment (PaypalTransaction $paypal, $response)
    {
        $classBookingService = new ClassBookingService;
        $paymentService = new PaymentService;
        $classPaymentService = new ClassPaymentService;
        $metadata = json_decode($paypal->data, true);
        $amount = $paypal->payment->open_balance - ($metadata['voucher_value'] ?? 0) - ($metadata['pass_value'] ?? 0);

        $paymentService->createSequentialInvoice($paypal->payment, 'class');

        if (isset($metadata['voucher_id'])) {
            $paypal->payment->booking()->update([
                'discount_value' => (float) $metadata['voucher_value'],
                'class_multi_passes_id' => (int) $metadata['voucher_id'],
            ]);
        }

        if (isset($metadata['pass_id'])) {
            $paypal->payment->booking()->update([
                'discount_value' => (float) $metadata['pass_value'],
                'class_multi_passes_id' => (int) $metadata['pass_id'],
                'class_multi_pass_payment_id' => (int) $metadata['pass_payment_id'],
            ]);

            $class_payment = ClassMultiPassPayment::find($metadata['pass_payment_id']);
            if ($class_payment) {
                $class_payment->update([
                    'remaining' => ($class_payment->remaining - $metadata['pass_usage']) < 0 ? 0 : ($class_payment->remaining - $metadata['pass_usage']),
                ]);
            }
        }

        $classPaymentService->saveRecord($paypal->payment, [
            'payment_type' => 'paypal',
            'amount' => $amount,
            'amount_paid' => $amount,
            'status' => 'paid',
            'data' => $response,
            'unique_id' => $paypal->order_id,
        ]);

        $paypal->payment->refresh();

        $classBookingService->sendBookingConfirmedEmail($paypal->payment->booking);

        $paypal->payment->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished payment',
            'details' => '<b>Guest</b> successfully paid the booking using <b>PayPal</b>',
            'ip_address' => request()->ip(),
        ]);

        $paypal->payment->booking->histories()->create([
            'user_id' => null,
            'info_type' => 'success',
            'action' => 'finished booking',
            'details' => '<b>System</b> updated the booking status to <b>COMPLETED</b>.',
        ]);
    }

    protected function completeMultiPassPayment (PaypalTransaction $paypal, $response, $tenant_id)
    {
        $paypal->payment->records()->where('methods', 'paypal')->where('unique_id', $paypal->order_id)->update([
            'status' => 'paid',
            'amount_paid' => $paypal->payment->total,
            'paid_at' => now(),
            'verified_at' => now(),
            'verify_by' => 1,
            'data' => json_encode($response),
        ]);

        $paypal->payment()->update([
            'remaining' => MultiPassService::getRemainingUsage($paypal->payment)
        ]);

        $paymentService = new PaymentService();

        $paymentService->createSequentialInvoice($paypal->payment, 'multi-pass');

        MultiPassService::sendConfirmationEmail($paypal->payment);

        if ($paypal->payment->is_other_guest) {
            MultiPassService::sendEmailToOtherGuest($paypal->payment);
        }
    }

    protected function preparePaypalConfig(string $tenant_id, int $location_id)
    {
        $payment = LocationPaymentMethod::with('details')
            ->where('tenant_id', $tenant_id)
            ->where('location_id', $location_id)
            ->where('name', 'Paypal')
            ->firstOrFail();

        $paypal = $payment->details->mapWithKeys(fn ($detail) => [$detail->key_name => $detail->key_value])->toArray();

        return [
            'mode' => strtolower($paypal['MODE']),
            'sandbox' => [
                'client_id' => $paypal['SANDBOX_CLIENT_ID'],
                'client_secret' => $paypal['SANDBOX_CLIENT_SECRET'],
                'app_id' => 'APP-80W284485P519543T',
            ],
            'live' => [
                'client_id' => $paypal['MODE'] != 'SANDBOX' ? $paypal['LIVE_CLIENT_ID'] : '',
                'client_secret' => $paypal['MODE'] != 'SANDBOX' ? $paypal['LIVE_CLIENT_SECRET'] : '',
                'app_id' => '',
            ],

            'payment_action' => 'Sale',
            'currency' => 'EUR',
            'notify_url' => config('paypal.notify_url'),
            'locale' => 'en_US',
            'validate_ssl' => config('paypal.validate_ssl'),
        ];
    }
}
