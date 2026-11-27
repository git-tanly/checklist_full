# Active Plan: Fitur Operational Checklist (Checklist Harian Tanpa Login)

## Ringkasan

Modul baru untuk checklist operasional harian staff lapangan tanpa login. Alur: staff scan QR outlet → pilih section → pilih nama staff → isi pertanyaan sesuai sesi berjalan → status OK/NOT OK dihitung otomatis → Restaurant Manager memverifikasi.

Modul ini **terpisah** dari Daily Report (revenue) agar alur publik tidak menembus global scope `restaurant_scope` dan tidak mencampur status `draft/submitted/approved`.

## Keputusan Desain (Disepakati)

- **Pendekatan:** modul terpisah dengan tabel sendiri (bukan menumpang `daily_reports`, bukan form builder generik).
- **Sesi:** 3 sesi — `breakfast`, `lunch`, `dinner`.
- **Jam sesi:** bisa beda per outlet, dengan baris `restaurant_id = NULL` sebagai default global (fallback).
- **Duplikat:** satu submission per `(restaurant_id, section_id, date, session_type)`.
- **Reject:** staff memperbaiki submission yang sama (status `rejected` → `pending` setelah submit ulang), riwayat verifikasi tetap satu baris.
- **Master data:** hanya **Super Admin** (sections, questions, session windows).
- **Verifikasi:** Super Admin (semua outlet) + Restaurant Manager (outlet di pivot `restaurant_user`).
- **Status OK/NOT OK:** dihitung saat submit dan **disimpan sebagai snapshot** bersama salinan pertanyaan, agar perubahan master tidak mengubah riwayat.
- **QR:** dibuat client-side via library CDN di halaman cetak, tanpa package Composer baru.

## Last Checkpoint (Auto-Updated by AI)

- **Status Terakhir:** **SELESAI — seluruh 14 step `docs/plans/active.md` tuntas.** Modul Operational Checklist lengkap: migrasi + model, service evaluator/resolver, CRUD master (sections, questions, session windows), alur publik tanpa login via QR (`/o/{token}`), verifikasi manager, menu + badge, dokumentasi README, dan **24 test (98 assertions) hijau**. `php artisan test` penuh: 30 passed, 19 failed — 19 kegagalan seluruhnya test Breeze lama (`Auth/*`, `ProfileTest`, `ExampleTest`) karena `routes/auth.php` tidak di-require (login via SSO), bukan regresi fitur ini.
- **File Template Rujukan:** `resources/views/layouts/portal.blade.php` (layout admin, Bootstrap 5 + Tailwind CDN + SweetAlert2) dan `resources/views/operational/*` (layout publik `layouts/checklist`). Logika sesi: `app/Services/Operational/ChecklistSessionResolver.php`; evaluasi: `ChecklistAnswerEvaluator.php`.
- **Catatan Penting:** Known issue lama yang sengaja tidak diubah (di luar lingkup): `app/Http/Api/SloWebhookController.php` namespace tidak sesuai PSR-4 (membuat `route:list` gagal), `RoleAndUserSeeder`/`UserSeeder` masih memakai kolom `nik`, scope `UpsellingItem` memakai `users.restaurant_id` yang sudah di-drop. Test fitur checklist memakai `OperationalChecklistTestCase` (koneksi `mysql` diarahkan ke SQLite in-memory yang sama, `migrate:fresh` per test) — jangan hapus base class ini saat menambah test modul.
- **Next Step:** Tidak ada. Fitur siap diuji manual di browser (`php artisan serve`) dan di-commit. Saran commit: `feat: add operational checklist module`.

## Skema Database

### 1. `restaurants` — tambah kolom

| Kolom | Tipe | Catatan |
|---|---|---|
| `checklist_token` | `char(32)` unique nullable, setelah `code` | Backfill 32 karakter acak untuk outlet yang sudah ada |

### 2. `checklist_session_windows`

| Kolom | Tipe | Catatan |
|---|---|---|
| `id` | bigint PK | |
| `restaurant_id` | FK nullable, `cascadeOnDelete` | `NULL` = default global |
| `session_type` | enum `breakfast,lunch,dinner` | |
| `start_time` | time | |
| `end_time` | time | `end_time < start_time` = lintas tengah malam |
| `is_active` | boolean, default true | |
| `timestamps` | | |

Unik `(restaurant_id, session_type)` **divalidasi di Form Request** (MySQL menganggap `NULL` berbeda-beda, jadi unique index tidak cukup).

### 3. `checklist_sections`

| Kolom | Tipe |
|---|---|
| `restaurant_id` | FK `cascadeOnDelete` |
| `name` | string(120) |
| `sort_order` | unsigned smallint, default 0 |
| `is_active` | boolean, default true |
| `timestamps` | |

Index `(restaurant_id, is_active)`.

### 4. `checklist_questions`

| Kolom | Tipe | Catatan |
|---|---|---|
| `section_id` | FK `cascadeOnDelete` | |
| `session_type` | enum `breakfast,lunch,dinner` | |
| `check_point` | text | Tampil ke staff |
| `key_check_point` | text nullable | Tampil ke staff |
| `answer_type` | enum `number,yes_no` | |
| `std_min` | decimal(10,2) nullable | |
| `std_max` | decimal(10,2) nullable | |
| `temp_max` | decimal(5,2) nullable | |
| `unit` | string(16) nullable | mis. `°C`, `pcs` |
| `sort_order` | unsigned smallint, default 0 | |
| `is_active` | boolean, default true | |
| `timestamps` | | |

Index `(section_id, session_type, is_active, sort_order)`.

### 5. `checklist_submissions`

| Kolom | Tipe | Catatan |
|---|---|---|
| `restaurant_id` | FK `cascadeOnDelete` | |
| `section_id` | FK `cascadeOnDelete` | |
| `employee_id` | FK nullable, `nullOnDelete` | Riwayat aman bila employee dihapus |
| `session_type` | enum | |
| `date` | date | **Tanggal bisnis** (tanggal mulai window) |
| `status` | enum `pending,verified,rejected`, default `pending` | |
| `verified_by` | FK users nullable, `nullOnDelete` | |
| `verified_at` | timestamp nullable | |
| `verification_note` | text nullable | |
| `submitted_at` | timestamp | |
| `timestamps` | | |

Unique `(restaurant_id, section_id, date, session_type)` — aturan "hanya sekali".
Index `(restaurant_id, date, status)` untuk halaman verifikasi.

### 6. `checklist_answers`

| Kolom | Tipe | Catatan |
|---|---|---|
| `submission_id` | FK `cascadeOnDelete` | |
| `question_id` | FK `nullOnDelete` nullable | |
| `answer_number` | decimal(10,2) nullable | |
| `answer_boolean` | boolean nullable | |
| `remarks` | text nullable | Opsional |
| `result` | enum `ok,not_ok` | Hasil evaluasi saat submit |
| `check_point` | text | Snapshot |
| `key_check_point` | text nullable | Snapshot |
| `answer_type` | string(16) | Snapshot |
| `unit` | string(16) nullable | Snapshot |
| `std_min`, `std_max` | decimal(10,2) nullable | Snapshot |
| `temp_max` | decimal(5,2) nullable | Snapshot |
| `timestamps` | | |

Unique `(submission_id, question_id)`.

## Aturan Bisnis

### Resolusi sesi
- Window dianggap berlaku `[start_time, end_time)`.
- `end_time < start_time` → melewati tengah malam (mis. dinner 18:00–01:00).
- **Tanggal bisnis** = tanggal mulai window. Contoh: dinner 18:00–01:00 diisi pukul 00:30 → tanggal bisnis kemarin.
- Window outlet menang; bila tidak ada, pakai window global (`restaurant_id IS NULL`).
- `is_active = false` → window diabaikan.
- Di luar semua window → halaman menampilkan "di luar jam checklist" + jadwal hari ini; submit ditolak **di server**.

### Evaluasi jawaban
- `yes_no`: `yes` → `ok`, `no` → `not_ok`.
- `number`:
  1. `temp_max` terisi → `ok` bila `value <= temp_max`.
  2. selain itu, `std_min` dan/atau `std_max` terisi → `ok` bila di dalam rentang inklusif.
  3. keduanya kosong → `ok`.
- `std_min` tidak boleh > `std_max` (validasi Form Request).

### Duplikat & reject
- Section+sesi+tanggal yang sudah `pending`/`verified` → tidak bisa diisi lagi; entry page menampilkan chip status.
- Status `rejected` → staff boleh membuka dan submit ulang; jawaban lama diganti, status kembali `pending`, `verified_by`/`verified_at`/`verification_note` direset.

### Keamanan
- Halaman publik memakai `restaurant:checklist_token` (32 char acak) + `throttle:60,1`, tanpa auth, CSRF aktif.
- Employee harus milik outlet tersebut (validasi server, bukan hanya filter dropdown).
- Pertanyaan harus milik section yang dipilih dan `session_type` sesuai sesi berjalan.
- Threshold **tidak** dikirim ke halaman publik.

## Rencana Route

### Publik (grup `web` + `throttle:60,1`)

| Method | URI | Nama |
|---|---|---|
| GET | `/o/{restaurant:checklist_token}` | `operational.entry` |
| GET | `/o/{restaurant:checklist_token}/s/{section}` | `operational.section` |
| POST | `/o/{restaurant:checklist_token}/s/{section}` | `operational.submit` |

### Master data (`auth` + `can:access-checklist-app` + `role:Super Admin`)

| Method | URI | Nama |
|---|---|---|
| GET | `/operational/sections` | `operational.sections.index` |
| GET/POST | `/operational/sections/create`, `/operational/sections` | `operational.sections.create/store` |
| GET/PUT/DELETE | `/operational/sections/{section}` | `operational.sections.edit/update/destroy` |
| GET/POST | `/operational/sections/{section}/questions/create`, `.../questions` | `operational.questions.create/store` |
| GET/PUT/DELETE | `/operational/questions/{question}` | `operational.questions.edit/update/destroy` |
| GET/PUT | `/operational/session-windows` | `operational.session-windows.edit/update` |
| GET | `/restaurants/{restaurant}/barcode` | `restaurants.barcode` |

### Verifikasi (`auth` + `role:Super Admin|Restaurant Manager`)

| Method | URI | Nama |
|---|---|---|
| GET | `/operational-checklists` | `operational-checklists.index` |
| GET | `/operational-checklists/{submission}` | `operational-checklists.show` |
| PATCH | `/operational-checklists/{submission}/verify` | `operational-checklists.verify` |
| PATCH | `/operational-checklists/{submission}/reject` | `operational-checklists.reject` |

## File yang Akan Dibuat / Diubah

**Baru — Migration (6):** `add_checklist_token_to_restaurants_table`, `create_checklist_session_windows_table`, `create_checklist_sections_table`, `create_checklist_questions_table`, `create_checklist_submissions_table`, `create_checklist_answers_table`.

**Baru — Model (5):** `ChecklistSection`, `ChecklistQuestion`, `ChecklistSubmission`, `ChecklistAnswer`, `ChecklistSessionWindow`.

**Baru — Controller (5):** `Operational/OperationalChecklistController`, `Operational/OperationalChecklistReviewController`, `Operational/ChecklistSectionController`, `Operational/ChecklistQuestionController`, `Operational/ChecklistSessionWindowController`.

**Baru — Service (2):** `Operational/ChecklistSessionResolver`, `Operational/ChecklistAnswerEvaluator`.

**Baru — Form Request (4):** `StoreChecklistSubmissionRequest`, `StoreChecklistSectionRequest`, `StoreChecklistQuestionRequest`, `UpdateChecklistSessionWindowsRequest`.

**Baru — View (13):** `layouts/checklist`, `operational/entry`, `operational/section`, `operational/submitted`, `operational/sections/{index,create,edit}`, `operational/questions/{create,edit}`, `operational/session-windows/edit`, `operational-checklists/{index,show}`, `restaurants/barcode`.

**Baru — Seeder (1):** `ChecklistSessionWindowSeeder` (3 baris global).

**Baru — Test (4):** `ChecklistAnswerEvaluatorTest`, `ChecklistSessionResolverTest`, `OperationalChecklistFlowTest`, `OperationalChecklistReviewTest`.

**Diubah:** `routes/web.php`, `app/Models/Restaurant.php` (+`checklistSections`, `checklistSubmissions`), `app/Models/Employee.php` (+`checklistSubmissions`), `app/Models/User.php` (+`verifiedChecklists`), `app/Http/Controllers/RestaurantController.php` (+`barcode()`), `database/seeders/DatabaseSeeder.php`, `resources/views/layouts/mantis.blade.php`, `resources/views/layouts/portal.blade.php`, `resources/views/restaurants/index.blade.php` (tombol QR), `README.md`, `docs/session-log.md`.

---

## Langkah Kerja Atomik

### Step 1 — Migrasi database

**Tujuan:** semua tabel tersedia tanpa menyentuh data lama.

- [x] 1.1 Buat migration `add_checklist_token_to_restaurants_table`; `up()`: tambah `char('checklist_token', 32)->nullable()->unique()->after('code')`.
- [x] 1.2 Di migration yang sama, backfill token untuk outlet existing: `Restaurant::whereNull('checklist_token')->each(...)` pakai `Str::random(32)`; `down()`: `dropUnique` + `dropColumn`.
- [x] 1.3 Buat migration `create_checklist_session_windows_table` sesuai skema di atas (`restaurant_id` nullable + FK cascade).
- [x] 1.4 Buat migration `create_checklist_sections_table` (FK cascade, index `restaurant_id + is_active`).
- [x] 1.5 Buat migration `create_checklist_questions_table` (enum `session_type`, enum `answer_type`, kolom threshold, index gabungan).
- [x] 1.6 Buat migration `create_checklist_submissions_table` (unique `restaurant_id + section_id + date + session_type`, index `restaurant_id + date + status`).
- [x] 1.7 Buat migration `create_checklist_answers_table` (unique `submission_id + question_id`, kolom snapshot).
- [x] 1.8 Pastikan setiap `down()` bersih dan berurutan (drop anak dulu, lalu induk).
- [x] 1.9 Jalankan `php artisan migrate` (bukan `migrate:fresh`) dan cek tidak ada error FK.

**Kriteria selesai:** 6 tabel/kolom baru ada; outlet lama punya `checklist_token` unik 32 karakter; data existing utuh.

### Step 2 — Model & relasi

- [x] 2.1 `ChecklistSection`: `$fillable`, `casts` (`is_active` bool, `sort_order` int), relasi `restaurant()`, `questions()`, `submissions()`.
- [x] 2.2 `ChecklistQuestion`: `$fillable`, `casts` (`std_min`/`std_max` decimal:2, `temp_max` decimal:2, `is_active` bool), relasi `section()`, scope `forSession($type)`.
- [x] 2.3 `ChecklistSubmission`: `$fillable`, `casts` (`date` date, `verified_at` datetime, `submitted_at` datetime), relasi `restaurant()`, `section()`, `employee()`, `verifier()`, `answers()`, scope `status()`.
- [x] 2.4 `ChecklistAnswer`: `$fillable`, `casts` (`answer_boolean` bool, `remarks` string, `result` string, threshold decimal:2), relasi `submission()`, `question()`.
- [x] 2.5 `ChecklistSessionWindow`: `$fillable`, `casts` (`start_time`/`end_time` string `H:i:s`, `is_active` bool), scope `global()`, `forRestaurant($id)`.
- [x] 2.6 Tambah relasi di `Restaurant` (`checklistSections`, `checklistSubmissions`) dan `Employee` (`checklistSubmissions`).
- [x] 2.7 Tambah `User::verifiedChecklists()` (`hasMany(ChecklistSubmission::class, 'verified_by')`).

**Kriteria selesai:** semua relasi bisa dipanggil di `php artisan tinker` tanpa error.

### Step 3 — Service layer + unit test

- [x] 3.1 `ChecklistAnswerEvaluator::evaluate(ChecklistQuestion $q, mixed $value): string` — implementasi urutan: `yes_no` → `temp_max` → rentang `std_min`/`std_max` → default `ok`.
- [x] 3.2 `ChecklistSessionResolver::activeSession(Restaurant $r, CarbonInterface $now): ?array` — kembalikan `['session_type' => ..., 'business_date' => ...]` atau `null`.
- [x] 3.3 Implementasi resolusi window: ambil window outlet (`is_active`), fallback ke global, lalu cocokkan `[start, end)` termasuk kasus `end < start` (cek juga window hari sebelumnya untuk jam setelah tengah malam).
- [x] 3.4 Helper `windowsFor(Restaurant $r)` untuk menampilkan jadwal di halaman entry.
- [x] 3.5 Tulis `tests/Unit/ChecklistAnswerEvaluatorTest.php`: temp_max lolos/melebihi, hanya `std_min`, hanya `std_max`, rentang penuh, tanpa threshold, `yes`/`no`, nilai desimal.
- [x] 3.6 Tulis `tests/Unit/ChecklistSessionResolverTest.php`: dalam window, tepat di `start_time`, tepat di `end_time` (harus tidak masuk), lintas tengah malam (00:30 → tanggal bisnis kemarin), override outlet menang atas global, window nonaktif diabaikan, di luar semua window → `null`.
- [x] 3.7 Jalankan `php artisan test --filter=ChecklistAnswerEvaluator` dan `--filter=ChecklistSessionResolver`.

**Kriteria selesai:** kedua unit test hijau; evaluasi & resolusi sesi terbukti benar untuk kasus batas.

### Step 4 — Seeder jam sesi default

- [x] 4.1 Buat `ChecklistSessionWindowSeeder`: 3 baris `restaurant_id = NULL` (breakfast 06:00–11:00, lunch 11:00–16:00, dinner 16:00–23:00) memakai `updateOrCreate`.
- [x] 4.2 Daftarkan seeder di `DatabaseSeeder::run()` (setelah `RestaurantSeeder`).
- [x] 4.3 Jalankan `php artisan db:seed --class=ChecklistSessionWindowSeeder` dan pastikan 3 baris global ada.

**Kriteria selesai:** window global tersedia sehingga halaman publik bisa menentukan sesi tanpa konfigurasi tambahan.

### Step 5 — Form Request & Controller publik

- [x] 5.1 `StoreChecklistSubmissionRequest`: `employee_id` required + `exists:employees,id`; `answers` array required; `answers.*.question_id` required; `answers.*.value` nullable; `answers.*.remarks` nullable string max 500; pesan error Bahasa Indonesia.
- [x] 5.2 `OperationalChecklistController@entry(Restaurant $restaurant)`: resolve sesi aktif, muat section aktif + pertanyaan aktif sesi tersebut, muat submission hari itu per section, kirim ke view. Bila di luar jam → kirim `windows` + flag `closed`.
- [x] 5.3 `OperationalChecklistController@section(Restaurant, ChecklistSection)`: pastikan section milik restaurant (abort 404 bila tidak); resolve sesi (abort 403 + pesan bila di luar jam); muat employee outlet; muat pertanyaan aktif sesi itu; blokir bila submission sudah `pending`/`verified`; izinkan bila `rejected` (prefill jawaban lama).
- [x] 5.4 `OperationalChecklistController@submit`: validasi lewat Form Request; **ulang** resolusi sesi di server; pastikan employee milik outlet; pastikan semua `question_id` milik section + sesi aktif; siapkan `DB::transaction`.
- [x] 5.5 Di dalam transaksi: `updateOrCreate` submission dengan kunci `(restaurant_id, section_id, business_date, session_type)`, set `status = pending`, reset `verified_by`/`verified_at`/`verification_note`, set `submitted_at = now()`.
- [x] 5.6 Di dalam transaksi: hapus `answers` lama, lalu buat ulang per pertanyaan dengan `answer_number`/`answer_boolean` sesuai `answer_type`, simpan `remarks` (null bila kosong), hitung `result` via evaluator, dan simpan seluruh kolom snapshot dari pertanyaan.
- [x] 5.7 Redirect ke `operational.submitted` dengan ringkasan jumlah `ok`/`not_ok` (flash session).
- [x] 5.8 Tangani race condition: bila unique constraint gagal saat `create` submission baru, tangkap `QueryException` dan arahkan kembali dengan pesan "sudah diisi".

**Kriteria selesai:** submit menghasilkan submission + answers lengkap dengan snapshot dan `result` benar; submit di luar jam / employee beda outlet / section salah ditolak.

### Step 6 — View publik

- [x] 6.1 Buat `layouts/checklist.blade.php`: mobile-first, Bootstrap 5 + Tailwind CDN + SweetAlert2 (mengikuti `layouts/portal.blade.php`), tanpa menu admin, meta viewport, warna navy/gold konsisten.
- [x] 6.2 Buat `operational/entry.blade.php`: nama outlet, badge sesi aktif + jam, grid kartu section (nama + jumlah pertanyaan), chip status bila sudah diisi, kartu "di luar jam checklist" berisi jadwal 3 sesi bila `closed`.
- [x] 6.3 Buat `operational/section.blade.php`: header section + sesi, `select` staff (opsi dari `employees`), kartu pertanyaan berisi **check point** dan **key check point** saja (threshold tidak dirender).
- [x] 6.4 Input jawaban: `number` → `input type="number" step="any"` + label unit; `yes_no` → dua tombol radio Yes/No yang wajib dipilih.
- [x] 6.5 Remarks opsional per pertanyaan: textarea yang bisa dibuka/tutup (Alpine.js atau vanilla JS) agar form tetap ringkas.
- [x] 6.6 Tombol submit dengan konfirmasi SweetAlert2; nonaktifkan tombol setelah diklik untuk mencegah double submit.
- [x] 6.7 Buat `operational/submitted.blade.php`: ringkasan OK / NOT OK + status "menunggu verifikasi" + tombol kembali ke entry.
- [x] 6.8 Cek tampilan di lebar 360px (DevTools) — tidak ada horizontal scroll, target sentuh ≥ 40px.

**Kriteria selesai:** alur scan → section → isi → submit selesai di HP tanpa login, dan threshold tidak terlihat di HTML.

### Step 7 — Master data: Sections

- [x] 7.1 Buat `StoreChecklistSectionRequest` (`restaurant_id` required exists, `name` required max 120, `sort_order` integer min 0, `is_active` boolean).
- [x] 7.2 `ChecklistSectionController@index`: daftar section dengan filter `restaurant_id`, eager load `restaurant` + `withCount('questions')`, paginate 15.
- [x] 7.3 `create`/`store`: form + simpan.
- [x] 7.4 `edit`/`update`: form + update.
- [x] 7.5 `destroy`: hapus section (cascade ke questions); konfirmasi SweetAlert2 di view.
- [x] 7.6 Buat view `operational/sections/{index,create,edit}.blade.php` memakai `layouts/portal`.
- [x] 7.7 Tambah tombol "Kelola Pertanyaan" di setiap baris index menuju `operational.questions.create` dengan `section` terpilih.

**Kriteria selesai:** Super Admin bisa menambah/mengubah/menghapus section per outlet dari UI.

### Step 8 — Master data: Questions

- [x] 8.1 Buat `StoreChecklistQuestionRequest`: `session_type` in `breakfast,lunch,dinner`; `check_point` required; `key_check_point` nullable; `answer_type` in `number,yes_no`; `std_min`/`std_max`/`temp_max` nullable numeric min 0; `unit` nullable max 16; `sort_order` integer; `is_active` boolean.
- [x] 8.2 Tambah validasi silang: `std_min <= std_max` bila keduanya terisi; tolak threshold terisi bila `answer_type = yes_no`; untuk `number`, wajib salah satu dari `temp_max` atau `std_min`/`std_max` (pesan: "Isi minimal satu batas").
- [x] 8.3 `ChecklistQuestionController@create`: form dengan `section` terpilih; tampilkan daftar pertanyaan existing section tersebut dikelompokkan per sesi (tabel: No, Check Point, Key Check Point, Tipe, Std Min, Std Max, Suhu Max, Aksi).
- [x] 8.4 `store`: simpan lalu kembali ke form section yang sama (agar input berurutan cepat).
- [x] 8.5 `edit`/`update`: form lengkap semua kolom + threshold.
- [x] 8.6 `destroy`: hapus pertanyaan; konfirmasi di view.
- [x] 8.7 Buat view `operational/questions/{create,edit}.blade.php`; input threshold memakai pemisah ribuan opsional dan field disabled saat `answer_type = yes_no` (JS toggle).
- [x] 8.8 Pastikan urutan tampil publik mengikuti `sort_order` lalu `id`.

**Kriteria selesai:** pertanyaan bisa dikelola per section + sesi, dengan validasi threshold yang mencegah konfigurasi tidak masuk akal.

### Step 9 — Master data: Session Windows

- [x] 9.1 Buat `UpdateChecklistSessionWindowsRequest`: `windows` array; `windows.*.session_type` in 3 sesi; `windows.*.start_time`/`end_time` format `H:i`; `windows.*.is_active` boolean.
- [x] 9.2 Tambah validasi tumpang tindih: untuk scope yang sama (outlet tertentu atau global), rentang antar sesi tidak boleh beririsan (hitung dengan normalisasi lintas tengah malam).
- [x] 9.3 `ChecklistSessionWindowController@edit`: pemilih scope (`?restaurant_id=` atau `global`), tampilkan 3 baris sesi; bila outlet belum punya baris, prefill dari nilai global.
- [x] 9.4 `update`: `updateOrCreate` per sesi pada scope terpilih; untuk scope outlet, izinkan "hapus override" (kembali ke global).
- [x] 9.5 Buat view `operational/session-windows/edit.blade.php` dengan keterangan bahwa outlet tanpa override memakai default global.
- [x] 9.6 Tampilkan ringkasan window efektif per outlet (hasil resolusi) di halaman yang sama.

**Kriteria selesai:** jam sesi bisa diatur global dan per outlet, tumpang tindih ditolak, dan fallback ke global berjalan.

### Step 10 — QR Barcode

- [x] 10.1 Tambah `RestaurantController@barcode(Restaurant $restaurant)`: kirim `url` absolut `route('operational.entry', $restaurant->checklist_token)`.
- [x] 10.2 Buat view `restaurants/barcode.blade.php`: kartu cetak berisi nama outlet, kode, QR (library CDN, mis. `qrcodejs`), dan URL teks; tombol Print (`window.print()`) + CSS `@media print`.
- [x] 10.3 Tambah tombol "QR Code" di `restaurants/index.blade.php` per baris.
- [x] 10.4 Tambah aksi "Regenerate Token" (POST, konfirmasi SweetAlert2) agar QR lama bisa dibatalkan bila bocor; daftarkan route-nya di grup Super Admin.

**Kriteria selesai:** setiap outlet punya halaman QR siap cetak, dan token bisa diganti bila perlu.

### Step 11 — Verifikasi Manager

- [x] 11.1 `OperationalChecklistReviewController@index`: query submission dengan filter `date` (default hari ini), `restaurant_id`, `status`; batasi outlet sesuai pivot untuk non-Super-Admin; eager load `restaurant`, `section`, `employee`, `answers`; hitung `not_ok` count via `withCount`.
- [x] 11.2 `show`: muat `answers` (snapshot), tampilkan check point, key check point, jawaban + unit, remarks, dan penanda OK/NOT OK; tampilkan info verifikator bila sudah diverifikasi.
- [x] 11.3 `verify`: cek otorisasi outlet; hanya status `pending` yang bisa diverifikasi; set `status = verified`, `verified_by`, `verified_at`.
- [x] 11.4 `reject`: cek otorisasi outlet; `verification_note` required (min 5 karakter); set `status = rejected` + `verified_by`/`verified_at`.
- [x] 11.5 Buat view `operational-checklists/{index,show}.blade.php` memakai `layouts/portal`, dengan badge status berwarna dan highlight baris NOT OK.
- [x] 11.6 Tampilkan jumlah submission `pending` di menu (opsional, cukup badge angka sederhana).

**Kriteria selesai:** manager hanya melihat & memverifikasi outletnya; reject mewajibkan catatan; staff bisa memperbaiki submission yang ditolak.

### Step 12 — Route, menu, dan akses

- [x] 12.1 Daftarkan grup route publik di `routes/web.php` (di luar middleware `auth`) dengan `throttle:60,1`.
- [x] 12.2 Daftarkan grup master data di dalam grup `role:Super Admin` yang sudah ada.
- [x] 12.3 Daftarkan grup verifikasi di grup `role:Super Admin|Restaurant Manager`.
- [x] 12.4 Tambah menu "Operational Checklist" (verifikasi) di `layouts/mantis.blade.php` dan `layouts/portal.blade.php` untuk role terkait.
- [x] 12.5 Tambah menu "Checklist Sections", "Checklist Questions", "Session Windows" di blok Master Data (Super Admin).
- [x] 12.6 Jalankan `php artisan route:list` dan pastikan semua nama route unik serta middleware benar.

**Kriteria selesai:** semua halaman bisa diakses sesuai role; halaman publik tetap di luar `auth`.

### Step 13 — Feature test

- [x] 13.1 `OperationalChecklistFlowTest`: entry page bisa dibuka tanpa login; section menampilkan pertanyaan sesuai sesi aktif (freeze time via `travelTo`).
- [x] 13.2 Test submit sukses: submission `pending`, jumlah answers sesuai, `result` benar untuk tiap tipe.
- [x] 13.3 Test submit di luar jam sesi → ditolak dengan pesan.
- [x] 13.4 Test submit kedua untuk section+sesi+tanggal sama → ditolak.
- [x] 13.5 Test employee dari outlet lain → validasi gagal.
- [x] 13.6 Test pertanyaan sesi lain tidak bisa diselundupkan lewat payload.
- [x] 13.7 `OperationalChecklistReviewTest`: manager outlet A tidak bisa verify submission outlet B (403); Super Admin bisa; reject tanpa catatan gagal; setelah reject staff bisa submit ulang dan status kembali `pending` serta jawaban lama tergantikan.
- [x] 13.8 Pastikan test memakai `RefreshDatabase` dan tidak menyentuh DB development.

**Kriteria selesai:** seluruh test hijau dengan `php artisan test`.

### Step 14 — Dokumentasi & finalisasi

- [x] 14.1 Tambah bagian "Operational Checklist" di `README.md` (alur, route, aturan sesi, cara cetak QR).
- [x] 14.2 Catat entri baru di `docs/session-log.md` sesuai format (entri terbaru di atas, maksimal 15 entri).
- [x] 14.3 Jalankan `./vendor/bin/pint` hanya pada file yang dibuat/diubah.
- [x] 14.4 Jalankan `php artisan test` penuh dan pastikan tidak ada regresi pada test Breeze yang sudah ada.
- [x] 14.5 Review manual: cek tidak ada `dd()`/`dump()`, tidak ada secret di kode, threshold tidak bocor ke halaman publik.

**Kriteria selesai:** dokumentasi diperbarui, formatting rapi, semua test hijau, dan tidak ada perubahan di luar lingkup.

---

## Execution Checklist

- [x] Step 1: Migrasi database (token outlet + 5 tabel checklist)
- [x] Step 2: Model & relasi (5 model + relasi di Restaurant/Employee/User)
- [x] Step 3: Service layer (`ChecklistAnswerEvaluator`, `ChecklistSessionResolver`) + unit test
- [x] Step 4: Seeder jam sesi global (breakfast/lunch/dinner)
- [x] Step 5: Form Request & controller publik (entry, section, submit)
- [x] Step 6: View publik (layout checklist, entry, section, submitted)
- [x] Step 7: Master data — Sections CRUD
- [x] Step 8: Master data — Questions CRUD (threshold + validasi silang)
- [x] Step 9: Master data — Session Windows (global + override per outlet)
- [x] Step 10: QR barcode per outlet (halaman cetak + regenerate token)
- [x] Step 11: Verifikasi manager (index, show, verify, reject)
- [x] Step 12: Route, menu navigasi, dan pengecekan akses
- [x] Step 13: Feature test alur end-to-end (publik + verifikasi)
- [x] Step 14: Dokumentasi (README, session log), Pint, dan test penuh
