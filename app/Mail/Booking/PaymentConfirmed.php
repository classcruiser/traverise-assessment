<?php

namespace App\Mail\Booking;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Models\Booking\Payment;
use App\Models\Booking\PaymentTransfer;
use App\Services\Booking\BookingService;
use App\Services\Booking\MailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDF;

class PaymentConfirmed extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct(
        public Payment $payment,
        public PaymentTransfer $transfer,
        public int $index = 1,
        public bool $is_final = true,
    ) {}

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build(BookingService $bookingService, MailService $mailService)
    {
        $payment = $this->payment;
        $booking = $payment->booking;

        if ($payment->methods == 'banktransfer') {
            $payment->processing_fee = 0;
        }

        $data = [
            'price' => $booking->subtotal_with_discount + $payment->processing_fee,
            'addons' => $booking->total_addons_price,
        ];

        $profile = Profile::where('tenant_id', $booking->tenant_id)->first();

        $file = $bookingService->preparePDFInvoice($booking->ref, $this->transfer, $this->index, $this->is_final);
        
        $pdf = $file['pdf'];

        $email = EmailTemplate::with(['documents'])->withCount('documents')->whereSlug('payment-confirmation-email')->where('tenant_id', $booking->tenant_id)->first();
        $subject = MailService::transformBody($email->subject, $booking);
        $template = Blade::render('Booking.emails.templates.'. $booking->tenant_id .'.'. $email->template);
        $parsed_template = MailService::transformBody($template, $booking);

        $message = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->attachData($pdf->output(), $booking->ref.'-'.Str::slug($booking->location->name).'.pdf', [
                'mime' => 'application/pdf'
            ]);

        if ($payment->status == 'COMPLETED') {
            // send full invoice as well
            $full_file = $bookingService->preparePDFInvoice($booking->ref);
            $message = $message->attachData($full_file['pdf']->output(), $booking->ref.'-'.Str::slug($booking->location->name).'-full.pdf', [
                'mime' => 'application/pdf'
            ]);
        }

        if ($email->documents_count > 0) {
            foreach ($email->documents as $doc) {
                $pdf_doc = PDF::loadview('Booking.documents.pdf-template', [
                    'title' => $doc->document->title,
                    'body' => $doc->document->content,
                    'footer' => url('/doc/'. $doc->document->slug),
                ]);

                $message = $message->attachData($pdf_doc->output(), $doc->document->slug .'.pdf', [
                    'mime' => 'application/pdf'
                ]);
            }
        }

        if ($profile->copy_email) {
            $message = $message->bcc($mailService->validateAndFormatEmail($profile->copy_email));
        }

        $message = $message->view('Booking.emails.bookings.payment-confirmed', [
                'booking' => $booking,
                'tax_info' => $bookingService->displayTaxInfo($data, $booking->location),
                'profile' => $profile,
                'template' => $parsed_template,
            ]);
    }
}
