<?php

namespace App\Mail\Classes;

use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Models\Classes\ClassBooking;
use App\Services\Classes\MailService;
use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class AbandonedClassBooking extends Mailable
{
    use Queueable, SerializesModels;

    public $profile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct (
        public ClassBooking $booking,
        public EmailTemplate $email,
    ) {
        $this->profile = Profile::where('tenant_id', $this->booking->tenant_id)->first();
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address($this->booking->location->contact_email, $this->booking->location->name),
            ],
            subject: $this->email->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $mailService = new MailService;

        $template = Blade::render('Booking.emails.templates.'. $this->booking->tenant_id .'.'. $this->email->template);
        $parsed_template = $mailService->transformBody($template, $this->booking);

        return new Content(
            view: 'Classes.emails.bookings.class-abandoned',
            with: [
                'template' => $parsed_template,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $arr = [];
        $email = $this->email;

        $this->email->load('documents');
        $this->email->loadCount('documents');

        if ($email->documents_count > 0) {
            foreach ($email->documents as $doc) {
                $pdf_doc = PDF::loadview('Booking.documents.pdf-template', [
                    'title' => $doc->document->title,
                    'body' => $doc->document->content,
                    'footer' => url('/doc/'. $doc->document->slug),
                ]);

                array_push($arr, Attachment::fromData(fn () => $pdf_doc->output(), $doc->document->slug .'.pdf')->withMime('application/pdf'));
            }
        }

        return $arr;
    }
}
