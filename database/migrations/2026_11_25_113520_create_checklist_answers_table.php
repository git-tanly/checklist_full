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
        Schema::create('checklist_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('checklist_submissions')->cascadeOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('checklist_questions')->nullOnDelete();

            $table->decimal('answer_number', 10, 2)->nullable();
            $table->boolean('answer_boolean')->nullable();
            $table->text('remarks')->nullable();

            // Hasil evaluasi saat submit: ok / not_ok.
            $table->enum('result', ['ok', 'not_ok']);

            // Snapshot pertanyaan agar riwayat tidak berubah saat master diedit.
            $table->text('check_point');
            $table->text('key_check_point')->nullable();
            $table->string('answer_type', 16);
            $table->string('unit', 16)->nullable();
            $table->decimal('std_min', 10, 2)->nullable();
            $table->decimal('std_max', 10, 2)->nullable();
            $table->decimal('temp_max', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['submission_id', 'question_id'], 'checklist_answers_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_answers');
    }
};
