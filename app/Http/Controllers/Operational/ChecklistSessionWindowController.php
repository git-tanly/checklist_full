<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateChecklistSessionWindowsRequest;
use App\Models\ChecklistSessionWindow;
use App\Models\Restaurant;
use App\Services\Operational\ChecklistSessionResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistSessionWindowController extends Controller
{
    public function __construct(private readonly ChecklistSessionResolver $resolver) {}

    /**
     * Halaman pengaturan jam sesi: global (default) atau override per outlet.
     */
    public function edit(Request $request): View
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'code', 'name']);
        $restaurantId = $request->filled('restaurant_id') && $request->integer('restaurant_id') > 0
            ? $request->integer('restaurant_id')
            : null;

        $restaurant = $restaurantId ? Restaurant::find($restaurantId) : null;

        // Baris yang ditampilkan: milik outlet bila ada, selain itu prefill global.
        $windows = ChecklistSessionWindow::query()
            ->when($restaurantId, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->get()
            ->keyBy('session_type');

        $globalWindows = ChecklistSessionWindow::query()->global()->get()->keyBy('session_type');

        $rows = collect(ChecklistSessionResolver::SESSIONS)
            ->map(fn (string $session) => $windows->get($session)
                ?? $globalWindows->get($session)
                ?? new ChecklistSessionWindow([
                    'restaurant_id' => $restaurantId,
                    'session_type' => $session,
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'is_active' => false,
                ]));

        // Ringkasan window efektif per outlet (hasil resolusi).
        $effectivePerOutlet = $restaurants
            ->mapWithKeys(function (Restaurant $item) {
                $windowsFor = $this->resolver->windowsFor($item);

                return [$item->id => collect(ChecklistSessionResolver::SESSIONS)
                    ->map(fn (string $session) => isset($windowsFor[$session])
                        ? substr($windowsFor[$session]->start_time, 0, 5).' - '.substr($windowsFor[$session]->end_time, 0, 5)
                        : '-')
                    ->all()];
            });

        return view('operational.session-windows.edit', [
            'restaurants' => $restaurants,
            'restaurant' => $restaurant,
            'restaurantId' => $restaurantId,
            'rows' => $rows,
            'hasOverride' => $restaurantId !== null && $windows->isNotEmpty(),
            'effectivePerOutlet' => $effectivePerOutlet,
        ]);
    }

    /**
     * Simpan jam sesi untuk scope terpilih (global atau satu outlet).
     */
    public function update(UpdateChecklistSessionWindowsRequest $request): RedirectResponse
    {
        $restaurantId = $request->integer('restaurant_id') ?: null;

        // Kembalikan outlet ke nilai global: hapus baris override-nya.
        if ($restaurantId !== null && $request->boolean('remove_override')) {
            ChecklistSessionWindow::query()
                ->forRestaurant($restaurantId)
                ->delete();

            return redirect()
                ->route('operational.session-windows.edit', ['restaurant_id' => $restaurantId])
                ->with('success', 'Override jam sesi outlet dihapus. Outlet kembali memakai jam global.');
        }

        foreach ($request->validated()['windows'] as $window) {
            ChecklistSessionWindow::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'session_type' => $window['session_type'],
                ],
                [
                    'start_time' => $window['start_time'],
                    'end_time' => $window['end_time'],
                    'is_active' => $window['is_active'],
                ]
            );
        }

        $scopeLabel = $restaurantId
            ? Restaurant::find($restaurantId)?->name
            : 'global (semua outlet)';

        return redirect()
            ->route('operational.session-windows.edit', $restaurantId ? ['restaurant_id' => $restaurantId] : [])
            ->with('success', 'Jam sesi untuk '.$scopeLabel.' berhasil disimpan.');
    }
}
