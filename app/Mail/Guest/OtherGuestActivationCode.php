<?php

namespace App\Mail\Guest;

use App\Models\Booking\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtherGuestActivationCode extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $guest;
    public $subject;
    protected $activationCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $url, string $activationCode, Guest $guest)
    {
        $this->url = $url;
        $this->guest = $guest;
        $this->activationCode = $activationCode;
        $this->subject = $this->guest->fname.' '.$this->guest->lname.' bought a multi-pass for you!';
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.guest.other-guest-activation-code',
            with: [
                'guest' => $this->guest,
                'url' => $this->url,
                'activationCode' => $this->activationCode
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
        return [];
    }
}
