<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        DB::statement("ALTER TABLE daily_report_details MODIFY COLUMN session_type ENUM('breakfast', 'lunch', 'dinner')");
    }
};
