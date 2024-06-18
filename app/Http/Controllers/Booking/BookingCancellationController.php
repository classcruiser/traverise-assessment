<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Models\Booking\Profile;
use Illuminate\Support\Str;
use PDF;

class BookingCancellationController extends Controller
{
    public function invoice(string $ref, int $id)
    {
        $booking = Booking::with(['cancellation', 'payment'])->where('ref', $ref)->firstOrFail();

        if (!$booking->cancellation) {
            return redirect()->route('tenant.booking.show', [$ref]);
        }

        $profile = Profile::where('tenant_id', $booking->tenant_id)->first();

        $grand_total = $booking->grand_total;

        if (!is_null($booking->payment->methods)) {
            $grand_total = $booking->grand_total + $booking->payment->processing_fee;
        }

        $cancellation_fee_amount = ($booking->cancellation->cancellation_fee / 100 ) * $grand_total;

        $pdf = [
            'pdf' => PDF::loadView('Booking.cancellation.invoice', compact('booking', 'profile', 'cancellation_fee_amount')),
            'filename' => $booking->cancellation->ref.'-'.Str::slug($booking->location->name).'.pdf',
        ];

        return $pdf['pdf']->download($pdf['filename']);
    }
}