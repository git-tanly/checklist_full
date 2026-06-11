# Workflow Daily Report

Modul Daily Report adalah inti aplikasi. Dokumen ini menjelaskan alur status, struktur data per session, validasi yang berjalan, dan cara field JSON disusun.

## Diagram Status

```
                +------ submit (Manager / Super Admin) ------+
                |                                            v
   [draft] --submit (staf) --> [submitted] -- approve --> [approved]
       ^                            |                         |
       |                            +-- reject (Manager) -----+
       +-------- (manual) edit ------+
```

- **draft** — bisa diedit/dihapus oleh pemilik. Tidak dihitung di chart/widget revenue.
- **submitted** — laporan menunggu approval Manager. Tidak bisa diedit oleh pemilik (harus di-reject dulu agar kembali ke draft).
- **approved** — final. Berkontribusi ke widget Today's Revenue, MTD vs Target, chart 7 hari, breakdown performance, dan modal analytics. Boleh diunduh sebagai PDF.

## Aksi & Aturan

| Aksi | Method | Aturan |
|---|---|---|
| Buat draft | `store` (`action=draft`) | Validasi minimal: `restaurant_id`, `date`. Non-Super-Admin hanya untuk outletnya. |
| Submit | `store` (`action=submit`) | Validasi penuh `validateSubmission()`. Jika user adalah `isUserApprover` (Super Admin / Restaurant Manager) → status langsung `approved`. Selain itu → `submitted`. |
| Edit | `update` | Hanya jika status saat ini = `draft`. |
| Update + submit | `update` (`action=submit`) | Sama dengan submit baru. Detail lama dihapus dan ditulis ulang. |
| Approve | `approve` | Hanya `isUserApprover`. Hanya status `submitted`. Set `approved_by`, `approved_at`. |
| Reject | `reject` | Hanya `isUserApprover`. Tidak boleh untuk status `draft`. Set status kembali `draft`, `approved_by/at` di-null-kan. |
| Hapus | `destroy` | Hanya Super Admin / Restaurant Manager. Hard delete. |
| PDF | `downloadPdf` | Hanya status `approved`. Filename: `Report_{code}_{Y-m-d}.pdf`. |

`isUserApprover` di `DailyReportController` adalah:

```php
$user->hasAnyRole(['Super Admin', 'Restaurant Manager']);
```

## Struktur Data per Session

Setiap `DailyReport` punya hingga 4 `DailyReportDetail` dengan `session_type ∈ {breakfast, lunch, dinner, supper}`. Per session terdiri dari:

- 4 kolom revenue numeric (`revenue_food`, `revenue_beverage`, `revenue_others`, `revenue_event`).
- 4 kolom JSON: `cover_data`, `upselling_data`, `competitor_data`, `additional_data`.
- Field text/array: `thematic`, `staff_on_duty[]`, `remarks`, `vip_remarks[]`.

### `cover_data`
Map jumlah cover/pax. Struktur keys-nya **dinamis per outlet**, contoh umum:

```json
{
  "in_house_adult": 120,
  "in_house_child": 18,
  "outside_guest": 42,
  "complimentary": 5
}
```

Aplikasi tidak memvalidasi nama key karena setiap outlet boleh punya kategori berbeda. Saat agregasi (mis. di `DashboardController::getOutletAnalytics`), aplikasi menormalkan nama (mis. `in_house_adult` → `In House Adult`) lalu menjumlahkan.

### `upselling_data`
Map `upselling_items.id` ke jumlah terjual. Form di `daily-reports/create` & `edit` me-render dropdown menu sesuai outlet yang dipilih, lalu user mengisi quantity per item:

```json
{
  "12": 4,
  "13": 0,
  "27": 7
}
```

Server menerima string JSON dari hidden input, lalu `sanitizeSessionData` melakukan `json_decode` sehingga tersimpan sebagai array.

### `competitor_data`
Map cover hotel kompetitor:

```json
{
  "shangrila_cover": 95,
  "jw_marriott_cover": 80,
  "sheraton_cover": 110
}
```

`DashboardController::index` membaca tepat 3 kunci ini untuk chart competitor di dashboard. Endpoint analytics outlet (`getOutletAnalytics`) memprosesnya secara dinamis (semua kunci yang berisi numeric akan ditampilkan).

### `additional_data`
Cadangan untuk kebutuhan outlet tertentu (misal paket spesifik di Chamas). Tidak diolah di dashboard default.

### `staff_on_duty`
Array nama/ID staff. Form mengirim hidden JSON; cast model `array` membuat penyimpanan kembali ke JSON di DB.

### `vip_remarks`
Array entri remark VIP, misal `[{ "name": "Mr. X", "note": "Birthday" }]`. Cast `array`.

## Validasi `validateSubmission`

Bila `action === 'submit'`, controller menerapkan rules berikut:

```php
'session'                     => 'required|array',
'session.*.cover_data'        => 'required|array',
'session.*.revenue_food'      => 'required|numeric|min:0',
'session.*.revenue_beverage'  => 'required|numeric|min:0',
'session.*.revenue_others'    => 'required|numeric|min:0',
'session.*.revenue_event'     => 'required|numeric|min:0',
'session.*.remarks'           => 'required|string',
'session.*.staff_on_duty'     => 'required|array|min:1',
'session.*.competitor_data'   => 'required|array',
```

Pesan kesalahan dilokalisasi (Bahasa Indonesia), mis. "Cover Report details wajib diisi.".

## Sanitasi Data

`sanitizeSessionData` membersihkan input sebelum validasi:

- **Money fields** (`revenue_food/beverage/others/event`) — `str_replace('.', '', $val)` agar input "1.500.000" tersimpan sebagai `1500000`.
- **JSON fields** (`staff_on_duty`, `upselling_data`, `vip_remarks`) — bila bertipe string, `json_decode($val, true)`. Bila gagal decode, di-fallback ke array kosong.

## Otorisasi Tambahan di Form

`create()` dan `edit()` mengirim daftar `restaurants` yang relevan ke view:

```php
if ($user->hasRole('Super Admin')) {
    $restaurants = Restaurant::with('users')->get();
} else {
    $restaurants = $user->restaurants()->with('users')->get();
}
```

Sehingga dropdown outlet di form hanya menampilkan outlet milik user. Server tetap melakukan validasi `restaurant_id` saat `store/update`, jadi manipulasi DOM tidak akan lolos.

## Generate PDF

`downloadPdf` menggunakan view `resources/views/daily-reports/pdf.blade.php` dengan paper A4 portrait. File diunduh dengan nama `Report_{restaurant.code-tanpa-spasi}_{date Y-m-d}.pdf`.

```php
$pdf = Pdf::loadView('daily-reports.pdf', compact('dailyReport'));
$pdf->setPaper('a4', 'portrait');
return $pdf->download($filename);
```
