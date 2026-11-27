<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChecklistQuestionRequest;
use App\Models\ChecklistQuestion;
use App\Models\ChecklistSection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistQuestionController extends Controller
{
    /**
     * Form kelola pertanyaan untuk satu section.
     * Menampilkan daftar pertanyaan existing yang dikelompokkan per sesi.
     */
    public function create(Request $request): View
    {
        $section = $this->resolveSection($request);

        $questions = $section->questions()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('session_type');

        return view('operational.questions.create', [
            'section' => $section,
            'questions' => $questions,
        ]);
    }

    /**
     * Simpan pertanyaan baru, lalu kembali ke halaman yang sama.
     */
    public function store(StoreChecklistQuestionRequest $request, ChecklistSection $section): RedirectResponse
    {
        ChecklistQuestion::create(array_merge(
            ['section_id' => $section->id],
            $request->validated()
        ));

        return redirect()
            ->route('operational.questions.create', ['section' => $section->id])
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Form edit pertanyaan.
     */
    public function edit(ChecklistQuestion $question): View
    {
        $question->load('section.restaurant:id,code,name');

        return view('operational.questions.edit', [
            'question' => $question,
            'section' => $question->section,
        ]);
    }

    /**
     * Update pertanyaan.
     */
    public function update(StoreChecklistQuestionRequest $request, ChecklistQuestion $question): RedirectResponse
    {
        $question->update($request->validated());

        return redirect()
            ->route('operational.questions.create', ['section' => $question->section_id])
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Hapus pertanyaan (jawaban lama tetap tersimpan sebagai snapshot).
     */
    public function destroy(ChecklistQuestion $question): RedirectResponse
    {
        $sectionId = $question->section_id;
        $question->delete();

        return redirect()
            ->route('operational.questions.create', ['section' => $sectionId])
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Section wajib valid dan milik context; bila parameter route tidak ada,
     * ambil dari query string (?section=).
     */
    private function resolveSection(Request $request): ChecklistSection
    {
        $sectionId = $request->route('section') ?? $request->integer('section');

        $section = ChecklistSection::query()
            ->when($sectionId, fn (Builder $query) => $query->where('id', (int) $sectionId))
            ->with('restaurant:id,code,name')
            ->first();

        abort_unless($section !== null, 404, 'Section tidak ditemukan.');

        return $section;
    }
}
