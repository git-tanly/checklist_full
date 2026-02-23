<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAndSyncUsers extends Command
{
    protected $signature = 'app:import-sync-users';
    protected $description = 'Upload user Lokal ke Portal, lalu update ID Lokal agar sama dengan Portal (dengan pengecekan konflik)';

    public function handle()
    {
        $this->alert('PERINGATAN: Script ini akan mengubah ID User di database lokal!');
        $this->warn('Pastikan Anda sudah mem-BACKUP database `checklist_full` sebelum lanjut.');

        if (!$this->confirm('Apakah Anda sudah backup dan yakin ingin melanjutkan?')) {
            $this->info('Proses dibatalkan.');
            return;
        }

        $this->info('Memulai proses migrasi & sinkronisasi...');

        $localUsers = DB::connection('mysql')->table('users')->get();

        $bar = $this->output->createProgressBar(count($localUsers));
        $bar->start();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($localUsers as $lUser) {

                // ================================
                // A. CEK / BUAT USER DI PORTAL
                // ================================
                $portalUser = DB::connection('mysql_portal')
                    ->table('users')
                    ->where('email', $lUser->email)
                    ->first();

                if ($portalUser) {
                    $portalId = $portalUser->id;
                } else {
                    $portalId = DB::connection('mysql_portal')
                        ->table('users')
                        ->insertGetId([
                            'name' => $lUser->name,
                            'email' => $lUser->email,
                            'password' => $lUser->password,
                            'email_verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                            'access_checklist' => true,
                            'is_active' => true,
                        ]);
                }

                $oldId = $lUser->id;
                $newId = $portalId;

                // ================================
                // B. JIKA ID BERBEDA → PROSES
                // ================================
                if ($oldId != $newId) {

                    // ================================
                    // C. CEK KONFLIK ID DI LOKAL
                    // ================================
                    $conflict = DB::table('users')
                        ->where('id', $newId)
                        ->exists();

                    if ($conflict) {
                        $this->newLine();
                        $this->error("SKIP: {$lUser->email} (ID: {$oldId}) ingin pindah ke ID {$newId}, tapi ID tersebut sudah dipakai user lain.");
                        $bar->advance();
                        continue;
                    }

                    $this->newLine();
                    $this->info("Migrasi User: {$lUser->email} | ID Lama: {$oldId} -> ID Baru: {$newId}");

                    // ================================
                    // D. UPDATE SEMUA RELASI
                    // ================================

                    DB::table('daily_reports')
                        ->where('user_id', $oldId)
                        ->update(['user_id' => $newId]);

                    DB::table('daily_reports')
                        ->where('approved_by', $oldId)
                        ->update(['approved_by' => $newId]);

                    DB::table('restaurant_user')
                        ->where('user_id', $oldId)
                        ->update(['user_id' => $newId]);

                    DB::table('model_has_roles')
                        ->where('model_id', $oldId)
                        ->update(['model_id' => $newId]);

                    DB::table('model_has_permissions')
                        ->where('model_id', $oldId)
                        ->update(['model_id' => $newId]);

                    DB::table('sessions')
                        ->where('user_id', $oldId)
                        ->update(['user_id' => $newId]);

                    // ================================
                    // E. UPDATE PRIMARY KEY USERS
                    // ================================
                    DB::table('users')
                        ->where('id', $oldId)
                        ->update(['id' => $newId]);
                }

                $bar->advance();
            }

            $this->newLine();
            $this->info('Sukses! Semua ID user lokal kini sinkron dengan Portal.');
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('Terjadi Kesalahan: ' . $e->getMessage());
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $bar->finish();
    }
}
