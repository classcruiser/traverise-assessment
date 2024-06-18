<?php

namespace App\Mail\Booking;

use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Services\Booking\BookingService;
use App\Services\Booking\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use PDF;

class OrderConfirmed extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $booking;
    public $dates;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build(BookingService $bookingService, MailService $mailService)
    {
        $booking = $this->booking;

        $message = $this->from(config('mail.from.address'), config('mail.from.name'));

        if ($booking->affiliation_id && $booking->affiliation->is_active && $booking->affiliation->email_received) {
            $message = $message->bcc($booking->affiliation->email, $booking->affiliation->name);
        }

        $data = [
            'price' => $booking->subtotal_with_discount + $booking->payment->processing_fee,
            'addons' => $booking->total_addons_price,
        ];

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $email = EmailTemplate::with(['documents'])->withCount('documents')->whereSlug('booking-confirmation-email')->where('tenant_id', tenant('id'))->first();
        $subject = $email->subject;
        $subject = MailService::transformBody($email->subject, $booking);
        $template = Blade::render('Booking.emails.templates.'. tenant('id') .'.'. $email->template);
        $parsed_template = MailService::transformBody($template, $booking);

        $message = $message
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

        $message = $message
            ->view('Booking.emails.bookings.confirmed', [
                'tax_info' => $bookingService->displayTaxInfo($data, $booking->location),
                'profile' => $profile,
                'template' => $parsed_template,
                'name' => $booking->guest->details->full_name
            ]);

        return $message;
    }
}
