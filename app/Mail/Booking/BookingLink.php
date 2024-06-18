<?php

namespace App\Mail\Booking;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;

use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Services\Booking\BookingService;
use Illuminate\Support\Facades\Storage;
use App\Services\Booking\MailService;
use PDF;

class BookingLink extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $booking;

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
    public function build(BookingService $bookingService)
    {
        $booking = $this->booking;
        $message = $this->from(config('mail.from.address'), config('mail.from.name'));
        $data = [
            'price' => $booking->subtotal_with_discount + $booking->payment->processing_fee,
            'addons' => $booking->total_addons_price,
        ];

        $hashId = Hashids::encode($booking->id);

        $profile = Profile::where('tenant_id', tenant('id'))->first();

        $email = EmailTemplate::with(['documents'])
            ->withCount('documents')
            ->whereSlug('booking-link-email')
            ->where('tenant_id', tenant('id'))
            ->first();

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

        if ($profile->copy_email && filter_var($profile->copy_email, FILTER_VALIDATE_EMAIL)) {
            $message = $message->bcc($profile->copy_email);
        }

        $message = $message
            ->view('Booking.emails.bookings.booking-link', [
                'tax_info' => $bookingService->displayTaxInfo($data, $booking->location),
                'profile' => $profile,
                'template' => $parsed_template,
                'name' => $booking->guest->details->full_name,
                'hashId' => $hashId,
            ]);

        return $message;
    }
}
