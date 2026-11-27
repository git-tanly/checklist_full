<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->char('checklist_token', 32)->nullable()->unique()->after('code');
        });

        // Backfill token untuk outlet yang sudah ada agar QR langsung bisa dipakai.
        $restaurantIds = DB::table('restaurants')->whereNull('checklist_token')->pluck('id');

        foreach ($restaurantIds as $restaurantId) {
            DB::table('restaurants')
                ->where('id', $restaurantId)
                ->update(['checklist_token' => Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropUnique(['checklist_token']);
            $table->dropColumn('checklist_token');
        });
    }
};
