<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('details', function (Blueprint $table) {
        //     //
        // });

        // MODIFY COLUMN hanya didukung MySQL. Driver lain (mis. SQLite untuk test)
        // sudah memakai enum yang sama dari migrasi create_daily_report_details_table.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE daily_report_details MODIFY COLUMN session_type ENUM('breakfast', 'lunch', 'dinner', 'supper')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('details', function (Blueprint $table) {
        //     //
        // });

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE daily_report_details MODIFY COLUMN session_type ENUM('breakfast', 'lunch', 'dinner')");
    }
};
