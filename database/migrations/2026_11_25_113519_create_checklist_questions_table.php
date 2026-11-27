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
        Schema::create('checklist_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('checklist_sections')->cascadeOnDelete();

            $table->enum('session_type', ['breakfast', 'lunch', 'dinner']);

            $table->text('check_point');
            $table->text('key_check_point')->nullable();
            $table->enum('answer_type', ['number', 'yes_no']);

            // Batas penilaian (tidak ditampilkan ke staff).
            $table->decimal('std_min', 10, 2)->nullable();
            $table->decimal('std_max', 10, 2)->nullable();
            $table->decimal('temp_max', 5, 2)->nullable();
            $table->string('unit', 16)->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['section_id', 'session_type', 'is_active', 'sort_order'], 'checklist_questions_session_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_questions');
    }
};
