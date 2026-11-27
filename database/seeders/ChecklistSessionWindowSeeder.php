<?php

namespace Database\Seeders;

use App\Models\ChecklistSessionWindow;
use Illuminate\Database\Seeder;

class ChecklistSessionWindowSeeder extends Seeder
{
    /**
     * Seed jam sesi default global (dipakai outlet yang belum punya override).
     */
    public function run(): void
    {
        $windows = [
            ['session_type' => 'breakfast', 'start_time' => '06:00:00', 'end_time' => '11:00:00'],
            ['session_type' => 'lunch', 'start_time' => '11:00:00', 'end_time' => '16:00:00'],
            ['session_type' => 'dinner', 'start_time' => '16:00:00', 'end_time' => '23:00:00'],
        ];

        foreach ($windows as $window) {
            ChecklistSessionWindow::updateOrCreate(
                [
                    'restaurant_id' => null,
                    'session_type' => $window['session_type'],
                ],
                [
                    'start_time' => $window['start_time'],
                    'end_time' => $window['end_time'],
                    'is_active' => true,
                ]
            );
        }
    }
}
