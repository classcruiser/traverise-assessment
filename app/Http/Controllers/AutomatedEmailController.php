<?php

namespace App\Http\Controllers;

use App\Jobs\SendAutomatedEmails;
use App\Mail\Booking\AutomatedEmail;
use App\Models\Booking\EmailTemplate;
use App\Services\Booking\MailService;
use Illuminate\Support\Facades\Mail;

class AutomatedEmailController extends Controller
{
    public function __construct(
        protected string $key = 'tVaXUr1eGaxY9n8pfbQ8YhsvPpnJ4dUpDwPSjj2oipdEa4ifrZC6Pif2FBbCPjJi'
    ) {
    }

    public function run()
    {
        $this->validateKey();

        $tasks = EmailTemplate::query()
            ->with(['rooms', 'condition', 'documents', 'addons'])
            ->withCount('documents', 'addons', 'rooms')
            ->where('is_scheduled', 1)
            ->where('slug', 'overnight-stay')
            ->whereNotIn('send_date_column', ['status'])
            ->get();

        if (!$tasks) {
            return response('DONE!');
        }

        foreach ($tasks as $task) {
            $date = MailService::getModifiedDate($task);

            $bookings = MailService::getFilteredBookings($task, $date)
                ->get()
                ->when($task->condition, function ($bookings) use ($task) {
                    return $bookings->filter(function ($booking) use ($task) {
                        return MailService::filterConditions($task->condition, $booking);
                    });
                })
                ->when($task->rooms->count(), function ($bookings) use ($task) {
                    return $bookings->filter(function ($booking) use ($task) {
                        return $booking->rooms->whereIn('room_id', $task->rooms->pluck('room_id'));
                    });
                })
                ->map(function ($booking) use ($task) {
                    if (request()->has('send')) {
                        $recipient = $booking->guest->details->email;
                        $name = $booking->guest->details->full_name;

                        $mail = Mail::to($recipient, $name);

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
                            $mail = $mail->later($timing, new AutomatedEmail($task, $booking));
                        } else {
                            $mail = $mail->send(new AutomatedEmail($task, $booking));
                        }
                    } else {
                        SendAutomatedEmails::dispatch($booking, $task);
                    }
/*
                    $booking->emails()->create([
                        'type' => $task->slug,
                        'notes' => 'Sent'
                    ]);*/
                });;
        }

        return response('DONE');
    }

    private function validateKey()
    {
        if ((!request()->has('key') || request('key') !== $this->key) && strtoupper(config('app.env')) == 'PRODUCTION') {
            abort(401, 'Unauthorized access');
        }
    }
}
