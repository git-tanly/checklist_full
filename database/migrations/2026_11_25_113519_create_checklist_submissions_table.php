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
        Schema::create('checklist_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('checklist_sections')->cascadeOnDelete();

            // Riwayat tetap aman bila data employee dihapus.
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->enum('session_type', ['breakfast', 'lunch', 'dinner']);
            $table->date('date'); // Tanggal bisnis (tanggal mulai window sesi).

            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            // Satu pengisian per section + sesi + tanggal bisnis.
            $table->unique(['restaurant_id', 'section_id', 'date', 'session_type'], 'checklist_submissions_unique');
            $table->index(['restaurant_id', 'date', 'status'], 'checklist_submissions_review_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_submissions');
    }
};
