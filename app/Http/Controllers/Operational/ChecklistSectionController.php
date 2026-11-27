<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChecklistSectionRequest;
use App\Models\ChecklistSection;
use App\Models\Restaurant;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChecklistSectionController extends Controller
{
    /**
     * Daftar section per outlet.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'code', 'name']);
        $restaurantId = $request->integer('restaurant_id') ?: null;

        $sections = ChecklistSection::query()
            ->when($restaurantId, fn (Builder $query) => $query->where('restaurant_id', $restaurantId))
            ->with('restaurant:id,code,name')
            ->withCount('questions')
            ->orderBy('restaurant_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('operational.sections.index', compact('sections', 'restaurants', 'restaurantId'));
    }

    /**
     * Form tambah section.
     */
    public function create(\Illuminate\Http\Request $request): View
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'code', 'name']);

        return view('operational.sections.create', [
            'restaurants' => $restaurants,
            'selectedRestaurantId' => $request->integer('restaurant_id') ?: null,
        ]);
    }

    /**
     * Simpan section baru.
     */
    public function store(StoreChecklistSectionRequest $request): RedirectResponse
    {
        ChecklistSection::create($request->validated());

        return redirect()
            ->route('operational.sections.index', ['restaurant_id' => $request->integer('restaurant_id')])
            ->with('success', 'Section berhasil ditambahkan.');
    }

    /**
     * Form edit section.
     */
    public function edit(ChecklistSection $section): View
    {
        $section->load('restaurant:id,code,name');
        $restaurants = Restaurant::orderBy('name')->get(['id', 'code', 'name']);

        return view('operational.sections.edit', compact('section', 'restaurants'));
    }

    /**
     * Update section.
     */
    public function update(StoreChecklistSectionRequest $request, ChecklistSection $section): RedirectResponse
    {
        $section->update($request->validated());

        return redirect()
            ->route('operational.sections.index', ['restaurant_id' => $section->restaurant_id])
            ->with('success', 'Section berhasil diperbarui.');
    }

    /**
     * Hapus section (pertanyaan ikut terhapus via FK cascade).
     */
    public function destroy(ChecklistSection $section): RedirectResponse
    {
        $section->delete();

        return redirect()
            ->route('operational.sections.index')
            ->with('success', 'Section dan seluruh pertanyaannya berhasil dihapus.');
    }
}
