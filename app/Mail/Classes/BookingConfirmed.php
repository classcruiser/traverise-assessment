<?php

namespace App\Mail\Classes;

use App\Models\Classes\ClassBooking;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Services\Classes\MailService;
use App\Services\Classes\ClassBookingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use PDF;

class BookingConfirmed extends Mailable
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
    public function __construct(ClassBooking $booking)
    {
        $this->booking = $booking;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build(ClassBookingService $bookingService, MailService $mailService)
    {
        $booking = $this->booking;

        $booking->load('pass');

        $booking->loadCount(['addons', 'guests', 'sessions']);

        $message = $this->from(config('mail.from.address'), config('mail.from.name'));

        $profile = Profile::where('tenant_id', $booking->tenant_id)->first();

        $email = EmailTemplate::with(['documents'])->withCount('documents')->whereSlug('booking-confirmation-email')->where('tenant_id', $booking->tenant_id)->first();
        $subject = $email->subject;
        $subject = $mailService->transformBody($email->subject, $booking);
        $template = Blade::render('Booking.emails.templates.'. $booking->tenant_id .'.'. $email->template);
        $parsed_template = $mailService->transformBody($template, $booking);

        $discount = $booking->discount_value;

        $file = $bookingService->preparePDFInvoice($booking->ref);
        $pdf = $file['pdf'];
        
        $taxes = [
            'goods_tax_percent' => $booking->location->goods_tax,
            'goods_tax' => $bookingService->calculateTax($booking->location->goods_tax, $booking->total_price - $discount),
        ];

        $message = $message
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->attachData($pdf->output(), $booking->ref.'-'.Str::slug($booking->location->name).'.pdf', [
                'mime' => 'application/pdf'
            ]);

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
            ->view('Classes.emails.bookings.confirmed', [
                'tax_info' => $bookingService->getTaxInfo($booking->total_price - $discount, $booking->location),
                'profile' => $profile,
                'template' => $parsed_template,
                'name' => $booking->guest->details->full_name,
                'taxes' => $taxes
            ]);

        return $message;
    }
}
