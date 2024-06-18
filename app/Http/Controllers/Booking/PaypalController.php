<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Payment;
use App\Models\Booking\PaypalTransaction;
use App\Models\Classes\ClassMultiPass;
use App\Models\Classes\ClassPayment;
use App\Services\Booking\PaypalService;
use App\Services\Classes\MultiPassService;
use App\Services\Classes\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaypalController extends Controller
{
    protected $paypalService;

    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function create(Request $request) : Response
    {
        $type = $request->type;

        $methodsMap = [
            'booking' => 'createBookingPayment',
            'class' => 'createClassPayment',
            'multi-pass' => 'createMultiPassPayment',
        ];

        $data = $this->{$methodsMap[$type]}($request);

        return response([
            'id' => $this->paypalService->create($data)['id'],
        ]);
    }

    public function createBookingPayment(Request $request)
    {
        $payment_link = $request->payment_link;

        $payment = Payment::with('booking')->where('link', $payment_link)->firstOrFail();

        return [
            'ref' => $payment->booking->ref,
            'location_id' => $payment->booking->location_id,
            'amount' => $payment->open_balance_with_fee,
            'amount_without_fee' => $payment->open_balance,
            'processing_fee' => $payment->processing_fee,
            'description' => 'Your booking for #' . $payment->booking->ref,
            'payment_model' => 'payments',
            'payment_id' => $payment->id,
        ];
    }

    public function createClassPayment(Request $request)
    {
        $payment_link = $request->payment_link;

        $payment_link = $request->payment_link;
        $adminAmount = $request->admin_amount;

        $payment = ClassPayment::with('booking')->where('link', $payment_link)->firstOrFail();

        $data = [
            'ref' => $payment->booking->ref,
            'location_id' => $payment->booking->location_id,
            'amount' => $adminAmount ?? ShopService::getTotalPrice(),
            'description' => 'Your booking for #' . $payment->booking->ref,
            'payment_model' => 'class_payments',
            'payment_id' => $payment->id,
        ];

        if (session()->has('class.voucher')) {
            $data = [
                ...$data,
                'voucher' => session('class.voucher')['code'],
                'voucher_id' => session('class.voucher')['id'],
                'voucher_value' => ShopService::getDiscountValue(),
            ];
        }

        if (session()->has('class.multipass-credit')) {
            $data = [
                ...$data,
                'pass' => session('class.multipass-credit')['name'],
                'pass_value' => session('class.multipass-credit')['value'],
                'pass_id' => session('class.multipass-credit')['pass_id'],
                'pass_payment_id' => session('class.multipass-credit')['pass_payment_id'],
                'pass_type' => 'CREDIT',
                'pass_usage' => session('class.multipass-credit')['value'],
            ];
        }

        if (session()->has('class.multipass-session')) {
            $data = [
                ...$data,
                'pass' => session('class.multipass-session')['name'],
                'pass_value' => ShopService::getMultiPassSessionDiscount(),
                'pass_id' => session('class.multipass-session')['pass_id'],
                'pass_payment_id' => session('class.multipass-session')['pass_payment_id'],
                'pass_type' => 'SESSION',
                'pass_usage' => ShopService::getTotalEligibleMultiPassSession(),
            ];
        }

        return $data;
    }

    public function createMultiPassPayment(Request $request)
    {
        $pass_id = (int) $request->id;

        $multiPass = ClassMultiPass::findOrFail($pass_id);
        $payment = MultiPassService::createPaymentRecord($multiPass, $request, 'paypal');

        return [
            'amount' => $multiPass->price,
            'location_id' => $payment->location_id,
            'ref' => $payment->ref,
            'description' => 'Your purchase of ' . $multiPass->name,
            'payment_model' => 'class_multi_pass_payments',
            'payment_id' => $payment->id,
        ];
    }

    public function capture(Request $request) : Response
    {
        $order_id = $request->order_id;

        $paypal = PaypalTransaction::where('order_id', $order_id)->firstOrFail();

        $response = $this->paypalService->capture($order_id, $paypal);

        return response($response);
    }
}
