<?php

namespace App\Mail\Classes;

use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Models\Classes\ClassMultiPassPayment;
use App\Services\Classes\ClassBookingService;
use App\Services\Classes\MailService;
use App\Services\Classes\MultiPassService;
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
use Illuminate\Support\Str;

class MultipassConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $profile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public ClassMultiPassPayment $booking,
        public EmailTemplate         $email,
    )
    {
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
        $bookingService = new ClassBookingService();
        $mailService = new MailService();

        $template = Blade::render('Booking.emails.templates.' . $this->booking->tenant_id . '.' . $this->email->template);
        $parsed_template = $mailService->transformMultiPassBody($template, $this->booking);

        $taxes = [
            'goods_tax_percent' => $this->booking->location->goods_tax,
            'goods_tax' => $bookingService->calculateTax($this->booking->location->goods_tax, $this->booking->total),
        ];

        return new Content(
            view: 'Classes.emails.bookings.multipass-confirmed',
            with: [
                'tax_info' => $bookingService->getTaxInfo($this->booking->total, $this->booking->location),
                'template' => $parsed_template,
                'taxes' => $taxes
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

        $file = MultiPassService::preparePDFInvoice($this->booking);
        $pdf = $file['pdf'];

        array_push($arr, Attachment::fromData(fn() => $pdf->output(), $this->booking->ref . '-' . Str::slug($this->booking->multiPass->name) . '.pdf')->withMime('application/pdf'));

        if ($email->documents_count > 0) {
            foreach ($email->documents as $doc) {
                $pdf_doc = PDF::loadview('Booking.documents.pdf-template', [
                    'title' => $doc->document->title,
                    'body' => $doc->document->content,
                    'footer' => url('/doc/' . $doc->document->slug),
                ]);

                array_push($arr, Attachment::fromData(fn() => $pdf_doc->output(), $doc->document->slug . '.pdf')->withMime('application/pdf'));
            }
        }

        return $arr;
    }
}
