# Role, Permission, dan Akses Data

Dokumen ini merangkum siapa boleh mengakses apa di aplikasi, di mana batasan ditegakkan dalam kode, dan bagaimana mapping user ↔ outlet bekerja.

## Daftar Role

Didaftarkan oleh `database/seeders/RoleAndUserSeeder.php`:

1. Super Admin
2. Restaurant Manager
3. Assistant Restaurant Manager
4. F&B Supervisor
5. Waiter
6. Cashier
7. Bartender
8. Daily Worker (default untuk user yang baru JIT-provisioning lewat SSO)
9. Trainee

Tidak ada permission granular Spatie yang dibuat — otorisasi seluruhnya berbasis nama role.

## Lapisan Otorisasi

1. **Middleware `auth`** — wajib login session.
2. **Gate `access-checklist-app`** — didefinisikan di `App\Providers\AppServiceProvider`. User wajib memiliki minimal salah satu role di atas. Diterapkan sebagai `can:access-checklist-app` pada grup utama di `routes/web.php`.
3. **Role middleware Spatie** — `role:Super Admin` dan `role:Super Admin|Restaurant Manager` diaplikasikan ke modul administrasi dan revenue target.
4. **Gate khusus di controller** — pengecekan `hasRole` / `hasAnyRole` di `DailyReportController::approve|reject|destroy`, `DashboardController::getOutletAnalytics`, dan `RevenueTargetController::store`.
5. **Global Scope** — `restaurant_scope` di `DailyReport` membatasi query non-Super-Admin hanya untuk outlet yang ada di pivot `restaurant_user`.
6. **Manual scope** — di `DailyReportController::store/update`, controller menolak request bila `restaurant_id` yang dipost bukan outlet milik user (`!in_array($restaurant_id, $myRestoIds)`).

## Matriks Akses Fitur

Legend: ✔ punya akses, ✖ tidak, ◑ akses dengan batasan tertentu.

| Fitur / Aksi | Super Admin | Restaurant Manager | Asst. RM / F&B Sup. / Waiter / Cashier / Bartender / Daily Worker / Trainee |
|---|:---:|:---:|:---:|
| Dashboard widget & charts | ✔ (semua outlet) | ◑ (outletnya) | ◑ (outletnya) |
| Modal analytics outlet | ✔ | ✔ (outletnya) | ✔ (outletnya) |
| Lihat list daily reports | ✔ (semua) | ◑ (outletnya — Global Scope) | ◑ (outletnya — Global Scope) |
| Buat draft / submit laporan | ✔ | ✔ | ✔ |
| Submit → langsung Approved (auto-approve) | ✔ | ✔ | ✖ (status menjadi `submitted`) |
| Edit laporan (status draft saja) | ✔ | ✔ | ✔ |
| Approve / Reject laporan | ✔ | ✔ | ✖ |
| Hapus laporan | ✔ | ✔ | ✖ |
| Download PDF (approved only) | ✔ | ✔ | ✔ |
| CRUD Restaurants | ✔ | ✖ | ✖ |
| CRUD Upselling Items | ✔ | ✖ | ✖ |
| CRUD Users (sync role & outlet) | ✔ | ✖ | ✖ |
| CRUD / lihat Revenue Targets | ✔ (semua) | ✔ (outletnya) | ✖ |
| Profile (lihat/edit) | ✔ | ✔ | ✔ |

> "Auto-approve" untuk Restaurant Manager: pada `DailyReportController::store/update`, jika `action=submit` dan `isUserApprover($user)` benar (`Super Admin|Restaurant Manager`), status langsung `approved` dan `approved_by/at` terisi.

## Mapping User ↔ Outlet

Tabel pivot `restaurant_user` (kolom `user_id`, `restaurant_id`, `timestamps`, unique `[user_id, restaurant_id]`). Satu user bisa memegang banyak outlet (cluster) dan satu outlet dipegang banyak user.

Diakses melalui:

- `App\Models\User::restaurants()` — `belongsToMany(Restaurant::class, 'restaurant_user')->withTimestamps()`.
- `App\Models\Restaurant::users()` — `belongsToMany(User::class, 'restaurant_user', 'restaurant_id', 'user_id')`.

Sinkronisasi dilakukan di `UserController@update` (`$user->restaurants()->sync($ids)`) dan saat user baru lewat SSO (`$user->restaurants()->attach($default209->id)`).

## Default untuk User Baru (JIT)

Saat login pertama via SSO, jika user belum ada di tabel lokal:

- Role: `Daily Worker`
- Outlet: `209 Dining`

Super Admin perlu menyesuaikan keduanya dari menu **Users → Edit** sebelum user bisa bekerja efektif di outletnya sendiri.

## Catatan: Gate yang Belum Terdefinisi

Pada `routes/web.php`:

```php
Route::get('/users/create', [UserController::class, 'create'])
    ->middleware(['can:is-super-admin'])
    ->name('users.create');
```

Gate `is-super-admin` belum didefinisikan di `AppServiceProvider` (yang ada hanya `access-checklist-app`). Dampaknya, request ke `/users/create` akan ditolak walau user adalah Super Admin. Lihat `docs/troubleshooting.md` untuk rekomendasi perbaikan.
