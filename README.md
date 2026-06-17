# Checklist Full — Daily Report (F&B Operations)

Aplikasi web internal untuk pelaporan operasional harian outlet F&B (Food & Beverage). Dibangun di atas Laravel 12 dan terintegrasi sebagai aplikasi satelit (satellite app) pada ekosistem Portal SSO Tanly.

> Nama paket Composer masih default `laravel/laravel` (skeleton). Identitas “produk” yang dipakai di antarmuka adalah **Daily Report / Checklist Full**.

---

## Daftar Isi

- [Ikhtisar](#ikhtisar)
- [Fitur Utama](#fitur-utama)
- [Stack Teknologi](#stack-teknologi)
- [Arsitektur Singkat](#arsitektur-singkat)
- [Prasyarat](#prasyarat)
- [Instalasi & Setup](#instalasi--setup)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Folder](#struktur-folder)
- [Peran (Role) & Hak Akses](#peran-role--hak-akses)
- [Daftar Route](#daftar-route)
- [Integrasi SSO Portal](#integrasi-sso-portal)
- [Perintah Artisan Kustom](#perintah-artisan-kustom)
- [Dokumentasi Lanjutan](#dokumentasi-lanjutan)
- [Issue & Catatan Teknis Saat Ini](#issue--catatan-teknis-saat-ini)
- [Lisensi](#lisensi)

---

## Ikhtisar

Aplikasi ini menggantikan checklist manual harian di setiap outlet menjadi laporan terstruktur per **session** (breakfast, lunch, dinner, supper). Setiap laporan menampung:

- Revenue (food, beverage, others, event)
- Cover / pax (struktur dinamis per outlet — disimpan sebagai JSON)
- Upselling sold (per item menu yang sudah didaftarkan)
- Competitor comparison (Shangri-La, JW Marriott, Sheraton, dll)
- Thematic, staff on duty, remarks, VIP remarks

Laporan mengikuti workflow **Draft → Submitted → Approved** dengan pemilahan akses berbasis role (Spatie Permission) dan global scope per restoran. Manajer dapat menyetujui, menolak, dan mengunduh laporan dalam format PDF (DomPDF). Dashboard menampilkan widget operasional (waiting approval, draft, today’s revenue), grafik tren 7 hari, perbandingan kompetitor, dan pencapaian target bulanan per outlet.

## Fitur Utama

| Modul | Ringkasan |
|---|---|
| **Daily Report** | CRUD laporan harian per outlet, workflow Draft/Submitted/Approved, generate PDF, validasi ketat saat submit. |
| **Restaurants (Outlets)** | Master 8 outlet (209 Dining, Xiang Fu Hai, Chamas, Nagano, Voda Bistro, Joe Milano, Brazilian Aussie BBQ, Banquet). |
| **Upselling Items** | Master menu upselling per outlet (food / beverage). Tampil otomatis di form laporan sesuai outlet yang dipilih. |
| **Revenue Targets** | Target omzet per bulan/tahun per outlet. Mendukung pengisian per bulan atau langsung Full Year (loop 1–12). |
| **Users & Roles** | Manajemen user lokal, sinkron dengan Portal SSO. Mapping user ↔ outlet melalui pivot `restaurant_user` (multi-outlet/cluster). |
| **Dashboard Analytics** | Widget MTD vs Target, chart 7 hari, breakdown performance per outlet, modal analytics matriks per session & per hari. |
| **SSO Login + JIT Provisioning** | Login OAuth ke Portal, otomatis membuat user lokal pada login pertama dengan role default `Daily Worker` dan outlet default `209 Dining`. |
| **Single Logout (SLO)** | Webhook `POST /api/sso/slo` ber-HMAC untuk memutus sesi user di seluruh aplikasi satelit. |

## Stack Teknologi

- **Backend:** PHP ≥ 8.2, Laravel 12.x
- **Authentication:** OAuth 2.0 Authorization Code (Portal SSO eksternal). Laravel Breeze terpasang sebagai dev dependency tetapi rute `routes/auth.php` tidak diaktifkan.
- **Authorization:** [Spatie Laravel Permission 6.x](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Database:** MySQL (dua koneksi — `mysql` lokal & `mysql_portal` ke Portal SSO). Default skeleton `sqlite` ada di `.env.example` namun model utama dipatok ke `mysql`.
- **PDF:** `barryvdh/laravel-dompdf` 3.x
- **Frontend:** Blade + tema admin **Mantis**, Tailwind CSS 3, Alpine.js 3, Vite 7, ApexCharts (di-load di view dashboard)
- **Tooling dev:** `laravel/pint`, `laravel/pail`, `laravel/sail`, `phpunit/phpunit` 11

## Arsitektur Singkat

```
+----------------+   OAuth (auth code)   +-----------------------+
|  Portal SSO    | <-------------------> |  Daily Report (this)  |
|  (mysql_portal)|   SLO webhook (HMAC)  |  (mysql lokal)        |
+----------------+ --------------------> +-----------------------+
                                          |  - users (lokal)
                                          |  - restaurants
                                          |  - daily_reports
                                          |  - daily_report_details (JSON)
                                          |  - upselling_items
                                          |  - revenue_targets
                                          |  - roles/permissions (Spatie)
```

Dua koneksi database aktif:
- `mysql` — schema aplikasi Checklist (laporan, master, role-permission Spatie).
- `mysql_portal` — read-only/lookup untuk akun & metadata aplikasi di Portal (`App\Models\Application`, sinkronisasi user via command `app:import-sync-users`).

Detail lengkap: lihat [`docs/architecture.md`](docs/architecture.md).

## Prasyarat

- PHP 8.2 atau lebih baru, dengan ekstensi `pdo_mysql`, `mbstring`, `intl`, `bcmath`, `gd` (untuk DomPDF), `openssl`.
- Composer 2.x
- Node.js 18+ dan NPM
- MySQL 8.x / MariaDB 10.6+ untuk database aplikasi
- Akses jaringan ke Portal SSO (variabel `SSO_PORTAL_URL`)
- Kredensial OAuth client yang sudah didaftarkan di Portal (Client ID, Secret, Redirect URI, Webhook Secret)

## Instalasi & Setup

```bash
# 1. Clone repo
git clone <repo-url> checklist_full
cd checklist_full

# 2. Install dependency PHP & JS
composer install
npm install

# 3. Salin env dan generate APP_KEY
copy .env.example .env        # Windows
# atau: cp .env.example .env  # Linux/Mac
php artisan key:generate

# 4. Buat database, lalu jalankan migrasi
php artisan migrate

# 5. Seed master data (restaurants, upselling items, roles, super admin)
php artisan db:seed

# 6. Build asset frontend (sekali untuk produksi)
npm run build
```

> Composer juga menyediakan shortcut `composer setup` untuk langkah 1, 3, 4, dan build asset secara berurutan.

## Konfigurasi Environment

Variabel di `.env` yang **wajib** disesuaikan untuk integrasi Portal (belum tersedia di `.env.example`, tambahkan secara manual):

```env
# Aplikasi
APP_NAME="Daily Report"
APP_URL=https://daily-report.example.com

# Database lokal aplikasi
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=checklist_full
DB_USERNAME=root
DB_PASSWORD=secret

# Database Portal SSO (read-only/lookup)
DB_PORTAL_HOST=127.0.0.1
DB_PORTAL_PORT=3306
DB_PORTAL_DATABASE=portal
DB_PORTAL_USERNAME=portal_user
DB_PORTAL_PASSWORD=secret

# OAuth Portal (Single Sign-On)
SSO_PORTAL_URL=https://portal.example.com
SSO_CLIENT_ID=your-client-id
SSO_CLIENT_SECRET=your-client-secret
SSO_REDIRECT_URI=https://daily-report.example.com/auth/callback

# Webhook Single Logout (HMAC SHA256 secret)
SSO_WEBHOOK_SECRET=shared-secret-with-portal
```

Variabel lain mengikuti default Laravel (`SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, dst).

> **Catatan keamanan:** `SSO_CLIENT_SECRET` dan `SSO_WEBHOOK_SECRET` jangan dimasukkan ke version control. Akses webhook menghitung HMAC `sha256(sso_id, SSO_WEBHOOK_SECRET)` dan dibandingkan dengan `hash_equals` agar aman dari timing attack.

## Menjalankan Aplikasi

Mode pengembangan (server PHP + Vite + queue + log tail):

```bash
composer dev
```

Perintah tersebut menjalankan `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, dan `npm run dev` secara concurrent. Untuk satu-satu:

```bash
php artisan serve         # http://127.0.0.1:8000
npm run dev               # Vite dev server untuk asset
```

Akses awal: buka `/` → akan diarahkan ke `/login` → redirect ke Portal SSO → kembali ke `/auth/callback` → masuk ke `/dashboard`.

## Struktur Folder

```
app/
  Console/Commands/ImportAndSyncUsers.php   # sinkron ID user ↔ Portal
  Http/
    Api/SloWebhookController.php             # webhook Single Logout
    Controllers/                             # controller utama (web)
      Auth/                                  # Breeze (tidak dipakai di runtime)
      AuthController.php                     # alur OAuth ke Portal
      DashboardController.php                # widget + analytics outlet
      DailyReportController.php              # inti laporan
      RestaurantController.php
      RevenueTargetController.php
      UpsellingItemController.php
      UserController.php
      ProfileController.php
    Middleware/CheckGlobalChecklistAccess.php  # (belum aktif, lihat catatan teknis)
  Models/                                    # User, Restaurant, DailyReport(+Detail),
                                             # UpsellingItem, RevenueTarget, Role,
                                             # Application, User, Session
  Providers/AppServiceProvider.php           # Gate access-checklist-app, paginator BS5

bootstrap/app.php                            # routing, middleware aliases (Spatie)
config/                                      # auth, database, permission, dll
database/
  migrations/                                # 15 migrasi (lihat docs/database.md)
  seeders/                                   # RestaurantSeeder, UpsellingItemSeeder,
                                             # RoleAndUserSeeder
resources/views/
  layouts/mantis.blade.php                   # layout master (theme Mantis)
  daily-reports/                             # index, create, edit, show, pdf
  restaurants/  upselling-items/  revenue-targets/  users/
  dashboard.blade.php  analytics_modal.blade.php
routes/
  web.php       # rute utama (auth + grup role)
  api.php       # webhook SSO
  console.php   # command custom
  auth.php      # Breeze (tidak di-require)
```

## Peran (Role) & Hak Akses

9 role didaftarkan otomatis oleh `RoleAndUserSeeder`:

| Role | Akses Inti |
|---|---|
| **Super Admin** | Seluruh menu (CRUD users, restaurants, upselling items, revenue targets), approve/reject/hapus laporan, melihat data semua outlet (lewat global scope). |
| **Restaurant Manager** | Buat/edit laporan untuk outletnya, **otomatis approve** saat submit, kelola revenue targets outletnya, hapus laporan, approve/reject laporan staf. |
| Assistant Restaurant Manager | Buat/edit laporan; submit → menunggu approval Manager. |
| F&B Supervisor | Sama dengan Asst. RM. |
| Waiter / Cashier / Bartender / Daily Worker / Trainee | Buat draft & submit laporan untuk outletnya. |

Semua route di-grup dengan Gate `access-checklist-app` (didefinisikan di `AppServiceProvider`) yang mensyaratkan minimal salah satu role di atas. Filter data per outlet ditegakkan melalui Global Scope `restaurant_scope` pada model `DailyReport` — non-Super-Admin hanya bisa membaca laporan untuk outlet yang ada di pivot `restaurant_user`.

Detail matriks akses route: lihat [`docs/roles-and-permissions.md`](docs/roles-and-permissions.md).

## Daftar Route

Ringkasan rute web (semua di belakang `auth` + `can:access-checklist-app`):

| Method | URI | Action | Akses |
|---|---|---|---|
| GET | `/dashboard` | `DashboardController@index` | Semua role |
| GET | `/dashboard/analytics/{restaurant}` | `DashboardController@getOutletAnalytics` | Super Admin / pemilik outlet |
| GET POST PUT DELETE | `/daily-reports[/...]` | `DailyReportController` | Semua role (operasi terbatas oleh status & role) |
| PATCH | `/daily-reports/{id}/approve` `/reject` | `DailyReportController` | Super Admin & Restaurant Manager |
| GET | `/daily-reports/{id}/pdf` | `DailyReportController@downloadPdf` | Status `approved` |
| GET POST PUT DELETE | `/upselling-items[/...]` | `UpsellingItemController` | **Super Admin** |
| GET POST PUT DELETE | `/restaurants[/...]` | `RestaurantController` | **Super Admin** |
| GET PUT DELETE | `/users[/...]` | `UserController` | **Super Admin** |
| GET POST | `/revenue-targets` | `RevenueTargetController` | Super Admin & Restaurant Manager |
| GET PATCH DELETE | `/profile` | `ProfileController` | User login |

Rute publik / khusus auth:

| Method | URI | Keterangan |
|---|---|---|
| GET | `/` | Redirect ke `/login` |
| GET | `/login` | Redirect ke Portal SSO untuk OAuth |
| GET | `/auth/redirect` | Sama seperti `/login` |
| GET | `/auth/callback` | Callback OAuth + JIT provisioning |
| POST | `/logout` | Logout lokal + redirect ke Portal logout |
| POST | `/api/sso/slo` | Webhook Single Logout (verifikasi HMAC) |

Detail tiap endpoint: [`docs/api.md`](docs/api.md).

## Integrasi SSO Portal

Aplikasi ini **tidak menyimpan password**; otentikasi sepenuhnya delegated ke Portal SSO. Alur ringkas:

1. User membuka `/login` → diarahkan ke `${SSO_PORTAL_URL}/oauth/authorize` dengan `state` random untuk proteksi CSRF.
2. Portal mengembalikan `code` ke `/auth/callback`.
3. Aplikasi menukar `code` dengan `access_token` di `${SSO_PORTAL_URL}/api/oauth/token`.
4. Aplikasi mengambil profil user dari `${SSO_PORTAL_URL}/api/user`.
5. **JIT Provisioning:** jika `email` belum ada di tabel `users` lokal, buat user baru dengan role default `Daily Worker` dan attach outlet default `209 Dining`. Jika sudah ada → update `sso_id`, `name`, `password`.
6. `Auth::login()` → redirect ke `/dashboard`.

**Single Logout** dikoordinasi via webhook `POST /api/sso/slo` dengan body `sso_id` + `signature`. Aplikasi memverifikasi `hash_hmac('sha256', sso_id, SSO_WEBHOOK_SECRET)` lalu `Session::where('user_id', $user->id)->delete()`.

Detail dan diagram urutan: [`docs/architecture.md`](docs/architecture.md).

## Perintah Artisan Kustom

```bash
php artisan app:import-sync-users
```

Menyamakan ID user lokal dengan ID user di Portal SSO. Untuk setiap user lokal:

1. Cari atau buat user di `mysql_portal.users` berdasarkan email.
2. Jika ID lokal berbeda dari ID portal dan tidak ada konflik, update FK di `daily_reports`, `restaurant_user`, `model_has_roles`, `model_has_permissions`, `sessions`, lalu perbarui PK `users.id`.
3. Disable `FOREIGN_KEY_CHECKS` selama proses dan re-enable di `finally`.

> **Backup database lokal sebelum menjalankan command ini.** Command meminta konfirmasi interaktif sebelum berjalan.

## Dokumentasi Lanjutan

Folder [`docs/`](docs/) berisi dokumentasi teknis tambahan:

- [`docs/architecture.md`](docs/architecture.md) — Arsitektur multi-database, integrasi Portal, flow SSO/SLO.
- [`docs/database.md`](docs/database.md) — Skema tabel, relasi, dan ringkasan setiap migrasi.
- [`docs/api.md`](docs/api.md) — Endpoint API & webhook (SLO).
- [`docs/roles-and-permissions.md`](docs/roles-and-permissions.md) — Matriks role × fitur, Gate, Global Scope.
- [`docs/daily-report-workflow.md`](docs/daily-report-workflow.md) — Workflow Draft → Submitted → Approved + skema field JSON.
- [`docs/development.md`](docs/development.md) — Panduan kontribusi, lint, test, build, struktur view.
- [`docs/troubleshooting.md`](docs/troubleshooting.md) — Known issues & solusinya.

## Issue & Catatan Teknis Saat Ini

Beberapa hal yang ditemukan dari telaah kode dan perlu diperhatikan saat onboarding atau lanjut develop. Daftar lengkap & rekomendasi perbaikan ada di [`docs/troubleshooting.md`](docs/troubleshooting.md).

- **`UserSeeder` & `RoleAndUserSeeder`** masih insert kolom `nik`. Migrasi `2026_02_05_161201_rename_nik_to_email_in_users_table` sudah me-rename `users.nik` → `users.email`, sehingga seeder akan **gagal** pada DB fresh sebelum kolom-kolom seeder diperbarui.
- **`App\Http\Middleware\CheckGlobalChecklistAccess`** belum di-alias di `bootstrap/app.php` dan menggunakan method (`hasAppAccess`) serta atribut (`is_active`) yang tidak ada di model `User` saat ini. Saat ini berstatus dead code.
- **Global scope di `App\Models\UpsellingItem`** mengacu kolom `restaurant_id` pada user yang sudah dihapus oleh migrasi pivot (`2025_11_20_222651`). Filter outlet untuk non-Super-Admin sebenarnya tidak pernah aktif via scope ini.
- **`App\Models\Restaurant`** memakai property `$tabel` (typo) alih-alih `$table`. Tidak fatal karena nama tabel mengikuti konvensi, tapi sebaiknya dirapikan.
- **`composer.json`** masih menggunakan nama dan deskripsi default skeleton (`laravel/laravel`).
- **`.env.example`** belum mencantumkan kunci `SSO_*` dan `DB_PORTAL_*`. Pengguna baru perlu menambahkan manual.
- **`routes/auth.php`** tidak di-`require` dari `routes/web.php`. Login Breeze (form lokal) tidak aktif — semua otentikasi via SSO.
- Middleware `can:is-super-admin` di route `users.create` mengacu Gate yang **tidak** didefinisikan di `AppServiceProvider`. Sangat mungkin typo dari `access-checklist-app`.

## Lisensi

Proyek mengikuti lisensi MIT (mengikuti skeleton Laravel). Sesuaikan jika ada kebijakan lisensi internal tim.
