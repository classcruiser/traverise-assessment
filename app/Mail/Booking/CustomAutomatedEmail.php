<?php

namespace App\Mail\Booking;

use App\Models\Booking\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomAutomatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $slug;
    public $tenant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($slug, $data, $tenant)
    {
        $this->data = $data;
        $this->slug = $slug;
        $this->tenant = $tenant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = EmailTemplate::whereSlug($this->slug)->where('tenant_id', $this->tenant)->first();

        $subject = str_replace('{ref}', $this->data['booking']->ref, $email->subject);

        $camp = isset($this->data['camp']) ? $this->data['camp'] : null;
        $name = isset($this->data['name']) ? $this->data['name'] : null;
        $ref = isset($this->data['ref']) ? $this->data['ref'] : null;
        $check_in = isset($this->data['check_in']) ? $this->data['check_in'] : null;
        $check_out = isset($this->data['check_out']) ? $this->data['check_out'] : null;
        $open_balance = isset($this->data['open_balance']) ? $this->data['open_balance'] : null;
        $template = 'Booking.emails.templates.'. $email->tenant_id .'.'. $email->template;

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->replyTo(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('Booking.emails.automated.index')
                    ->with([
                        'email' => $email,
                        'camp' => $camp,
                        'name' => $name,
                        'ref' => $ref,
                        'template' => $template,
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                        'open_balance' => $open_balance,
                    ]);
    }
}
