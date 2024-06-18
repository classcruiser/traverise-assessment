<?php

namespace App\Services\Booking;

use App\Models\Booking\Payment;
use App\Models\Booking\PaymentTransfer;
use App\Models\Classes\ClassMultiPassPayment;
use App\Models\Classes\ClassPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
    * PERFORM SEARCH
    *
    * @param object $payments
    * @param object $request
    *
    * @return object
    */
    public function filterPayments($payments, $request)
    {
        if ($request->has('guest_name') && $request->guest_name != '') {
            $payments->whereHas('payment.booking.guests.details', function ($q) use ($request) {
                $q
                ->where('fname', 'LIKE', '%'. $request->guest_name .'%')
                ->orWhere('lname', 'LIKE', '%'. $request->guest_name .'%');
            });
        }

        if ($request->has('email') && $request->email != '') {
            $payments->whereHas('payment.booking.guests.details', function ($q) use ($request) {
                $q->where('email', $request->email);
            });
        }

        if ($request->has('method') && $request->method != '') {
            $payments->whereHas('payment', function ($q) use ($request) {
                $q->where('methods', $request->method);
            });
        }

        if ($request->has('dates') && $request->dates != '') {
            $dts = explode(' - ', request('dates'));
            $date_start = explode('.', $dts[0]);
            $date_start = $date_start[2] .'-'. $date_start[1] .'-'. $date_start[0];
            $date_end = explode('.', $dts[1]);
            $date_end = $date_end[2] .'-'. $date_end[1] .'-'. $date_end[0];

            $payments->where('created_at', '>=', $date_start .' 00:00:00')->where('created_at', '<=', $date_end .' 23:59:59');
        }

        return $payments;
    }

    public function createPayment($booking, $grand_total = null)
    {
        $invoice = 'INV/'. date('Y') .'/'. $this->convertMonthToRoman(date('n')) .'/'. $booking->id;
        $payment = Payment::create([
            'link' => $this->generateRandomPaymentLink(),
            'invoice_number' => $invoice,
            'booking_id' => $booking->id,
            'total' => $grand_total ? $grand_total : $booking->grand_total,
            'status' => 'DUE',
            'processing_fee' => $booking->processing_fee,
        ]);

        return $payment->id;
    }

    public function getPaymentRecord($id)
    {
        $record = PaymentTransfer::with(['payment', 'payment.booking'])->whereId($id)->get()->map(function ($item, $key) {
            $booking = $item->payment->booking;
            $file = url('tenancy/assets/records/'. $item->methods .'_'. $item->id .'.jpg');

            return [
                'id' => $item->id,
                'bank_name' => $item->bank_name,
                'methods' => $item->methods,
                'account_number' => $item->account_number,
                'account_owner' => $item->account_owner,
                'iban_code' => $item->iban_code ? $item->iban_code : '--',
                'amount' => $booking->parsePrice($item->amount),
                'default_amount' => $booking->parsePrice($item->amount),
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                'paid_at' => !is_null($item->paid_at) ? $item->paid_at->format('d.m.Y') : '-',
                'proof' => $file,
                'grand_total' => $booking->parsePrice($item->payment->total),
                'deposit_total' => $booking->parsePrice($item->payment->booking->deposit_amount),
                'open_balance' => $booking->parsePrice($item->payment->open_balance),
            ];
        });

        return $record->first();
    }

    public function refreshPayment($booking)
    {
        $booking->refresh();

        $tax_total = TaxService::calculateTotalExclusiveTax($booking);

        $booking->payment->update([
            'total' => $booking->grand_total,
            //'processing_fee' => $booking->payment->calculateProcessingFee($booking->open_balance),
        ]);

        if ($booking->payment->open_balance > 0) {
            if ($booking->payment->status == 'COMPLETED') {
                $booking->histories()->create([
                    'user_id' => null,
                    'info_type' => 'slate',
                    'action' => 'Update payment status',
                    'details' => '<b>System</b> updated payment status to <b>PARTIAL</b>'
                ]);

                $booking->payment->update([
                    'status' => 'PARTIAL'
                ]);
            } else {
                if ($booking->payment->total_paid > 0) {
                    $booking->histories()->create([
                        'user_id' => null,
                        'info_type' => 'slate',
                        'action' => 'Update payment status',
                        'details' => '<b>System</b> updated payment status to <b>PARTIAL</b>'
                    ]);

                    $booking->payment->update([
                        'status' => 'PARTIAL'
                    ]);
                } else {
                    $booking->payment->update([
                        'status' => 'DUE'
                    ]);
                }
            }
        } else {
            $booking->histories()->create([
                'user_id' => null,
                'info_type' => 'slate',
                'action' => 'Update payment status',
                'details' => '<b>System</b> updated payment status to <b>COMPLETED</b>',
                'ip_address' => request()->ip()
            ]);

            $booking->payment->update([
                'status' => 'COMPLETED'
            ]);
        }
    }

    public function createSequentialInvoice($payment, $type = 'booking'): bool
    {
        if (!is_null($payment?->invoice)) {
            return true;
        }

        $tenant_id = $payment->tenant_id;

        // calculate the current position first
        switch ($type) {
            case 'booking':
                $check = Payment::query()
                    ->where('tenant_id', $tenant_id)
                    ->where('invoice', 'LIKE', $tenant_id .'-'. date('Y') .'%')
                    ->count();
                break;
            case 'class':
                $check = ClassPayment::query()
                    ->where('tenant_id', $tenant_id)
                    ->where('invoice', 'LIKE', $tenant_id .'-'. date('Y') .'%')
                    ->count();
                break;
            case 'multi-pass':
                $check = ClassMultiPassPayment::query()
                    ->where('tenant_id', $tenant_id)
                    ->where('invoice', 'LIKE', $tenant_id .'-'. date('Y') .'%')
                    ->count();
                break;
        }

        $next = intVal($check) + 1;

        $number = str_pad($next, 7, '0', STR_PAD_LEFT);

        return $payment->update([
            'invoice' => $tenant_id .'-'. date('Y') .'-'. $number
        ]);
    }

    public function saveRecord(Payment $payment, array $data): PaymentTransfer | null
    {
        $record = null;
        DB::beginTransaction();

        try {
            $payment->update(['methods' => $data['payment_type']]);

            if ($data['payment_type'] == 'banktransfer') {
                $record = $payment->records()->create([
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'iban_code' => $data['iban_code'],
                    'account_owner' => $data['account_owner'],
                    'paid_at' => $data['date_year'] . '-' . $data['date_month'] . '-' . $data['date_day'],
                    'methods' => $data['payment_type'],
                    'amount' => null,
                    'amount_paid' => null,
                ]);
            }

            if ($data['payment_type'] == 'paypal') {
                $record = $payment->records()->create([
                    'methods' => $data['payment_type'],
                    'amount' => $data['amount'],
                    'amount_paid' => $data['amount'],
                    'unique_id' => $data['unique_id'],
                    'paid_at' => now(),
                    'verify_by' => 1,
                    'verified_at' => now(),
                    'data' => $data['data'],
                    'status' => 'paid'
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
        }

        return $record;
    }

    public static function totalPaid(Payment $payment): float
    {
        $total_paid = $payment->total_paid;
        $processing_fee = $payment->processing_fee;

        return $total_paid + $processing_fee;
    }

    public function generateRandomPaymentLink()
    {
        return strtolower(Str::random(5) .'-'. Str::random(4) .'-'. Str::random(4) .'-'. Str::random(5));
    }

    public function convertMonthToRoman($mo)
    {
        $symbols = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $symbols[$mo-1];
    }
}
