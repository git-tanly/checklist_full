<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\LocalUser;

class ImportAndSyncUsers extends Command
{
    /**
     * Nama perintah yang akan dijalankan nanti
     */
    protected $signature = 'app:import-sync-users';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Upload user Lokal ke Portal, lalu update ID Lokal agar SAMA PERSIS dengan Portal (Termasuk relasi Foreign Key)';

    public function handle()
    {
        $this->alert('PERINGATAN: Script ini akan mengubah ID User di database lokal!');
        $this->warn('Pastikan Anda sudah mem-BACKUP database `checklist_full` sebelum lanjut.');

        if (!$this->confirm('Apakah Anda sudah backup dan yakin ingin melanjutkan?')) {
            $this->info('Proses dibatalkan.');
            return;
        }

        $this->info('Memulai proses migrasi & sinkronisasi...');

        // 1. Ambil semua user lokal
        $localUsers = DB::connection('mysql')->table('users')->get();

        $bar = $this->output->createProgressBar(count($localUsers));
        $bar->start();

        // Matikan Foreign Key Check sementara agar kita bisa ubah ID
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($localUsers as $lUser) {
                // A. INSERT / GET USER DI PORTAL
                // Kita gunakan updateOrCreate berdasarkan email
                // Jadi kalau user sudah ada, ID-nya diambil. Kalau belum, dibuat baru.
                $portalId = DB::connection('mysql_portal')->table('users')->insertGetId([
                    'name' => $lUser->name,
                    'email' => $lUser->email,
                    'password' => $lUser->password, // Copy password lama
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'access_checklist' => true, // Beri akses default
                    'is_active' => true,
                ]);

                // Jika user sudah ada (insertGetId mungkin gagal karena unique email), ambil ID-nya
                if (!$portalId) {
                    $portalUser = DB::connection('mysql_portal')->table('users')->where('email', $lUser->email)->first();
                    $portalId = $portalUser->id;
                }

                // B. CEK APAKAH ID PERLU DIUBAH?
                $oldId = $lUser->id;
                $newId = $portalId;

                if ($oldId != $newId) {
                    $this->newLine();
                    $this->info(" Migrasi User: {$lUser->email} | ID Lama: {$oldId} -> ID Baru: {$newId}");

                    // C. UPDATE SEMUA TABEL RELASI DI CHECKLIST (PENTING!)
                    // Ganti semua referensi ID lama ke ID baru

                    // 1. Tabel Daily Reports (Pembuat Laporan)
                    DB::table('daily_reports')->where('user_id', $oldId)->update(['user_id' => $newId]);

                    // 2. Tabel Daily Reports (Approved By - Jika ada)
                    DB::table('daily_reports')->where('approved_by', $oldId)->update(['approved_by' => $newId]);

                    // 3. Tabel Restaurant User (Pivot)
                    DB::table('restaurant_user')->where('user_id', $oldId)->update(['user_id' => $newId]);

                    // 4. Tabel Spatie (Model Has Roles)
                    DB::table('model_has_roles')->where('model_id', $oldId)->update(['model_id' => $newId]);

                    // 5. Tabel Spatie (Model Has Permissions)
                    DB::table('model_has_permissions')->where('model_id', $oldId)->update(['model_id' => $newId]);

                    // 6. Tabel Sessions (Jika ada)
                    DB::table('sessions')->where('user_id', $oldId)->update(['user_id' => $newId]);

                    // D. TERAKHIR: UPDATE ID DI TABEL USERS UTAMA
                    DB::table('users')->where('id', $oldId)->update(['id' => $newId]);
                }

                $bar->advance();
            }

            $this->newLine();
            $this->info('Sukses! Semua ID user lokal kini sinkron dengan Portal.');
        } catch (\Exception $e) {
            $this->error('Terjadi Kesalahan: ' . $e->getMessage());
        } finally {
            // Nyalakan kembali Foreign Key Check (Wajib!)
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $bar->finish();
    }
}
