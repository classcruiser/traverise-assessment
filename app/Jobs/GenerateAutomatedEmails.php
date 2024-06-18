<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Services\Booking\MailService;
use Illuminate\Support\Facades\Log;

class GenerateAutomatedEmails implements ShouldQueue
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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tasks = EmailTemplate::query()
            ->with(['rooms', 'condition', 'documents', 'addons'])
            ->withCount(['documents', 'addons', 'rooms'])
            ->where('is_scheduled', 1)
            ->whereNotIn('send_date_column', ['status'])
            ->get();

        if (!$tasks) {
            return;
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
                    SendAutomatedEmails::dispatch($booking, $task);

                    $booking->emails()->create([
                        'type' => $task->slug,
                        'notes' => 'Sent'
                    ]);
                });
        }
    }
}
