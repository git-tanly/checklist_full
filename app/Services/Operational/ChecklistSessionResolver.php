<?php

namespace App\Services\Operational;

use App\Models\ChecklistSessionWindow;
use App\Models\Restaurant;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class ChecklistSessionResolver
{
    /**
     * Urutan sesi operasional.
     */
    public const SESSIONS = ['breakfast', 'lunch', 'dinner'];

    /**
     * Window efektif per sesi untuk sebuah outlet.
     *
     * Window milik outlet menang; bila outlet belum punya baris untuk sesi
     * tersebut, dipakai window global (restaurant_id = NULL). Window dengan
     * is_active = false diabaikan.
     *
     * @return array<string, ChecklistSessionWindow>
     */
    public function windowsFor(Restaurant $restaurant): array
    {
        $outletWindows = ChecklistSessionWindow::query()
            ->forRestaurant($restaurant->id)
            ->get()
            ->keyBy('session_type');

        $globalWindows = ChecklistSessionWindow::query()
            ->global()
            ->get()
            ->keyBy('session_type');

        $windows = [];

        foreach (self::SESSIONS as $session) {
            $window = $outletWindows->get($session) ?? $globalWindows->get($session);

            if ($window === null || ! $window->is_active) {
                continue;
            }

            $windows[$session] = $window;
        }

        return $windows;
    }

    /**
     * Sesi yang sedang berjalan pada waktu tertentu.
     *
     * @return array{session_type: string, business_date: string}|null
     */
    public function activeSession(Restaurant $restaurant, ?CarbonInterface $now = null): ?array
    {
        $moment = $now === null
            ? Carbon::now(config('app.timezone'))
            : Carbon::instance($now);

        $today = $moment->copy()->startOfDay();

        foreach ($this->windowsFor($restaurant) as $session => $window) {
            $start = Carbon::parse($today->format('Y-m-d').' '.$window->start_time, $today->timezone);
            $end = Carbon::parse($today->format('Y-m-d').' '.$window->end_time, $today->timezone);

            // start == end bukan window yang valid (tidak ada durasi).
            if ($start->equalTo($end)) {
                continue;
            }

            // Window normal: [start, end) pada hari yang sama.
            if ($end->greaterThan($start)) {
                if ($moment->greaterThanOrEqualTo($start) && $moment->lessThan($end)) {
                    return [
                        'session_type' => $session,
                        'business_date' => $today->toDateString(),
                    ];
                }

                continue;
            }

            // Window lintas tengah malam, mis. dinner 18:00 - 01:00.
            // Bagian yang dimulai hari ini.
            if ($moment->greaterThanOrEqualTo($start)) {
                return [
                    'session_type' => $session,
                    'business_date' => $today->toDateString(),
                ];
            }

            // Bagian sisa dari window yang dimulai kemarin.
            if ($moment->lessThan($end)) {
                return [
                    'session_type' => $session,
                    'business_date' => $today->copy()->subDay()->toDateString(),
                ];
            }
        }

        return null;
    }
}
