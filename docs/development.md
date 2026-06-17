# Panduan Pengembangan

Bagian ini ringkas: cara setup environment dev, perintah harian, struktur view, dan beberapa konvensi yang ditemukan di kode.

## Setup Awal (sekali)

```bash
composer install
npm install
copy .env.example .env       # Windows (cmd)
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Aktifkan SSO (`SSO_*`, `DB_PORTAL_*`) di `.env` sesuai instruksi di `README.md`. Tanpa konfigurasi ini login tidak akan jalan.

## Perintah Harian

```bash
composer dev                  # serve + queue + pail + vite (concurrent)
php artisan serve             # PHP dev server
npm run dev                   # Vite dev server (HMR)
npm run build                 # Bundle untuk produksi
php artisan tinker            # REPL
php artisan migrate:fresh --seed   # reset DB lokal + reseed
```

## Lint & Test

```bash
./vendor/bin/pint             # auto-format PHP (Laravel Pint)
composer test                 # config:clear lalu php artisan test (PHPUnit 11)
```

> Belum ada test domain spesifik di folder `tests/Feature` — penambahan test direkomendasikan untuk `DailyReportController` (workflow approve/reject) dan `SloWebhookController` (HMAC).

## Struktur View Penting

```
resources/views/
  layouts/
    mantis.blade.php          # layout utama (Mantis admin theme)
    navigation.blade.php
  daily-reports/
    index.blade.php           # tabel laporan + filter status & outlet
    create.blade.php          # form 4 session (breakfast / lunch / dinner / supper)
    edit.blade.php            # sama dengan create + prefilled
    show.blade.php            # detail readonly
    pdf.blade.php             # template DomPDF
    partials/                 # komponen kecil (input session, dll)
  restaurants/
  upselling-items/
  revenue-targets/
  users/
  dashboard.blade.php
  analytics_modal.blade.php   # partial untuk modal "Outlet Analytics"
  profile/
```

Hampir semua view domain memakai `@extends('layouts.mantis')`. Form Daily Report menggunakan Alpine.js untuk komponen dinamis (tab session, repeater VIP remarks, dropdown upselling per outlet).

## Konvensi Kode yang Ditemukan

- Bahasa pesan flash & error: **Bahasa Indonesia**.
- Format angka rupiah di form: titik sebagai pemisah ribuan (`1.500.000`); selalu di-strip oleh `sanitizeSessionData` sebelum tersimpan.
- Pagination: `paginate(10)` untuk listing utama, `paginate(20)` untuk upselling items.
- Eager loading rutin: `with(['restaurant', 'user'])`, `with('details')`.
- Error handling pada `store/update` Daily Report dibungkus `DB::beginTransaction()` + `try/catch` + `rollBack()`.

## Menambah Migrasi & Seeder

```bash
php artisan make:migration add_<thing>_to_<table>_table
php artisan make:seeder <Name>Seeder
```

Setelah seeder dibuat, daftarkan ke `database/seeders/DatabaseSeeder.php` agar ikut dijalankan oleh `db:seed`.

## Menambah Role / Permission

Karena seluruh model `User` memakai trait `HasRoles` (Spatie), penambahan role cukup:

```php
\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Nama Role']);
$user->assignRole('Nama Role');
```

Ingat juga update Gate `access-checklist-app` di `App\Providers\AppServiceProvider::boot` jika role baru juga perlu mengakses dashboard.

## Menambah Endpoint Web

1. Buat controller / method baru.
2. Daftarkan di `routes/web.php` di dalam grup yang sesuai (`auth`, `can:access-checklist-app`, `role:...`).
3. Tambahkan view di `resources/views/<modul>/`.
4. Tambahkan link nav di `resources/views/layouts/navigation.blade.php` jika perlu.

## Menambah Endpoint API

Tambahkan ke `routes/api.php`. Pastikan endpoint API stateless dan tidak memerlukan session web. Bila butuh otentikasi server-to-server, gunakan HMAC pattern seperti `SloWebhookController` (jangan reinvent).

## Logging

`MAIL_MAILER=log` di env example artinya email default ditulis ke log Laravel. Untuk debugging integrasi SSO, manfaatkan `php artisan pail` (sudah terinclude di `composer dev`).
