<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Models\Booking\Booking;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\EmailTemplateCondition;
use App\Models\Booking\EmailTemplateDocument;
use App\Models\Booking\EmailTemplateExtra;
use App\Models\Booking\EmailTemplateRoom;
use App\Models\Booking\Extra;
use App\Models\Booking\Location;
use App\Models\Booking\Document;
use App\Models\Booking\EmailHistory;
use App\Services\Booking\AutomatedEmailService;
use App\Services\Booking\BookingService;
use App\Services\Booking\MailService;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    protected $disk;
    protected $bookingService;
    protected $operators;
    protected $condition_columns;

    public function __construct(BookingService $bookingService)
    {
        $this->disk = Storage::disk('resource');

        $this->bookingService = $bookingService;

        $this->condition_columns = [
            'bookings' => [
                'check_in' => 'Check In',
                'check_out' => 'Check Out',
                'booking_submitted' => 'Booking submitted',
            ],
            'payment' => [
                'open_balance' => 'Open balance',
                'payment_record' => 'Payment record'
            ]
        ];

        $this->operators = [
            'is' => 'is',
            'is_not' => 'is not',
            'is_empty' => 'is empty',
            'is_not_empty' => 'is not empty',
            'lt' => 'less than',
            'lte' => 'less than or equal to',
            'gt' => 'greater than',
            'gte' => 'greater than or equal to',
            'contains' => 'contains'
        ];
    }

    /**
     * EMAILS INDEX.
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function index()
    {
        $emails = EmailTemplate::all();

        return view('Booking.automated.index', compact('emails'));
    }

    /**
     * CREATE NEW AUTOMATED EMAIL
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function create()
    {
        $locations = Location::with(['rooms'])->get();
        $columns = AutomatedEmailService::getBookingColumns();
        $documents = Document::orderBy('sort', 'asc')->get();
        $addons = Extra::query()->where('active', 1)->orderBy('sort', 'asc')->get(['id', 'tenant_id', 'name', 'active']);

        return view('Booking.automated.new', compact('columns', 'locations', 'documents', 'addons'));
    }

    /**
     * INSERT NEW AUTOMATED EMAIL
     *
     * @param none
     *
     * @return Illuminate\Http\Redirect
     */
    public function insert()
    {
        $tenant = tenant()->id;

        DB::beginTransaction();

        $slug = Str::slug(request('name'), '-');

        $email = EmailTemplate::create(request()->only(['name', 'send_time', 'send_timing', 'time_unit', 'send_date_column', 'subject', 'foot_note']));

        $filename = 'automated_'. $email->id;
        $bladename = $filename .'.blade.php';

        $email->update([
            'slug' => $slug,
            'is_scheduled' => 0,
            'template' => $filename,
            'resource' => $bladename
        ]);

        @chmod(resource_path('views/Bookings/emails/templates/'. $tenant), 0755);

        $this->disk->put('templates/'. $tenant .'/'. $bladename, request('resource'));

        if (request()->has('documents') && count(request()->documents) > 0) {
            foreach (request()->documents as $document_id => $state) {
                $email->documents()->create([
                    'document_id' => $document_id,
                ]);
            }
        }

        if (request()->has('rooms') && count(request()->rooms) > 0) {
            foreach (request()->rooms as $room_id => $state) {
                $email->rooms()->create([
                    'room_id' => $room_id,
                ]);
            }
        }

        if (request()->has('addons') && count(request()->addons) > 0) {
            foreach (request()->addons as $addon_id => $state) {
                $email->addons()->create([
                    'extra_id' => $addon_id,
                ]);
            }
        }

        DB::commit();

        session()->flash('messages', 'Email added');

        return redirect('automated-emails/'.$email->id);
    }

    /**
     * SHOW EMAIL TEMPLATE.
     *
     * @param int $id
     *
     * @return Illuminate\Http\View
     */
    public function show($id)
    {
        $tenant = tenant()->id;

        $email = EmailTemplate::with(['rooms', 'addons', 'documents'])->where('id', $id)->where('tenant_id', tenant('id'))->firstOrfail();
        $template = $this->disk->get('templates/'. $tenant .'/'. $email->resource);
        $locations = Location::with(['rooms'])->get();
        $columns = AutomatedEmailService::getBookingColumns();
        $documents = Document::orderBy('sort', 'asc')->get();
        $addons = Extra::query()->where('active', 1)->orderBy('sort', 'asc')->get(['id', 'tenant_id', 'name', 'active']);

        return view('Booking.automated.show', [
            'id' => $id,
            'email' => $email,
            'template' => $template,
            'locations' => $locations,
            'columns' => $columns,
            'operators' => $this->operators,
            'condition_columns' => $this->condition_columns,
            'documents' => $documents,
            'addons' => $addons
        ]);
    }

    /**
     * UPDATE EXISTING ENTRY.
     *
     * @param int    $id
     * @param object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        $email = EmailTemplate::with(['rooms', 'condition'])->where('id', $id);

        $email->update($request->only(['name', 'slug', 'send_time', 'send_timing', 'time_unit', 'send_date_column', 'subject', 'foot_note']));

        $email = $email->first();

        $email->update([
            'is_scheduled' => request()->has('is_scheduled')
        ]);

        $old_rooms = json_decode(request('old_rooms'));
        $old_documents = json_decode(request('old_documents'));
        $old_addons = json_decode(request('old_addons'));
        $rooms = request('rooms');
        $documents = request('documents');
        $addons = request('addons');

        foreach ($old_rooms as $room) {
            if (!isset($rooms[$room->room_id])) {
                EmailTemplateRoom::find($room->id)->delete();
            }
        }

        foreach ($old_documents as $document) {
            if (!isset($documents[$document->document_id])) {
                EmailTemplateDocument::find($document->id)->delete();
            }
        }

        foreach ($old_addons as $addon) {
            if (!isset($addons[$addon->extra_id])) {
                EmailTemplateExtra::find($addon->id)->delete();
            }
        }

        if ($request->has('rooms') && count($request->rooms) > 0) {
            foreach ($request->rooms as $room_id => $state) {
                EmailTemplateRoom::firstOrCreate([
                    'email_template_id' => $email->id,
                    'room_id' => $room_id,
                ]);
            }
        }

        if ($request->has('documents') && count($request->documents) > 0) {
            foreach ($request->documents as $document_id => $state) {
                EmailTemplateDocument::firstOrCreate([
                    'email_template_id' => $email->id,
                    'document_id' => $document_id,
                ]);
            }
        }

        if ($request->has('addons') && count($request->addons) > 0) {
            foreach ($request->addons as $addon_id => $state) {
                EmailTemplateExtra::firstOrCreate([
                    'email_template_id' => $email->id,
                    'extra_id' => $addon_id,
                ]);
            }
        }

        if (!empty(request('condition_column')) && !empty(request('condition_operator'))) {
            EmailTemplateCondition::updateOrCreate([
                'email_template_id' => $email->id,
            ], [
                'column' => request('condition_column'),
                'operator' => request('condition_operator'),
                'value' => request('condition_value'),
            ]);
        }

        DB::commit();

        $this->disk->put('templates/'. tenant('id') .'/'. $email->resource, request('resource'));

        session()->flash('messages', 'Email updated');

        return redirect('automated-emails/'.$id);
    }

    public function delete($id)
    {
        $email = EmailTemplate::find($id);

        $email->rooms()->delete();
        $email->documents()->delete();

        $email->delete();

        session()->flash('messages', 'Template deleted');

        return redirect('automated-emails');
    }

    /**
     * RUN AUTOMATED EMAIL TASKS
     *
     * @return Illuminate\Http\Response
     */
    public function automated()
    {
        $emails = EmailTemplate::with(['rooms'])->where('custom', 1)->get();

        $key = '588wTD9a3';
        if (!request()->has('key') || '588wTD9a3' != request('key')) {
            return response('.');
        }

        foreach ($emails as $email) {
            $bookings = $this->getBookings($email);

            if ($bookings->count() > 0) {
                $this->sendCustomAutomatedEmails($bookings, $email);
            }
        }

        return 'OK';
    }

    public function previewRecipient(int $id) : Response
    {
        $task = EmailTemplate::query()
            ->with(['rooms', 'documents', 'addons', 'condition'])
            ->withCount(['documents', 'addons', 'rooms'])
            ->find($id);

        $date = MailService::getModifiedDate($task);

        $bookings = MailService::getFilteredBookings($task, $date)
            ->get()
            ->when($task->condition, function ($bookings) use ($task) {
                return $bookings->filter(function ($booking) use ($task) {
                    return MailService::filterConditions($task->condition, $booking);
                });
            });

        return response()->view('Booking.automated.preview-recipient', compact('task', 'bookings', 'id'));
    }

    public function excludeBooking(int $id, int $booking_id) : RedirectResponse
    {
        $task = EmailTemplate::find($id);

        EmailHistory::create([
            'booking_id' => $booking_id,
            'type' => $task->slug,
            'notes' => 'Excluded from automated email',
        ]);

        return redirect()->back();
    }
}
