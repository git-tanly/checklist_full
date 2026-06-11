# Troubleshooting & Catatan Teknis

Daftar masalah/inkonsistensi yang ditemukan saat audit kode beserta saran perbaikan. Belum diperbaiki di kode, tapi penting diketahui agar onboarding lancar.

## 1. Seeder masih memakai kolom `nik`

**Lokasi:** `database/seeders/RoleAndUserSeeder.php` (dan `UserSeeder.php` yang tidak terpanggil).

```php
$admin = User::firstOrCreate([
    'nik' => '0000',
], [
    'name' => 'Super Admin',
    'password' => $password,
]);
```

**Masalah:** Migrasi `2026_02_05_161201_rename_nik_to_email_in_users_table.php` sudah me-rename kolom `nik` menjadi `email`. Pada DB fresh seeder akan error `Unknown column 'nik'`.

**Saran perbaikan:**

```php
$admin = User::firstOrCreate([
    'email' => 'superadmin@tanly.id',
], [
    'name'     => 'Super Admin',
    'password' => $password,
]);
```

## 2. Middleware `CheckGlobalChecklistAccess` adalah dead code

**Lokasi:** `app/Http/Middleware/CheckGlobalChecklistAccess.php`.

Kode middleware memanggil `$user->hasAppAccess('report')` dan `$user->is_active`, namun:

- Method `hasAppAccess` tidak ada di `App\Models\User`.
- Atribut `is_active` tidak ada di tabel/kolom `users` lokal.
- Middleware tidak di-alias di `bootstrap/app.php` dan tidak diaplikasikan ke route manapun.

**Saran perbaikan:** Tentukan dulu apakah pengecekan akses aplikasi mau ditarik dari Portal (lewat koneksi `mysql_portal` atau panggilan API ke Portal). Jika ya, tambahkan method ke User model atau pakai Application/AppAccess service. Jika tidak, hapus saja middleware ini agar tidak menyesatkan.

## 3. Global Scope `restaurant_scope` di `UpsellingItem` tidak pernah aktif

**Lokasi:** `app/Models/UpsellingItem.php`.

```php
if ($user && $user->restaurant_id && !$user->hasRole('Super Admin')) {
    $builder->where('restaurant_id', $user->restaurant_id);
}
```

Kolom `users.restaurant_id` sudah dihapus pada migrasi pivot (`2025_11_20_222651_create_restaurant_user_pivot_table.php`). Kondisi `$user->restaurant_id` selalu `null`/undefined di Eloquent, sehingga scope tidak pernah aktif untuk non-Super-Admin.

**Saran perbaikan:** Sesuaikan ke pivot baru, mis.:

```php
if ($user && !$user->hasRole('Super Admin')) {
    $ids = $user->restaurants->pluck('id')->toArray();
    $builder->whereIn('restaurant_id', $ids);
}
```

## 4. Typo property di `Restaurant`

**Lokasi:** `app/Models/Restaurant.php`.

```php
protected $tabel = 'restaurants';   // <-- harusnya $table
```

Tidak fatal karena nama tabel tetap mengikuti konvensi (snake_case dari class), tapi sebaiknya diperbaiki untuk hindari kebingungan.

## 5. Gate `is-super-admin` belum didefinisikan

**Lokasi:** `routes/web.php`

```php
Route::get('/users/create', [UserController::class, 'create'])
    ->middleware(['can:is-super-admin'])
    ->name('users.create');
```

Gate `is-super-admin` tidak ada di `App\Providers\AppServiceProvider`. Akibatnya request ke `/users/create` selalu ditolak. Halaman create user efektif tidak dapat diakses.

**Saran perbaikan:**

- Daftarkan gate baru:
  ```php
  Gate::define('is-super-admin', fn ($user) => $user->hasRole('Super Admin'));
  ```
- Atau ganti middleware menjadi `role:Super Admin` (sudah di parent group, jadi sebenarnya redundan dan bisa dihapus saja).

## 6. `composer.json` masih default skeleton

```json
{
    "name": "laravel/laravel",
    "description": "The skeleton application for the Laravel framework."
}
```

Sebaiknya disesuaikan menjadi nama paket internal, mis. `tanly/checklist-full`, plus deskripsi proyek yang sebenarnya.

## 7. `.env.example` belum lengkap untuk SSO & Portal

Kunci yang dipakai di kode tetapi belum ada di `.env.example`:

- `DB_PORTAL_HOST`, `DB_PORTAL_PORT`, `DB_PORTAL_DATABASE`, `DB_PORTAL_USERNAME`, `DB_PORTAL_PASSWORD`, `DATABASE_PORTAL_URL`
- `SSO_PORTAL_URL`, `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, `SSO_REDIRECT_URI`
- `SSO_WEBHOOK_SECRET`

Tambahkan ke `.env.example` (boleh dengan placeholder kosong) agar developer baru tidak perlu menebak.

## 8. `routes/auth.php` tidak diaktifkan

Baris `require __DIR__ . '/auth.php';` di `routes/web.php` dikomentari. Jadi rute Breeze (login lokal, register, password reset) tidak terdaftar. Hanya alur SSO yang aktif.

Bila tidak akan dipakai, hapus saja folder `app/Http/Controllers/Auth/*` dan file `routes/auth.php` agar tidak menyesatkan dan mengurangi permukaan serangan.

## 9. `App\Models\User` tidak menyimpan kolom `email` di `$fillable`

```php
protected $fillable = ['sso_id', 'name', 'email', 'password'];
```

Sebenarnya `email` ada di list — aman. Yang patut diperhatikan: `nik` sudah tidak dipakai, jadi pastikan semua referensi ke `nik` di seeder, factory, atau view sudah dimigrasi ke `email`.

`database/factories/UserFactory.php` perlu dicek juga (kemungkinan masih hasil scaffold default).

## 10. `restaurants.users()` mengarah ke `LocalUser`, bukan `User`

```php
public function users()
{
    return $this->belongsToMany(LocalUser::class, 'restaurant_user', 'restaurant_id', 'user_id');
}
```

Hal ini disengaja agar relasi pivot selalu memakai koneksi `mysql` (bukan `mysql_portal`). Jika nanti `User` model sudah di-pin koneksi yang benar, relasi ini bisa disederhanakan menjadi `belongsToMany(User::class, ...)` agar konsisten dengan `User::restaurants()`.

## 11. SuperAdmin email referensi (`superadmin@tanly.id`)

`UserController::index` mem-filter user dengan `where('email', '!=', 'superadmin@tanly.id')`. Pastikan email Super Admin yang dibuat oleh seeder dan/atau Portal sesuai dengan filter ini. Kalau berbeda, super admin akan ikut tampil di list manage users.

## 12. Tidak ada test untuk modul utama

`tests/Feature` masih kosong dari kebutuhan domain. Test yang sebaiknya ditambahkan:

- `DailyReportController` — workflow draft/submit/approve/reject, validasi submit, otorisasi cross-outlet.
- `SloWebhookController` — verifikasi HMAC sukses & gagal, idempotency.
- `RevenueTargetController::store` — mode single vs full year, sanitasi rupiah, otorisasi non-Super-Admin.

---

Bila Anda memperbaiki salah satu item di atas, perbarui dokumen ini agar daftar inkonsistensi tetap akurat.
