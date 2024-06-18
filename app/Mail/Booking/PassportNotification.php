<?php

namespace App\Mail\Booking;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Booking\Booking;

class PassportNotification extends Mailable
{
    use Queueable, SerializesModels;

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
    public function build()
    {
      $booking = $this->booking;

      return $this->from('no-reply@kimasurf.com', 'KIMASURF')
                ->cc('foseminyak@thegreenroombali.com', 'FO Seminyak')
                ->cc('focanggu@thegreenroombali.com', 'FO Canggu')
                ->replyTo('info@kimasurf.com', 'KIMASURF')
                ->subject('Passport Details (#'. $booking->ref .')')
                ->view('Booking.emails.bookings.passport-notification');
    }
}
