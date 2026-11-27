<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Models\ChecklistSubmission;
use App\Models\Restaurant;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OperationalChecklistReviewController extends Controller
{
    /**
     * Daftar submission checklist untuk diverifikasi manager.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('Super Admin');

        $restaurants = $isSuperAdmin
            ? Restaurant::orderBy('name')->get(['id', 'code', 'name'])
            : $user->restaurants()->orderBy('restaurants.name')->get(['restaurants.id', 'restaurants.code', 'restaurants.name']);

        $date = $request->filled('date') && strtotime((string) $request->input('date'))
            ? $request->date('date')->toDateString()
            : now()->toDateString();

        $restaurantId = $request->filled('restaurant_id') && $request->integer('restaurant_id') > 0
            ? $request->integer('restaurant_id')
            : null;

        $status = in_array($request->input('status'), ['pending', 'verified', 'rejected'], true)
            ? $request->input('status')
            : null;

        $allowedIds = $isSuperAdmin ? null : $restaurants->pluck('id')->all();

        $submissions = ChecklistSubmission::query()
            ->with(['restaurant:id,code,name', 'section:id,name', 'employee:id,name,position'])
            ->withCount([
                'answers as not_ok_count' => fn (Builder $query) => $query->where('result', 'not_ok'),
                'answers as answers_count',
            ])
            ->when($date, fn (Builder $query) => $query->whereDate('date', $date))
            ->when($restaurantId, fn (Builder $query) => $query->where('restaurant_id', $restaurantId))
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->when($allowedIds !== null, fn (Builder $query) => $query->whereIn('restaurant_id', $allowedIds))
            ->orderBy('date', 'desc')
            ->orderBy('restaurant_id')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('operational-checklists.index', [
            'submissions' => $submissions,
            'restaurants' => $restaurants,
            'date' => $date,
            'restaurantId' => $restaurantId,
            'status' => $status,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Detail checklist + snapshot jawaban untuk verifikasi.
     */
    public function show(ChecklistSubmission $submission): View|RedirectResponse
    {
        if (! $this->canReview($submission)) {
            return redirect()
                ->route('operational-checklists.index')
                ->with('error', 'Anda tidak memiliki akses ke checklist outlet ini.');
        }

        $submission->load([
            'restaurant:id,code,name',
            'section:id,name',
            'employee:id,name,position',
            'verifier:id,name',
            'answers' => fn (Builder $query) => $query->orderBy('id'),
        ]);

        return view('operational-checklists.show', [
            'submission' => $submission,
            'answers' => $submission->answers,
        ]);
    }

    /**
     * Setujui checklist (hanya yang pending).
     */
    public function verify(ChecklistSubmission $submission): RedirectResponse
    {
        if (! $this->canReview($submission)) {
            return redirect()
                ->route('operational-checklists.index')
                ->with('error', 'Anda tidak memiliki akses untuk memverifikasi checklist outlet ini.');
        }

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Hanya checklist berstatus Menunggu Verifikasi yang bisa disetujui.');
        }

        $submission->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Checklist berhasil diverifikasi.');
    }

    /**
     * Tolak checklist: staff memperbaiki submission yang sama (kembali pending).
     */
    public function reject(Request $request, ChecklistSubmission $submission): RedirectResponse
    {
        if (! $this->canReview($submission)) {
            return redirect()
                ->route('operational-checklists.index')
                ->with('error', 'Anda tidak memiliki akses untuk menolak checklist outlet ini.');
        }

        $validated = $request->validate(
            ['verification_note' => ['required', 'string', 'min:5', 'max:1000']],
            [
                'verification_note.required' => 'Catatan penolakan wajib diisi.',
                'verification_note.min' => 'Catatan penolakan minimal 5 karakter.',
            ]
        );

        if ($submission->status !== 'pending') {
            return back()->with('error', 'Hanya checklist berstatus Menunggu Verifikasi yang bisa ditolak.');
        }

        $submission->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_note' => $validated['verification_note'],
        ]);

        return back()->with('success', 'Checklist ditolak. Staff dapat memperbaiki dan mengirim ulang.');
    }

    /**
     * Super Admin boleh semua outlet; Restaurant Manager hanya outlet di pivot.
     */
    private function canReview(ChecklistSubmission $submission): bool
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if (! $user->hasRole('Restaurant Manager')) {
            return false;
        }

        return $user->restaurants()->where('restaurants.id', $submission->restaurant_id)->exists();
    }
}
