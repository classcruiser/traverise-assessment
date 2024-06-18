<?php

namespace App\Mail\Booking;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Checklist extends Mailable
{
  use Queueable, SerializesModels;

  public $booking;
  public $type;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($booking, $type)
  {
    $this->booking = $booking;
    $this->type = $type;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $destination = $this->type == 'check-in' ? 'Checked In' : 'Checked Out';

    return $this->from('no-reply@kimasurf.com', 'KIMASURF')
                ->replyTo('info@kimasurf.com', 'KIMASURF')
                ->subject('Guest '. $destination .' (#'. $this->booking->ref .')')
                ->view('Booking.emails.bookings.checklist');
  }
}
