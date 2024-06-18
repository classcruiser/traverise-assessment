<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\InsertQuestionnaireRequest;
use App\Http\Requests\Booking\QuestionnaireRequest;
use App\Models\Booking\Booking;
use App\Models\Booking\Questionnaire;
use App\Models\Booking\QuestionnaireAnswer;
use App\Models\Booking\QuestionnaireType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class QuestionnaireController extends Controller
{
    /**
     * QUESTIONNAIRE INDEX
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function index()
    {
        $questionnaires = Questionnaire::with(['type'])->get();

        return view('Booking.questionnaire.index', compact('questionnaires'));
    }

    /**
     * NEW QUESTIONNAIRE
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function create()
    {
        $types = QuestionnaireType::all();

        return view('Booking.questionnaire.new', compact('types'));
    }

    /**
     * INSERT NEW QUESTIONNAIRE
     *
     * @param Object $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function insert(QuestionnaireRequest $request)
    {
        $validated = $request->validated();
        $answers = isset($validated['answers']) ? $validated['answers'] : null;
        unset($validated['answers']);
        $validated['active'] = isset($validated['active']);

        DB::beginTransaction();

        $questionnaire = Questionnaire::create($validated);

        if ($answers) {
            foreach ($answers as $answer) {
                $questionnaire->answers()->create([
                    'questionnaire_id' => $questionnaire->id,
                    'answer' => $answer,
                ]);
            }
        }

        DB::commit();

        return redirect()->route('tenant.questionnaire');
    }

    /**
     * SHOW ADDON
     *
     * @param Questionnaire $questionnaire
     *
     * @return Illuminate\Http\View
     */
    public function show(Questionnaire $questionnaire)
    {
        $questionnaire->load('answers', 'type');
        $types = QuestionnaireType::all();

        return view('Booking.questionnaire.show', compact('questionnaire', 'types'));
    }

    /**
     * UPDATE EXISTING QUESTIONNAIRE
     *
     * @param Questionnaire $questionnaire
     * @param QuestionnaireRequest $request
     *
     * @return Illuminate\Http\Redirect
     */
    public function update(Questionnaire $questionnaire, QuestionnaireRequest $request)
    {
        $validated = $request->validated();
        $answers = isset($validated['answers']) ? $validated['answers'] : null;
        unset($validated['answers']);
        $validated['active'] = isset($validated['active']);

        DB::beginTransaction();

        $questionnaire->update($validated);

        if ($answers) {
            QuestionnaireAnswer::where('questionnaire_id', $questionnaire->id)->delete();
            foreach ($answers as $answer) {
                $questionnaire->answers()->create([
                    'questionnaire_id' => $questionnaire->id,
                    'answer' => $answer,
                ]);
            }
        }

        DB::commit();

        session()->flash('messages', 'Questionnaire updated');

        return redirect()->route('tenant.questionnaire.show', $questionnaire->id);
    }

    /**
     * DELETE QUESTIONNAIRE
     *
     * @param Questionnaire $questionnaire
     *
     * @return Illuminate\Http\Redirect
     */
    public function remove(Questionnaire $questionnaire)
    {
        $questionnaire->delete();

        return redirect()->route('tenant.questionnaire');
    }

    public function oldBookingsQuestionnaire(string $ref) : Response | RedirectResponse
    {
        $booking = Booking::where('ref', $ref)
            ->with(['guests.details'])
            ->withCount(['guests'])
            ->firstOrFail();

        $check = DB::table('old_booking_questions')
            ->where('ref', $ref)
            ->count();

        if ($booking->guests_count == $check && !request()->has('success')) {
            return redirect()->route('questionnaire.old-bookings', ['ref' => $ref, 'success' => 'true']);
        }

        return response()->view('Booking.front.questionnaire', [
            'booking' => $booking,
            'ref' => $ref
        ]);
    }

    public function processOldBookingsQuestionnaire(string $ref, Request $request) : RedirectResponse
    {
        $validated = $request->validate([
            'guest.*.surflevel' => 'required',
            'guest.*.nutrition' => 'required',
            'guest.*.medical_info' => '',
            'guest.*.arrival' => ''
        ], [
            'guest.*.surflevel.required' => 'Please select a surf level',
            'guest.*.nutrition.required' => 'Please select a nutrition option',
        ]);

        $guests = $validated['guest'];

        DB::beginTransaction();

        foreach ($guests as $booking_guest_id => $data) {
            $record = DB::table('old_booking_questions')
                ->updateOrInsert(
                    ['ref' => $ref, 'booking_guest_id' => $booking_guest_id],
                    [
                        'answer' => json_encode([
                            'surflevel' => $data['surflevel'],
                            'nutrition' => $data['nutrition'],
                            'medical_info' => $data['medical_info'],
                            'arrival' => $data['arrival'],
                        ]),
                        'created_at' => now()
                    ]
                );
        }

        DB::commit();

        return redirect()->route('questionnaire.old-bookings', ['ref' => $ref, 'success' => 'true']);
    }
}
