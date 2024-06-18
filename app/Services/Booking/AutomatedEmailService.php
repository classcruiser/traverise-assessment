<?php

namespace App\Services\Booking;

use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Jobs\SendAutomatedEmails;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AutomatedEmailService
{
    public static function getBookingColumns(): array
    {
        return [
            [
                'name' => 'Booking',
                'columns' => [
                    'check_in' => 'Check in',
                    'check_out' => 'Check out',
                    'deposit_expiry' => 'Deposit expired',
                    'created_at' => 'Booking submitted',
                    'confirmed' => 'Booking confirmed'
                ]
            ],
        ];
    }

    public static function checkAndSendEmailWhenBookingIsApproved(Booking $booking)
    {
        $tasks = EmailTemplate::query()
            ->with(['rooms', 'condition', 'documents', 'addons'])
            ->withCount('documents', 'addons')
            ->where('is_scheduled', 1)
            ->whereIn('send_date_column', ['status'])
            ->get();

        if (count($tasks) <= 0) {
            return;
        }

        foreach ($tasks as $task) {
            $bookings = Booking::with(['payment'])
                ->where('status', 'CONFIRMED')
                ->whereDoesntHave('emails', function ($q) use ($task) {
                    $q->where('type', $task->slug);
                })
                ->when($task->addons_count, function ($q) use ($task) {
                    $q->whereHas('rooms.addons', fn ($sub) => $sub->whereIn('extra_id', $task->addons->pluck('extra_id')));
                })
                ->chunk(100, function ($bookings) use ($task) {
                    foreach ($bookings as $booking) {
                        $note = false;
                        if (self::filterConditions($task->condition, $booking)) {
                            // send email
                            $note = true;
                            SendAutomatedEmails::dispatch($booking, $task);
                        }

                        // log email
                        $booking->emails()->create([
                            'type' => $task->slug,
                            'notes' => $note ? 'Sent' : 'Skip'
                        ]);
                    }
                });
        }
    }

    protected static function filterConditions($condition, $booking): bool
    {
        if (!$condition || empty($condition->column)) {
            return true;
        }

        $value = $condition->value;

        $check = null;

        switch ($condition->column) {
            case 'payment_record':
                $check = $booking->payment->records_count;
                break;

            case 'check_in':
                $check = $booking->check_in;
                break;

            case 'check_out':
                $check = $booking->check_out;
                break;

            case 'open_balance':
                $check = floatVal($booking->payment->open_balance);
                break;

            case 'booking_submitted':
                $check = $booking->created_at;
                break;
        }

        if (is_null($check)) {
            return false;
        }

        switch ($condition->operator) {
            case 'is':
                return $check == $value;
                break;

            case 'is_not':
                return $check != $value;
                break;

            case 'is_empty':
                return empty($check) || !($check) || is_null($check) || $check <= 0;
                break;

            case 'is_not_empty':
                return !empty($check) && ($check) && !is_null($check);
                break;

            case 'lt':
                return ($check instanceof Carbon) ? $check->lt(Carbon::createFromFormat('Y-m-d', $value)) : $check < $value;
                break;

            case 'lte':
                return ($check instanceof Carbon) ? $check->lte(Carbon::createFromFormat('Y-m-d', $value)) : $check <= $value;
                break;

            case 'gt':
                return ($check instanceof Carbon) ? $check->gt(Carbon::createFromFormat('Y-m-d', $value)) : $check > $value;
                break;

            case 'gte':
                return ($check instanceof Carbon) ? $check->gte(Carbon::createFromFormat('Y-m-d', $value)) : $check >= $value;
                break;

            case 'contains':
                return Str::of($check)->contains($value);
                break;
        }

        return false;
    }
}
