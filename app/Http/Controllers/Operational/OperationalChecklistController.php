<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChecklistSubmissionRequest;
use App\Models\ChecklistAnswer;
use App\Models\ChecklistQuestion;
use App\Models\ChecklistSection;
use App\Models\ChecklistSubmission;
use App\Models\Employee;
use App\Models\Restaurant;
use App\Services\Operational\ChecklistAnswerEvaluator;
use App\Services\Operational\ChecklistSessionResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OperationalChecklistController extends Controller
{
    public function __construct(
        private readonly ChecklistSessionResolver $sessionResolver,
        private readonly ChecklistAnswerEvaluator $evaluator,
    ) {}

    /**
     * Halaman awal setelah scan QR: daftar section untuk sesi yang berjalan.
     */
    public function entry(Restaurant $restaurant): View
    {
        $session = $this->sessionResolver->activeSession($restaurant);
        $windows = $this->sessionResolver->windowsFor($restaurant);

        $sections = collect();
        $submissions = collect();

        if ($session !== null) {
            $sections = ChecklistSection::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('is_active', true)
                ->withCount([
                    'questions' => fn (Builder $query) => $query
                        ->where('session_type', $session['session_type'])
                        ->where('is_active', true),
                ])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->filter(fn (ChecklistSection $section) => $section->questions_count > 0)
                ->values();

            $submissions = ChecklistSubmission::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('session_type', $session['session_type'])
                ->where('date', $session['business_date'])
                ->get()
                ->keyBy('section_id');
        }

        return view('operational.entry', [
            'restaurant' => $restaurant,
            'session' => $session,
            'windows' => $windows,
            'sections' => $sections,
            'submissions' => $submissions,
        ]);
    }

    /**
     * Form checklist untuk satu section pada sesi yang sedang berjalan.
     */
    public function section(Restaurant $restaurant, ChecklistSection $section): View|RedirectResponse
    {
        $this->ensureSectionBelongsToRestaurant($section, $restaurant);

        $session = $this->sessionResolver->activeSession($restaurant);

        if ($session === null) {
            abort(403, 'Checklist sedang di luar jam sesi. Silakan coba lagi pada jam sesi berikutnya.');
        }

        $questions = $this->questionsFor($section, $session['session_type']);

        if ($questions->isEmpty()) {
            return redirect()
                ->route('operational.entry', $restaurant->checklist_token)
                ->with('error', 'Belum ada pertanyaan checklist untuk section ini pada sesi berjalan.');
        }

        $submission = $this->findSubmission($restaurant, $section, $session);

        if ($submission !== null && $submission->status !== 'rejected') {
            return redirect()
                ->route('operational.entry', $restaurant->checklist_token)
                ->with('error', 'Checklist section ini sudah diisi untuk sesi berjalan.');
        }

        $employees = Employee::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('name')
            ->get(['id', 'name', 'position']);

        $oldAnswers = $submission?->answers->keyBy('question_id') ?? collect();

        return view('operational.section', [
            'restaurant' => $restaurant,
            'section' => $section,
            'session' => $session,
            'questions' => $questions,
            'employees' => $employees,
            'submission' => $submission,
            'oldAnswers' => $oldAnswers,
        ]);
    }

    /**
     * Simpan jawaban checklist dari halaman publik.
     */
    public function submit(
        StoreChecklistSubmissionRequest $request,
        Restaurant $restaurant,
        ChecklistSection $section
    ): RedirectResponse {
        $this->ensureSectionBelongsToRestaurant($section, $restaurant);

        // Sesi ditentukan ulang di server, bukan dari input form.
        $session = $this->sessionResolver->activeSession($restaurant);

        if ($session === null) {
            return back()
                ->withInput()
                ->with('error', 'Checklist sedang di luar jam sesi. Jawaban tidak disimpan.');
        }

        $questions = $this->questionsFor($section, $session['session_type']);

        if ($questions->isEmpty()) {
            return back()->with('error', 'Tidak ada pertanyaan aktif untuk section dan sesi ini.');
        }

        $existing = $this->findSubmission($restaurant, $section, $session);

        if ($existing !== null && $existing->status !== 'rejected') {
            return back()->with('error', 'Checklist section ini sudah diisi untuk sesi berjalan.');
        }

        $answers = collect($request->input('answers', []))->keyBy('question_id');
        $validationError = $this->validateAnswers($answers, $questions);

        if ($validationError !== null) {
            return back()->withInput()->with('error', $validationError);
        }

        // Seluruh operasi database checklist memakai koneksi default (`sqlite` pada
        // test, `mysql` di runtime) agar konsisten dengan model Restaurant yang
        // dipatok ke `mysql`.
        try {
            $summary = DB::transaction(function () use (
                $request,
                $restaurant,
                $section,
                $session,
                $questions,
                $answers
            ) {
                $submission = ChecklistSubmission::query()->updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'section_id' => $section->id,
                        'session_type' => $session['session_type'],
                    ],
                    [
                        'employee_id' => $request->integer('employee_id'),
                        'date' => $session['business_date'],
                        'status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                        'verification_note' => null,
                        'submitted_at' => now(),
                    ]
                );

                ChecklistAnswer::where('submission_id', $submission->id)->delete();

                $okCount = 0;
                $notOkCount = 0;

                foreach ($questions as $question) {
                    $input = $answers->get($question->id, []);
                    $value = $input['value'] ?? null;
                    $remarks = trim((string) ($input['remarks'] ?? ''));

                    $result = $this->evaluator->evaluate($question, $value);

                    $isNumber = $question->answer_type === 'number';

                    ChecklistAnswer::create([
                        'submission_id' => $submission->id,
                        'question_id' => $question->id,
                        'answer_number' => $isNumber ? $value : null,
                        'answer_boolean' => $isNumber ? null : $value === 'yes',
                        'remarks' => $remarks === '' ? null : $remarks,
                        'result' => $result,
                        'check_point' => $question->check_point,
                        'key_check_point' => $question->key_check_point,
                        'answer_type' => $question->answer_type,
                        'unit' => $question->unit,
                        'std_min' => $question->std_min,
                        'std_max' => $question->std_max,
                        'temp_max' => $question->temp_max,
                    ]);

                    if ($result === ChecklistAnswerEvaluator::RESULT_OK) {
                        $okCount++;
                    } else {
                        $notOkCount++;
                    }
                }

                return [
                    'ok' => $okCount,
                    'not_ok' => $notOkCount,
                    'section' => $section->name,
                    'session' => $session['session_type'],
                ];
            });
        } catch (QueryException $exception) {
            // Unique index (restaurant, section, date, session) menangkap submit ganda
            // yang lolos dari pengeccekan awal (race condition).
            $sqlState = $exception->errorInfo[0] ?? $exception->getCode();

            if ($sqlState === '23000') {
                return back()
                    ->withInput()
                    ->with('error', 'Checklist section ini sudah diisi. Muat ulang halaman untuk melihat statusnya.');
            }

            throw $exception;
        }

        return redirect()
            ->route('operational.submitted', $restaurant->checklist_token)
            ->with('checklist_summary', $summary);
    }

    /**
     * Ringkasan setelah submit (data diambil dari flash session).
     */
    public function submitted(Restaurant $restaurant): View|RedirectResponse
    {
        $summary = session('checklist_summary');

        if (! is_array($summary)) {
            return redirect()->route('operational.entry', $restaurant->checklist_token);
        }

        return view('operational.submitted', [
            'restaurant' => $restaurant,
            'summary' => $summary,
        ]);
    }

    private function ensureSectionBelongsToRestaurant(ChecklistSection $section, Restaurant $restaurant): void
    {
        abort_unless((int) $section->restaurant_id === (int) $restaurant->id, 404);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ChecklistQuestion>
     */
    private function questionsFor(ChecklistSection $section, string $sessionType)
    {
        return $section->questions()
            ->where('session_type', $sessionType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array{session_type: string, business_date: string}  $session
     */
    private function findSubmission(
        Restaurant $restaurant,
        ChecklistSection $section,
        array $session
    ): ?ChecklistSubmission {
        return ChecklistSubmission::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('section_id', $section->id)
            ->where('session_type', $session['session_type'])
            ->whereDate('date', $session['business_date'])
            ->first();
    }

    /**
     * Pastikan jawaban lengkap, tidak menyelundupkan pertanyaan lain, dan
     * tipe nilainya sesuai dengan tipe pertanyaan.
     *
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $answers
     * @param  \Illuminate\Database\Eloquent\Collection<int, ChecklistQuestion>  $questions
     */
    private function validateAnswers($answers, $questions): ?string
    {
        $questionIds = $questions->pluck('id')->all();
        $answeredIds = $answers->keys()->map(fn ($id) => (int) $id)->all();

        $unknown = array_diff($answeredIds, $questionIds);

        if ($unknown !== []) {
            return 'Ada pertanyaan yang tidak sesuai dengan section dan sesi ini.';
        }

        $missing = array_diff($questionIds, $answeredIds);

        if ($missing !== []) {
            return 'Semua pertanyaan wajib diisi sebelum submit.';
        }

        foreach ($questions as $question) {
            $input = $answers->get($question->id);
            $value = is_array($input) ? ($input['value'] ?? null) : null;

            if ($question->answer_type === 'number') {
                if ($value === null || $value === '' || ! is_numeric($value)) {
                    return 'Jawaban untuk "'.str($question->check_point)->limit(60).'" harus berupa angka.';
                }

                continue;
            }

            if (! in_array($value, ['yes', 'no'], true)) {
                return 'Jawaban untuk "'.str($question->check_point)->limit(60).'" harus Yes atau No.';
            }
        }

        return null;
    }
}
