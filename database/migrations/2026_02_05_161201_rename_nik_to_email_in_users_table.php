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
        Schema::table('users', function (Blueprint $table) {
            // Rename kolom 'nik' menjadi 'email'
            $table->renameColumn('nik', 'email');

            // Jika kamu ingin menjadikan kolom email unik (umumnya email unik)
            $table->string('email')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika migrasi di-rollback, ubah kembali nama kolom 'email' menjadi 'nik'
            $table->renameColumn('email', 'nik');

            // Pastikan 'nik' bukan unik (sesuai dengan kebutuhan)
            $table->string('nik')->change();
        });
    }
};
