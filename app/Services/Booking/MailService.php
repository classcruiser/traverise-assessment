<?php

namespace App\Services\Booking;

use App\Mail\Booking\AutomatedEmail;
use App\Mail\Booking\CustomAutomatedEmail;
use App\Mail\Booking\NotifyAgentApproval;
use App\Mail\Booking\OrderCancelled;
use App\Mail\Booking\OrderConfirmed;
use App\Mail\Booking\OrderPending;
use App\Mail\Booking\BookingLink;
use App\Models\Booking\Booking;
use App\Models\Booking\EmailHistory;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\EmailTemplateCondition;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailService
{
    public static function createEmailTemplate(string $tenant_id, array $data)
    {
        $template = EmailTemplate::create([
            'tenant_id' => $tenant_id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'subject' => $data['subject'],
            'is_scheduled' => $data['is_scheduled'],
            'send_time' => $data['send_time'],
            'time_unit' => $data['time_unit'],
            'send_timing' => $data['send_timing'],
            'send_date_column' => $data['send_date_column'],
            'resource' => $data['resource'],
            'template' => $data['template'],
            'attachment' => $data['attachment'],
        ]);

        return $template;
    }
    
    /**
     * SEND BOOKING CONFIRMATION EMAIL.
     *
     * @param object $booking
     * @param mixed  $email
     */
    public static function sendConfirmationEmail($booking, $email = '')
    {
        if ($email == '') {
            $recipient = $booking->source_type == 'Agent' ? $booking->agent->email : $booking->guest->details->email;
            $name = $booking->source_type == 'Agent' ? $booking->agent->full_name : $booking->guest->details->full_name;
        } else {
            $recipient = $email;
            $name = '';
        }

        Mail::to($recipient, $name)->send(new OrderConfirmed($booking));
    }

    /**
     * SEND BOOKING LINK EMAIL.
     *
     * @param object $booking
     * @param mixed  $email
     */
    public static function sendBookingLink($booking, $email = '')
    {
        $name = $booking->guest->details->full_name;

        Mail::to($email, $name)->send(new BookingLink($booking));
    }

    public static function sendCancellationEmail($booking)
    {
        $recipient = $booking->source_type == 'Agent' ? $booking->agent->email : $booking->guest->details->email;
        $name = $booking->source_type == 'Agent' ? $booking->agent->full_name : $booking->guest->details->full_name;

        Mail::to($recipient, $name)->send(new OrderCancelled($booking));
    }

    /**
     * SEND BOOKING PENDING EMAIL.
     *
     * @param object $booking
     * @param mixed  $email
     */
    public static function sendPendingEmail($booking, $email = '')
    {
        $recipient = $booking->guest->details->email;
        $name = $booking->guest->details->full_name;

        Mail::to($recipient, $name)->send(new OrderPending($booking));
    }

    /**
     * SEND AGENT APPROVAL NOTIFICATION EMAIL.
     *
     * @param object $booking
     */
    public static function sendAgentApprovalEmail($booking)
    {
        Mail::to(config('mail.from.address'), config('mail.from.name'))->send(new NotifyAgentApproval($booking));
    }

    public static function sendAutomatedEmail($slug, $data)
    {
        $recipient = $data['recipient'];
        $name = $data['name'];

        Mail::to($recipient, $name)->send(new AutomatedEmail($slug, $data));
    }

    public static function sendCustomAutomatedEmail($slug, $data, $tenant_id)
    {
        $recipient = $data['recipient'];
        $name = $data['name'];

        Mail::to($recipient, $name)->send(new CustomAutomatedEmail($slug, $data, $tenant_id));
    }

    public static function getEmailHistory($slug, $booking_id)
    {
        return EmailHistory::whereType($slug)
            ->where('booking_id', $booking_id)
            ->first();
    }

    public static function getModifiedDate(EmailTemplate $task) : Carbon
    {
        $date = Carbon::now();

        if ('BEFORE' == $task->send_timing) {
            switch ($task->time_unit) {
                case 'minutes':
                    $date->addMinutes($task->send_time);
                break;

                case 'hours':
                    $date->addHours($task->send_time);
                break;

                case 'days':
                    $date->addDays($task->send_time);
                break;
            }
        } else {
            switch ($task->time_unit) {
                case 'minutes':
                    $date->subMinutes($task->send_time);
                break;

                case 'hours':
                    $date->subHours($task->send_time);
                break;

                case 'days':
                    $date->subDays($task->send_time);
                break;
            }
        }

        return $date;
    }

    public static function getFilteredBookings(EmailTemplate $task, Carbon $date) : Builder
    {
        $bookings = Booking::with(['payment', 'guest.details', 'guests', 'rooms'])
            ->withCount(['guests', 'payment_records'])
            ->where('status', 'CONFIRMED')
            ->where('source_type', '!=', 'Agent')
            ->where('tenant_id', $task->tenant_id)
            ->when($task->send_time, function ($q) use ($task, $date) {
                $col = $task->send_date_column == 'confirmed' ? 'updated_at' : $task->send_date_column;
                if ($task->send_date_column != 'confirmed') {
                    $q
                        ->when($task->time_unit == 'days', fn ($q) => $q->where($col, '<=', $date->format('Y-m-d')))
                        ->when($task->time_unit != 'days', fn ($q) => $q->where($col, '<=', $date));
                }
            })
            ->whereDoesntHave('emails', function ($q) use ($task) {
                $q->where('type', $task->slug);
            })
            ->when($task->rooms_count, function ($q) use ($task) {
                $q->whereHas('rooms', fn ($sub) => $sub->whereIn('room_id', $task->rooms->pluck('room_id')));
            })
            ->when($task->addons_count, function ($q) use ($task) {
                $q->whereHas('rooms.addons', fn ($sub) => $sub->whereIn('extra_id', $task->addons->pluck('extra_id')));
            })
            ->whereHas('payment', fn ($q) => $task->send_date_column == 'confirmed' ? $q->where('status', 'COMPLETED') : $q->where('total', '>', 0));

        return $bookings;
    }

    public static function filterConditions(EmailTemplateCondition $condition, Booking $booking) : bool
    {
        if (!$condition || empty($condition->column)) {
            return true;
        }

        $value = $condition->value;

        $check = null;

        switch ($condition->column) {
            case 'payment_record':
                $check = $booking->payment_records_count;
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

    public static function transformBody($text, $data)
    {
        $domain = DB::table('domains')->select('domain')->where('tenant_id', $data->tenant_id)->first();

        $payment_link = tenant_route($domain->domain, 'tenant.payment.show', ['id' => $data->payment->link]);

        $patterns = [
            '{guest_name}' => $data->guest->details->full_name,
            '{paid_amount}' => $data->payment && $data->payment->records()->latest()->first() ? '&euro; '. $data->payment->records()->latest()->first()->amount : '',
            '{ref}' => $data->ref,
            '{camp}' => $data->location->name,
            '{check_in}' => $data->check_in->format('d.m.Y'),
            '{check_out}' => $data->check_out->format('d.m.Y'),
            '{deposit_amount}' => $data->deposit_amount,
            '{payment_link}' => '<a href="'. $payment_link .'" title="">'. $payment_link .'</a>',
            '{open_balance}' => $data->payment->open_balance,
            '{booking_link}' => '<a href="'. tenant_route($domain->domain, 'booknow.email-link-redirect', ['hashid' => Hashids::encode($data->id)]) .'" title="">'. tenant_route($domain->domain, 'booknow.email-link-redirect', ['hashid' => Hashids::encode($data->id)]) .'</a>',
            '{booking_url}' => tenant_route($domain->domain, 'tenant.bookings.show', ['ref' => $data->ref]),
        ];

        $replaced = $text;

        foreach ($patterns as $pattern => $substitute) {
            $replaced = Str::contains($text, $pattern) ? Str::replace($pattern, $substitute, $replaced) : $replaced;
        }

        return $replaced;
    }

    public function validateAndFormatEmail(string $emails): array
    {
        $emails = explode(',', $emails);

        $validEmails = [];

        foreach ($emails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }

        return $validEmails;
    }
}
