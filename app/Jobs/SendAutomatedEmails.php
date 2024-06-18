<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Mail\Booking\AutomatedEmail;

class SendAutomatedEmails implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Booking $booking,
        public EmailTemplate $task
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipient = $this->booking->guest->details->email;
        $name = $this->booking->guest->details->full_name;

        $mail = Mail::to($recipient, $name);
        $task = $this->task;

        if ($task->send_date_column == 'status') {
            $timing = null;
            switch ($task->time_unit) {
                case 'minutes':
                    $timing = now()->addMinutes($task->send_time);
                    break;

                case 'hours':
                    $timing = now()->addHours($task->send_time);
                    break;

                case 'days':
                    $timing = now()->addDays($task->send_time);
                    break;
            }
            $mail = $mail->later($timing, new AutomatedEmail($this->task, $this->booking));
        } else {
            $mail = $mail->send(new AutomatedEmail($this->task, $this->booking));
        }

        return $mail;
    }
}
