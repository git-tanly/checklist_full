# API & Endpoint Reference

Aplikasi terdiri dari rute web (Blade-driven) dan satu rute API webhook untuk integrasi SSO. Rute web membutuhkan session login (kecuali rute auth) dan dilindungi Gate `access-checklist-app` plus role-based middleware Spatie.

## Konvensi

- Base URL contoh: `http://localhost:8000`
- Otentikasi web menggunakan session cookie. Login lewat `/login` (redirect ke Portal SSO).
- Sebagian besar response berupa HTML (Blade) atau redirect dengan flash message; hanya satu endpoint API JSON-pure (`/api/sso/slo`).

## Rute Otentikasi

| Method | Path | Controller | Keterangan |
|---|---|---|---|
| GET | `/` | inline closure | Redirect ke `/login`. |
| GET | `/login` | `AuthController@redirect` | Generate state random, redirect ke `${SSO_PORTAL_URL}/oauth/authorize`. |
| GET | `/auth/redirect` | `AuthController@redirect` | Alias `/login`. |
| GET | `/auth/callback` | `AuthController@callback` | Callback OAuth, tukar code → token, ambil profil, JIT provisioning, login. |
| POST | `/logout` | `AuthController@logout` | Invalidate session lokal, redirect ke `${SSO_PORTAL_URL}/logout`. |

## Rute Aplikasi (di belakang `auth` + `can:access-checklist-app`)

### Dashboard

| Method | Path | Controller | Akses |
|---|---|---|---|
| GET | `/dashboard` | `DashboardController@index` | Semua role |
| GET | `/dashboard/analytics/{restaurant}` | `DashboardController@getOutletAnalytics` | Super Admin atau pemilik outlet |

`index` menerima query parameter:
- `start_date` (Y-m-d, default 6 hari lalu)
- `end_date` (Y-m-d, default hari ini)
- `restaurant_id` (opsional)

`getOutletAnalytics` menerima:
- `start_date`, `end_date` (opsional, default awal–akhir bulan ini)
- Path param: `restaurant` (route model binding)
Mengembalikan view `analytics_modal` (HTML partial) yang berisi matriks revenue / cover / competitor / day-of-week per session.

### Daily Reports

| Method | Path | Controller | Akses |
|---|---|---|---|
| GET | `/daily-reports` | `index` | Semua role (terbatas Global Scope) |
| GET | `/daily-reports/create` | `create` | Semua role |
| POST | `/daily-reports` | `store` | Semua role (untuk outletnya) |
| GET | `/daily-reports/{dailyReport}` | `show` | Semua role |
| GET | `/daily-reports/{dailyReport}/edit` | `edit` | Pemilik / Super Admin (status draft) |
| PUT | `/daily-reports/{dailyReport}` | `update` | Pemilik / Super Admin (status draft) |
| GET | `/daily-reports/{dailyReport}/pdf` | `downloadPdf` | Status `approved` |
| PATCH | `/daily-reports/{dailyReport}/approve` | `approve` | Super Admin & Restaurant Manager |
| PATCH | `/daily-reports/{dailyReport}/reject` | `reject` | Super Admin & Restaurant Manager |
| DELETE | `/daily-reports/{dailyReport}` | `destroy` | Super Admin & Restaurant Manager |

Body POST/PUT (multipart/form-urlencoded):

```
restaurant_id  : integer (FK restaurants)
date           : Y-m-d (datetime di DB)
action         : "draft" | "submit"
session[breakfast][revenue_food]      : numeric (boleh "1.500.000", akan di-strip titik)
session[breakfast][revenue_beverage]  : numeric
session[breakfast][revenue_others]    : numeric
session[breakfast][revenue_event]     : numeric
session[breakfast][cover_data]        : array
session[breakfast][upselling_data]    : JSON string (akan di-decode)
session[breakfast][competitor_data]   : array
session[breakfast][additional_data]   : array
session[breakfast][thematic]          : string
session[breakfast][staff_on_duty]     : JSON string (akan di-decode → array)
session[breakfast][remarks]           : string
session[breakfast][vip_remarks]       : JSON string (akan di-decode)
... ulang untuk lunch, dinner, supper
```

Validasi `submit` (action = `submit`) lebih ketat dibanding `draft`: `cover_data`, semua `revenue_*`, `remarks`, `staff_on_duty` (min 1), dan `competitor_data` wajib.

### Master Outlet (Restaurants) — Super Admin

| Method | Path | Controller |
|---|---|---|
| GET | `/restaurants` | `index` |
| GET | `/restaurants/create` | `create` |
| POST | `/restaurants` | `store` |
| GET | `/restaurants/{restaurant}/edit` | `edit` |
| PUT | `/restaurants/{restaurant}` | `update` |
| DELETE | `/restaurants/{restaurant}` | `destroy` |

Validasi: `code` (string, max 10, unique), `name` (string, max 255).

### Upselling Items — Super Admin

| Method | Path | Controller |
|---|---|---|
| GET | `/upselling-items` | `index` (filter `?restaurant_id=`) |
| GET | `/upselling-items/create` | `create` |
| POST | `/upselling-items` | `store` |
| GET | `/upselling-items/{upsellingItem}/edit` | `edit` |
| PUT | `/upselling-items/{upsellingItem}` | `update` |
| DELETE | `/upselling-items/{upsellingItem}` | `destroy` |

Validasi: `restaurant_id` (exists), `type` (`food`/`beverage`), `name` (max 255).

### Users — Super Admin

| Method | Path | Controller |
|---|---|---|
| GET | `/users` | `index` (paginate 10, exclude `superadmin@tanly.id`) |
| GET | `/users/create` | `create` (middleware `can:is-super-admin` — gate ini belum didefinisikan, lihat troubleshooting) |
| GET | `/users/{user}/edit` | `edit` |
| PUT | `/users/{user}` | `update` |
| DELETE | `/users/{user}` | `destroy` |

Body PUT untuk `update`:

```
role             : string (nama role yang ada di Spatie)
restaurants      : array of restaurant_id
restaurants.*    : exists:restaurants,id
```

`update` melakukan `syncRoles($role)` lalu `restaurants()->sync($restaurants)`. Tidak mengubah `name`/`email`. Hanya hak akses.

### Revenue Targets — Super Admin & Restaurant Manager

| Method | Path | Controller |
|---|---|---|
| GET | `/revenue-targets?year=YYYY` | `index` |
| POST | `/revenue-targets` | `store` |

Body POST:

```
restaurant_id : integer
year          : integer
month         : integer (wajib jika TIDAK ada is_full_year)
amount        : numeric (boleh berformat "1.500.000")
is_full_year  : checkbox (opsional). Jika ada → loop month 1..12 dengan amount sama.
```

Hubungan unik: `[restaurant_id, year, month]` (`updateOrCreate`).

### Profile

| Method | Path | Controller |
|---|---|---|
| GET | `/profile` | `ProfileController@edit` |
| PATCH | `/profile` | `ProfileController@update` (validasi via `ProfileUpdateRequest`) |
| DELETE | `/profile` | `ProfileController@destroy` (perlu `current_password`) |

## API: Webhook Single Logout

```
POST /api/sso/slo
Content-Type: application/json
```

Body:

```json
{
  "sso_id": "550e8400-e29b-41d4-a716-446655440000",
  "signature": "hex-hmac-sha256-of-sso_id-with-secret"
}
```

Perilaku:

1. Validasi `sso_id` (string required) dan `signature` (string required).
2. Hitung `hash_hmac('sha256', sso_id, env('SSO_WEBHOOK_SECRET'))`.
3. Bandingkan dengan `signature` menggunakan `hash_equals` (anti timing attack).
4. Jika cocok dan user lokal `sso_id` ditemukan → hapus seluruh row di `sessions` untuk `user_id` tersebut.
5. Jika user tidak ditemukan → log info, tetap kembalikan 200 (idempotent).

Response:

| Kondisi | HTTP | Body |
|---|---|---|
| Signature invalid | 401 | `{ "message": "Unauthorized / Invalid Signature" }` |
| Sukses (user ada / tidak ada) | 200 | `{ "message": "SLO processed successfully" }` |
| Validasi gagal | 422 | Standard validation response |

## Health Check

`GET /up` — disediakan oleh Laravel 12 (didaftarkan di `bootstrap/app.php` via `health: '/up'`). Mengembalikan status sehat.
