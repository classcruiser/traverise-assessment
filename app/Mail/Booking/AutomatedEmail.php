<?php

namespace App\Mail\Booking;

use App\Models\Booking\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use App\Models\Booking\Booking;
use App\Models\Booking\Profile;
use App\Services\Booking\BookingService;
use App\Services\Booking\MailService;
use PDF;

class AutomatedEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public EmailTemplate $email,
        public Booking $booking,
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $booking = $this->booking;
        $email = $this->email;

        $message = $this->from(config('mail.from.address'), config('mail.from.name'));

        $profile = Profile::where('tenant_id', $booking->tenant_id)->first();

        $subject = $email->subject;
        $subject = MailService::transformBody($email->subject, $booking);
        $template = Blade::render('Booking.emails.templates.'. $booking->tenant_id.'.'. $email->template);
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

        $message = $message
            ->view('Booking.emails.automated.index', [
                'profile' => $profile,
                'template' => $parsed_template,
            ]);

        return $message;
    }
}
