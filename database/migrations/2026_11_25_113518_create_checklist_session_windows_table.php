<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklist_session_windows', function (Blueprint $table) {
            $table->id();

            // NULL = default global, dipakai outlet yang belum punya override.
            $table->foreignId('restaurant_id')->nullable()->constrained()->cascadeOnDelete();

            $table->enum('session_type', ['breakfast', 'lunch', 'dinner']);
            $table->time('start_time');
            $table->time('end_time'); // end_time < start_time berarti lintas tengah malam.
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['restaurant_id', 'session_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_session_windows');
    }
};
