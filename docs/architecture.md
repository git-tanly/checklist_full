# Arsitektur

Dokumen ini menjelaskan komponen aplikasi, hubungan antar koneksi database, dan integrasi dengan Portal SSO.

## Posisi dalam Ekosistem

Aplikasi `checklist_full` adalah **satellite app** dalam ekosistem Tanly. Otentikasi dan akun terpusat di Portal; aplikasi satelit fokus pada domain bisnisnya masing-masing — di sini, pelaporan operasional F&B harian.

```
                +-------------------------------------+
                |              Portal SSO             |
                |  - oauth/authorize, oauth/token     |
                |  - api/user                         |
                |  - tabel users (sumber identitas)   |
                |  - tabel applications               |
                +------------------+------------------+
                                   |
                  OAuth code flow  |  SLO webhook (HMAC)
                                   v
+------------------------------------------------------------------+
|                       Daily Report (Checklist)                   |
|                                                                  |
|  Web (auth + Spatie role)         API (no auth, HMAC verified)   |
|  - DailyReportController          - SloWebhookController         |
|  - DashboardController                                           |
|  - Restaurant/User/...Controller                                 |
|                                                                  |
|  Konektor DB:                                                    |
|  - mysql        (lokal aplikasi: laporan, role, target, dll)     |
|  - mysql_portal (Portal SSO: lookup users & applications)        |
+------------------------------------------------------------------+
```

## Dua Koneksi Database

Didefinisikan di `config/database.php`:

- `mysql` — koneksi default. Menyimpan tabel-tabel domain aplikasi:
  `users`, `restaurants`, `daily_reports`, `daily_report_details`, `upselling_items`, `revenue_targets`, `restaurant_user`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`, `sessions`, `cache`, `jobs`, dll.

- `mysql_portal` — koneksi Portal SSO. Diakses melalui:
  - `App\Models\Application` (membaca metadata aplikasi yang terdaftar di Portal).
  - Command `app:import-sync-users` (membaca `users` dari Portal lalu menyamakan ID di DB lokal).

Model `App\Models\Restaurant` dan `App\Models\LocalUser` secara eksplisit men-set `protected $connection = 'mysql'`. Hal ini memastikan relasi pivot `restaurant_user` dan `model_has_roles` selalu memakai DB lokal, bahkan ketika model `User` (default `mysql` juga) dimaksudkan sebagai cermin user Portal.

## Modul Auth (OAuth + JIT Provisioning)

```
Browser            App (Daily Report)               Portal SSO
   |                       |                              |
   |  GET /login           |                              |
   |---------------------->|                              |
   |                       |  redirect to /oauth/authorize|
   |                       |----------------------------->|
   |  user login & approve |                              |
   |  (di Portal)          |                              |
   |                       |  GET /auth/callback?code=... |
   |<----------------------|------------------------------|
   |  POST /api/oauth/token (server-to-server)            |
   |                       |----------------------------->|
   |                       |  access_token                |
   |                       |<-----------------------------|
   |                       |  GET /api/user (Bearer ...)  |
   |                       |----------------------------->|
   |                       |  user profile JSON           |
   |                       |<-----------------------------|
   |  Auth::login(localUser) (JIT create jika belum ada)  |
   |  redirect /dashboard  |                              |
   |<----------------------|                              |
```

Logika utama ada di `App\Http\Controllers\AuthController`:

- `redirect()` — generate `state` random (40 char) dan simpan di session, lalu redirect ke
  `${SSO_PORTAL_URL}/oauth/authorize?client_id=...&redirect_uri=...&response_type=code&scope=&state=...`.
- `callback()`
  1. Verifikasi `state` dari session vs request — bila tidak match, tampilkan error.
  2. POST `${SSO_PORTAL_URL}/api/oauth/token` (form) untuk menukar `code` dengan `access_token`.
  3. GET `${SSO_PORTAL_URL}/api/user` dengan Bearer token.
  4. JIT: jika user lokal belum ada → `User::create([...])`, `assignRole('Daily Worker')`, attach outlet `209 Dining`. Bila sudah ada → update `sso_id`, `name`, `password` (hash dari Portal disimpan untuk fallback).
  5. `Auth::login($user)` dan `redirect()->intended('/dashboard')`.
- `logout()` — `Auth::logout()`, invalidate session, regenerate token, lalu redirect ke `${SSO_PORTAL_URL}/logout`.

## Modul Single Logout (SLO)

Endpoint `POST /api/sso/slo` (file `app/Http/Api/SloWebhookController.php`) dipanggil oleh Portal saat user logout di mana saja.

Body request:

```json
{ "sso_id": "...", "signature": "..." }
```

Verifikasi: `hash_hmac('sha256', sso_id, env('SSO_WEBHOOK_SECRET'))` lalu `hash_equals($expected, $signature)` untuk mencegah timing attack. Jika cocok, ambil user lokal berdasarkan `sso_id` dan hapus seluruh row di `sessions` yang `user_id`-nya cocok. Bila user tidak ditemukan, log info (idempotent — return 200 OK).

## Otorisasi

Lapisan demi lapisan:

1. **Login session** — middleware `auth` (Laravel default).
2. **Gate global** — `can:access-checklist-app` di `AppServiceProvider`. Setiap user wajib memiliki minimal satu role yang terdaftar untuk masuk ke seluruh route grup.
3. **Role-based middleware** — `role:Super Admin` dan `role:Super Admin|Restaurant Manager` (Spatie). Diterapkan pada modul administrasi (users, restaurants, upselling-items) dan revenue targets.
4. **Global Scope di model `DailyReport`** — non-Super-Admin otomatis hanya melihat laporan untuk outlet di pivot `restaurant_user` (`whereIn('restaurant_id', $myRestaurantIds)`).
5. **Manual scope di controller** — di `DailyReportController::store/update`, controller mengecek bahwa `restaurant_id` yang dipost merupakan outlet milik user (kecuali Super Admin), sehingga tidak bisa membuat laporan untuk outlet orang lain.

## Sinkronisasi User dengan Portal

Command `app:import-sync-users` (di `app/Console/Commands/ImportAndSyncUsers.php`) dipakai sekali saat onboarding aplikasi atau saat menarik user batch baru dari Portal:

1. Iterasi seluruh user lokal.
2. Cari user di `mysql_portal.users` berdasarkan email; jika tidak ada, insert (set `access_checklist=true`, `is_active=true`).
3. Bila ID lokal ≠ ID portal:
   - Cek konflik (ID portal sudah dipakai user lokal lain) → skip.
   - Kalau aman, update FK di `daily_reports.user_id`, `daily_reports.approved_by`, `restaurant_user.user_id`, `model_has_roles.model_id`, `model_has_permissions.model_id`, `sessions.user_id`.
   - Update PK `users.id`.
4. `SET FOREIGN_KEY_CHECKS=0` di awal proses dan dikembalikan di `finally`.

> Tujuannya supaya user_id antar aplikasi satelit konsisten — memudahkan reporting silang antar aplikasi yang sama-sama bergantung ke Portal.

## Frontend & Layout

- Layout master `resources/views/layouts/mantis.blade.php` (theme **Mantis Admin**). Hampir semua view domain `@extends('layouts.mantis')`.
- Pagination memakai Bootstrap 5 (`Paginator::useBootstrapFive()` di `AppServiceProvider`) supaya konsisten dengan Mantis.
- ApexCharts di-load di view dashboard dan modal analytics (data dirakit di `DashboardController`).
- Asset di-bundle dengan Vite 7 (`resources/css/app.css`, `resources/js/app.js`).
