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
use PDF;

class PaymentSubmitted extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct(
        public Payment $payment
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

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $email = EmailTemplate::with(['documents'])->withCount('documents')->whereSlug('payment-submitted-email')->where('tenant_id', tenant('id'))->first();
        $subject = MailService::transformBody($email->subject, $booking);
        $template = Blade::render('Booking.emails.templates.'. tenant('id') .'.'. $email->template);
        $parsed_template = MailService::transformBody($template, $booking);

        $message = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject);

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

        $message = $message->view('Booking.emails.bookings.payment-submitted', [
                'booking' => $booking,
                'profile' => $profile,
                'template' => $parsed_template
            ]);
    }
}
