# Skema Database

Aplikasi memakai dua koneksi MySQL: `mysql` (lokal) dan `mysql_portal` (Portal SSO). Dokumen ini fokus pada koneksi `mysql` (schema utama aplikasi). Migrasi ada di `database/migrations/`.

## Daftar Migrasi (urut kronologis)

| File | Ringkasan |
|---|---|
| `0001_01_01_000000_create_users_table.php` | `users` (id, name, **nik unique**, email_verified_at, password, remember_token, timestamps); `password_reset_tokens` (email pk, token, created_at); `sessions` (id pk, user_id index, ip_address, user_agent, payload, last_activity index). |
| `0001_01_01_000001_create_cache_table.php` | Tabel cache standar Laravel. |
| `0001_01_01_000002_create_jobs_table.php` | Tabel queue standar Laravel. |
| `2025_11_19_070357_create_restaurants_table.php` | `restaurants` (id, code unique, name, timestamps). |
| `2025_11_19_070409_create_daily_reports_table.php` | `daily_reports` (id, restaurant_id FK→restaurants cascade, user_id FK→users cascade, **date** (date), status enum[draft, submitted, approved] default `draft`, approved_by FK→users null, approved_at, timestamps). |
| `2025_11_19_070417_create_daily_report_details_table.php` | `daily_report_details` (id, daily_report_id FK cascade, session_type enum[breakfast, lunch, dinner], 4 kolom revenue decimal(15,2) nullable, 4 kolom JSON, thematic, staff_on_duty text, remarks, vip_remarks text, timestamps). |
| `2025_11_20_093049_create_upselling_items_table.php` | `upselling_items` (id, restaurant_id FK cascade, type enum[food, beverage], name, timestamps). |
| `2025_11_20_120937_create_permission_tables.php` | Tabel-tabel Spatie Permission (`permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`). |
| `2025_11_20_121151_add_restaurant_id_to_users_table.php` | Menambah kolom `users.restaurant_id` (FK ke restaurants, nullable). |
| `2025_11_20_222651_create_restaurant_user_pivot_table.php` | Buat pivot `restaurant_user`, **migrasi data** dari `users.restaurant_id` ke pivot, lalu **drop** kolom `users.restaurant_id`. |
| `2025_11_21_091012_change_date_column_to_datetime_in_daily_reports.php` | Mengubah `daily_reports.date` menjadi `DATETIME`. |
| `2025_11_26_152812_create_revenue_targets_table.php` | `revenue_targets` (id, restaurant_id FK cascade, year int, month int, amount decimal(15,2), timestamps, **unique [restaurant_id, year, month]**). |
| `2025_11_28_133209_modify_session_type_enum_in_details.php` | Mengubah enum `session_type` menjadi `[breakfast, lunch, dinner, supper]`. |
| `2026_02_05_161201_rename_nik_to_email_in_users_table.php` | **Rename** `users.nik` → `users.email` + index unique. |
| `2026_06_11_123402_add_sso_id_to_users_table.php` | Menambah `users.sso_id` (uuid, nullable, unique). |

## Tabel & Relasi

```
restaurants ----< daily_reports ----< daily_report_details
   |  ^                |  ^
   |  |                |  +---- approved_by ---> users
   |  +---- restaurant_user >---- users
   |
   +---< upselling_items
   +---< revenue_targets

users ----< sessions
users ----< daily_reports (user_id, approved_by)

(Spatie)
roles ----< role_has_permissions >---- permissions
model_has_roles (morph) >----- users (model_type App\Models\User)
model_has_permissions (morph)
```

### `users`
Kolom utama setelah seluruh migrasi: `id, name, email (unique), email_verified_at, password, remember_token, sso_id (uuid unique nullable), timestamps`.

Catatan: kolom `restaurant_id` sudah dihapus pada migrasi `2025_11_20_222651`. Mapping user ke outlet sekarang melalui pivot `restaurant_user` (many-to-many).

### `restaurants`
`id, code (unique, max 10), name, timestamps`. Dipakai sebagai FK utama di banyak tabel; saat dihapus, cascade ke `daily_reports`, `daily_report_details`, `upselling_items`, `revenue_targets`, `restaurant_user`.

### `restaurant_user` (pivot)
`id, user_id (FK cascade), restaurant_id (FK cascade), timestamps, unique [user_id, restaurant_id]`.

### `daily_reports`
Header laporan harian per outlet:

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint pk | |
| restaurant_id | FK → restaurants | cascade |
| user_id | FK → users | cascade, pembuat laporan |
| date | datetime | tanggal laporan (diubah dari date di migrasi 2025_11_21) |
| status | enum | `draft`, `submitted`, `approved` (default `draft`) |
| approved_by | FK → users null | terisi saat approved |
| approved_at | timestamp null | terisi saat approved |
| created_at, updated_at | | |

### `daily_report_details`
Detail laporan per session (1 laporan = sampai 4 detail: breakfast/lunch/dinner/supper).

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint pk | |
| daily_report_id | FK → daily_reports | cascade |
| session_type | enum | `breakfast`, `lunch`, `dinner`, `supper` |
| revenue_food | decimal(15,2) null | |
| revenue_beverage | decimal(15,2) null | |
| revenue_others | decimal(15,2) null | |
| revenue_event | decimal(15,2) null | |
| cover_data | json null | struktur dinamis per outlet (mis. `in_house_adult`, `in_house_child`, dll) |
| upselling_data | json null | jumlah upselling per `upselling_items.id` |
| competitor_data | json null | mis. `shangrila_cover`, `jw_marriott_cover`, `sheraton_cover` |
| additional_data | json null | cadangan untuk kebutuhan outlet tertentu (mis. paket Chamas) |
| thematic | string null | tema/event harian |
| staff_on_duty | text/array | dicast `array` di model |
| remarks | text null | |
| vip_remarks | text/array | dicast `array` di model |
| created_at, updated_at | | |

### `upselling_items`
`id, restaurant_id (FK cascade), type (enum food/beverage), name, timestamps`. Satu master menu upselling milik satu outlet.

### `revenue_targets`
`id, restaurant_id (FK cascade), year (int), month (int), amount (decimal 15,2), timestamps, unique [restaurant_id, year, month]`. Target omzet per bulan/outlet.

### Spatie Permission
Tabel default: `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`. Aplikasi hanya memakai role (tidak ada permission granular). Daftar role didaftarkan oleh `RoleAndUserSeeder`:

```
Super Admin, Restaurant Manager, Assistant Restaurant Manager,
F&B Supervisor, Waiter, Cashier, Bartender, Daily Worker, Trainee
```

### `sessions`
Tabel session standar Laravel (driver `database`). Dihapus baris user oleh `SloWebhookController` saat menerima webhook SLO dari Portal.

## Casts & Relasi pada Model

`App\Models\User`
- `casts`: `email_verified_at => datetime`, `password => hashed`.
- Relasi: `restaurants()` belongsToMany `Restaurant` via pivot `restaurant_user`.
- Trait: `HasFactory`, `Notifiable`, `Spatie\Permission\Traits\HasRoles`.
- Helper: `isSuperAdmin`, `isRestaurantManager`, `isAssRestaurantManager`, `isFnBSupervisor`, `isWaiter`, `isCashier`, `isBartender`, `isDailyWorker`, `isTrainee` (semua wrapper `hasRole`).

`App\Models\Restaurant`
- Connection `mysql`, fillable `code, name`.
- `dailyReports()` hasMany, `upsellingItems()` hasMany, `users()` belongsToMany `user` via pivot.

`App\Models\DailyReport`
- Casts `date => datetime`, `approved_at => datetime`.
- **Global Scope `restaurant_scope`**: jika user login & bukan Super Admin, query difilter `whereIn('restaurant_id', $user->restaurants->pluck('id'))`.
- `restaurant`, `user`, `approver` (FK approved_by), `details` hasMany.

`App\Models\DailyReportDetail`
- Casts: `cover_data, upselling_data, competitor_data, additional_data, vip_remarks, staff_on_duty` → `array`. Revenue → `decimal:2`.
- `dailyReport` belongsTo.

`App\Models\UpsellingItem`
- Fillable `restaurant_id, type, name`.
- Global Scope `restaurant_scope` mengacu `$user->restaurant_id` — kolom yang sudah dihapus (lihat `docs/troubleshooting.md`).

`App\Models\RevenueTarget`
- Fillable `restaurant_id, year, month, amount`. Cast `amount => decimal:2`. Relasi `restaurant` belongsTo.

`App\Models\Role`
- Override Spatie `Role` agar relasi `users()` menunjuk ke `App\Models\LocalUser` (memastikan pivot dibaca dari koneksi `mysql`).

`App\Models\LocalUser`
- Connection `mysql`, table `users`, `guard_name = web`. Tidak auto-increment (id ditentukan dari Portal). Trait `HasRoles`.

`App\Models\Application`
- Connection `mysql_portal`, table `applications`. Metadata aplikasi yang terdaftar di Portal.

## Seeder

`DatabaseSeeder` memanggil:

1. `RestaurantSeeder` — 8 outlet dengan `code` unik.
2. `UpsellingItemSeeder` — daftar menu upselling per outlet.
3. `RoleAndUserSeeder` — 9 role + 1 user `Super Admin` (NIK `0000`, password `password`).

> Catatan: `RoleAndUserSeeder` & `UserSeeder` masih insert kolom `nik`. Migrasi `2026_02_05` sudah me-rename `nik → email`, jadi seeder ini akan error pada DB fresh sampai diperbarui.
