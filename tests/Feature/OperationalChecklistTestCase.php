<?php

namespace Tests\Feature;

use App\Models\ChecklistQuestion;
use App\Models\ChecklistSection;
use App\Models\ChecklistSessionWindow;
use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Tests\TestCase as BaseTestCase;

/**
 * Basis untuk test fitur Operational Checklist.
 *
 * Model Restaurant dipatok ke koneksi `mysql`, sedangkan test memakai SQLite.
 * Agar keduanya melihat data yang sama, koneksi `mysql` diarahkan ke file
 * SQLite yang sama dengan koneksi default, lalu schema dibuat ulang per test.
 */
abstract class OperationalChecklistTestCase extends BaseTestCase
{
    private array $originalMysqlConnection = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Gunakan SQLite in-memory khusus untuk test ini. Koneksi `mysql`
        // (dipakai model Restaurant) diarahkan ke koneksi in-memory yang sama.
        $this->originalMysqlConnection = config('database.connections.mysql');

        config([
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.mysql' => array_merge(
                config('database.connections.sqlite'),
                ['database' => ':memory:']
            ),
        ]);

        DB::purge('sqlite');
        DB::purge('mysql');

        // Buat schema pada koneksi `sqlite` (satu in-memory database untuk kedua nama koneksi).
        $this->artisan('migrate:fresh', ['--database' => 'sqlite']);

        // Pastikan koneksi `mysql` memakai PDO yang sama dengan koneksi `sqlite`.
        DB::connection('mysql')->setPdo(DB::connection('sqlite')->getPdo());
        DB::connection('mysql')->setReadPdo(DB::connection('sqlite')->getReadPdo());
    }

    protected function tearDown(): void
    {
        config(['database.connections.mysql' => $this->originalMysqlConnection]);

        DB::purge('sqlite');
        DB::purge('mysql');

        parent::tearDown();
    }

    protected function makeRestaurant(string $code, string $name): Restaurant
    {
        $restaurant = Restaurant::create([
            'code' => $code,
            'name' => $name,
        ]);

        $restaurant->forceFill(['checklist_token' => \Illuminate\Support\Str::random(32)])->save();

        return $restaurant->refresh();
    }

    protected function makeEmployee(Restaurant $restaurant, string $name, string $position = 'Waiter'): Employee
    {
        return Employee::create([
            'restaurant_id' => $restaurant->id,
            'name' => $name,
            'position' => $position,
        ]);
    }

    protected function makeSection(Restaurant $restaurant, string $name): ChecklistSection
    {
        return ChecklistSection::create([
            'restaurant_id' => $restaurant->id,
            'name' => $name,
            'sort_order' => 1,
        ]);
    }

    protected function makeQuestion(ChecklistSection $section, string $sessionType, array $attributes = []): ChecklistQuestion
    {
        return ChecklistQuestion::create(array_merge([
            'section_id' => $section->id,
            'session_type' => $sessionType,
            'check_point' => 'Checkpoint',
            'answer_type' => 'yes_no',
            'sort_order' => 1,
        ], $attributes));
    }

    protected function makeAllDayWindow(string $sessionType): ChecklistSessionWindow
    {
        return ChecklistSessionWindow::create([
            'restaurant_id' => null,
            'session_type' => $sessionType,
            'start_time' => '00:00:00',
            'end_time' => '23:59:00',
            'is_active' => true,
        ]);
    }
}
