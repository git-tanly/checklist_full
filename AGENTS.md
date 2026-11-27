# AGENTS.md

**Laravel 12 Development Guidelines**

Dokumen ini berisi instruksi dan standar pengembangan untuk AI Coding Agents yang bekerja pada proyek ini.

Seluruh perubahan kode wajib mengikuti panduan yang telah ditetapkan dalam dokumen ini.

## 1. Project Overview

| Component          | Technology                                                 |
| ------------------ | ---------------------------------------------------------- |
| Framework          | Laravel 12                                                 |
| Language           | PHP 8.2+                                                   |
| Database           | MySQL 8.0+                                                 |
| ORM                | Eloquent ORM                                               |
| Frontend           | Ikuti teknologi frontend yang sudah digunakan dalam proyek |
| Package Manager    | Composer                                                   |
| Database Migration | Laravel Migrations                                         |
| Testing            | PHPUnit atau Pest (ikuti konfigurasi proyek)               |

### Prinsip Utama

- Gunakan fitur bawaan Laravel sebelum membuat implementasi sendiri.
- Terapkan clean code dan prinsip SOLID.
- Hindari kode duplikat (DRY).
- Buat kode yang mudah dibaca, dikembangkan, dan dipelihara.
- Jangan mengubah arsitektur proyek tanpa alasan yang jelas.
- Prioritaskan keamanan, performa, dan konsistensi data.
- Jangan membuat fitur yang tidak diminta.

## 2. Laravel Development Standards

### General Rules

- Gunakan Laravel 12 conventions.
- Gunakan dependency injection.
- Manfaatkan Service Container Laravel.
- Gunakan named routes.
- Gunakan route model binding jika sesuai.
- Gunakan Form Request untuk validasi request yang kompleks.
- Gunakan API Resources untuk standardisasi response API.
- Gunakan Eloquent ORM untuk interaksi database.
- Hindari raw SQL kecuali diperlukan untuk optimasi.
- Gunakan database transactions untuk operasi yang melibatkan beberapa perubahan data.

### Naming Conventions

| Component  | Convention              | Example               |
| ---------- | ----------------------- | --------------------- |
| Model      | Singular PascalCase     | User, Product         |
| Controller | PascalCase + Controller | UserController        |
| Migration  | snake_case              | create_products_table |
| Table      | Plural snake_case       | products              |
| Column     | snake_case              | created_at            |
| Method     | camelCase               | getUserData()         |
| Variable   | camelCase               | $userData             |
| Request    | PascalCase + Request    | StoreUserRequest      |
| Resource   | PascalCase + Resource   | UserResource          |
| Service    | PascalCase + Service    | UserService           |
| Enum       | PascalCase              | UserStatus            |
| Route name | dot notation            | users.index           |

## 3. Project Structure

Ikuti struktur standar Laravel 12.

Direkomendasikan menggunakan struktur berikut apabila proyek membutuhkan pemisahan business logic:

```text
app/
├── Enums/
├── Exceptions/
├── Http/
│ ├── Controllers/
│ ├── Middleware/
│ ├── Requests/
│ └── Resources/
├── Models/
├── Providers/
├── Services/
└── Actions/

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php
├── console.php
└── web.php

tests/
├── Feature/
└── Unit/
```

### Catatan

- Jangan membuat folder tambahan jika belum diperlukan.
- Gunakan struktur yang sudah ada apabila proyek memiliki konvensi sendiri.
- Hindari membuat terlalu banyak abstraksi untuk fitur sederhana.

## 4. Database Guidelines (MySQL)

Database menggunakan MySQL.

### Database Configuration

Gunakan konfigurasi melalui file `.env`.

Contoh konfigurasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Aturan

- Jangan hardcode kredensial database.
- Jangan commit file `.env`.
- Gunakan `.env.example` sebagai template konfigurasi.
- Jangan mengakses database menggunakan kredensial yang ditulis langsung dalam source code.

### Database Design

- Gunakan InnoDB sebagai storage engine.
- Gunakan foreign key untuk relasi antar tabel.
- Gunakan index pada kolom yang sering digunakan untuk filtering dan pencarian.
- Gunakan unique index untuk data yang harus unik.
- Gunakan `created_at` dan `updated_at` jika diperlukan.
- Gunakan tipe data yang sesuai dengan kebutuhan.
- Hindari menyimpan data relasional dalam bentuk JSON jika relasi tersebut lebih tepat direpresentasikan menggunakan tabel terpisah.
- Gunakan `DECIMAL` untuk nilai keuangan, bukan `FLOAT`.
- Pertimbangkan penggunaan soft deletes jika data perlu dipulihkan.

### Migration Rules

Setiap perubahan struktur database wajib menggunakan migration.

Contoh:

```bash
php artisan make:migration create_products_table
```

#### Ketentuan

- Jangan mengubah struktur database secara manual tanpa migration.
- Jangan menghapus migration lama yang sudah digunakan di environment bersama.
- Setiap migration harus memiliki method `up()` dan `down()` yang benar.
- Tambahkan foreign key sesuai kebutuhan.
- Pertimbangkan dampak migration terhadap data production.
- Jangan menjalankan `migrate:fresh` atau `db:wipe` tanpa persetujuan eksplisit.

### Eloquent Models

- Gunakan `$fillable` atau `$guarded` untuk mass assignment protection.
- Definisikan relationship dengan benar.
- Gunakan casts untuk atribut dengan tipe data khusus.
- Hindari query berulang dalam loop (N+1 problem).
- Gunakan eager loading dengan `with()` jika diperlukan.

Contoh:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
}
```

## 5. Controller Guidelines

Controller harus bertanggung jawab terhadap HTTP request dan response.

Hindari menempatkan business logic yang kompleks di dalam controller.

Contoh:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }
}
```

### Ketentuan

- Gunakan dependency injection jika dibutuhkan.
- Gunakan Form Request untuk validasi.
- Delegasikan business logic kompleks ke Service atau Action.
- Hindari controller yang terlalu besar.
- Gunakan HTTP status code yang sesuai untuk API.

## 6. Validation Guidelines

Validasi harus dilakukan menggunakan Laravel Validation.

Untuk request yang kompleks, buat Form Request.

Contoh:

```bash
php artisan make:request StoreProductRequest
```

Implementasi:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
        'stock' => ['required', 'integer', 'min:0'],
    ];
}
```

### Ketentuan

- Jangan hanya mengandalkan validasi frontend.
- Validasi semua input dari pengguna.
- Gunakan validasi yang sesuai dengan tipe data.
- Gunakan unique validation untuk data yang harus unik.
- Pastikan validasi tidak menyebabkan kebocoran informasi sensitif.

## 7. Service Layer

Gunakan Service Layer untuk business logic yang kompleks atau digunakan oleh beberapa controller.

Contoh:

```php
<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            return Product::create($data);
        });
    }
}
```

### Ketentuan

- Service harus memiliki tanggung jawab yang jelas.
- Hindari service dengan terlalu banyak tanggung jawab.
- Gunakan dependency injection.
- Jangan membuat service untuk operasi CRUD sederhana yang tidak membutuhkan abstraksi tambahan.

## 8. API Development Standards

Jika proyek menyediakan REST API, ikuti aturan berikut.

### API Conventions

- Gunakan RESTful endpoints.
- Gunakan HTTP methods sesuai fungsinya.
- Gunakan API Resources untuk response.
- Gunakan Form Request untuk validasi.
- Gunakan pagination untuk data berjumlah besar.
- Gunakan authentication middleware untuk endpoint privat.
- Gunakan authorization melalui Policy atau Gate.

Contoh endpoint:

| Method | Endpoint                  | Description        |
| ------ | ------------------------- | ------------------ |
| GET    | `/api/products`           | Get all products   |
| GET    | `/api/products/{product}` | Get product detail |
| POST   | `/api/products`           | Create product     |
| PUT    | `/api/products/{product}` | Replace product    |
| PATCH  | `/api/products/{product}` | Update product     |
| DELETE | `/api/products/{product}` | Delete product     |

### API Response

Gunakan format response yang konsisten.

Contoh sukses:

```json
{
    "success": true,
    "message": "Product retrieved successfully",
    "data": {
        "id": 1,
        "name": "Product A",
        "price": "150000.00"
    }
}
```

Contoh error:

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

Jangan mengubah format response yang sudah digunakan proyek tanpa alasan dan pertimbangan kompatibilitas.

## 9. Security Guidelines

Keamanan merupakan prioritas utama.

Wajib mengikuti ketentuan berikut:

- Jangan menyimpan password dalam bentuk plain text.
- Gunakan Laravel Hash untuk password.
- Gunakan CSRF protection untuk web forms.
- Gunakan authorization pada operasi sensitif.
- Hindari SQL injection dengan Eloquent atau parameter binding.
- Hindari mass assignment vulnerability.
- Jangan menampilkan stack trace kepada pengguna production.
- Jangan mengekspos API keys, password, atau secret lainnya.
- Validasi dan sanitasi input sesuai konteks penggunaannya.
- Jangan menonaktifkan middleware keamanan tanpa alasan yang valid.
- Gunakan rate limiting untuk endpoint yang sensitif.
- Jangan mencatat password, token, atau informasi sensitif ke log.

## 10. Performance Guidelines

Optimalkan aplikasi dengan memperhatikan:

- Gunakan eager loading untuk mencegah N+1 queries.
- Gunakan pagination untuk dataset besar.
- Hindari mengambil seluruh data jika hanya membutuhkan beberapa kolom.
- Gunakan database indexing secara tepat.
- Gunakan cache untuk data yang sering diakses dan jarang berubah.
- Gunakan queue untuk proses berat atau asynchronous.
- Hindari query database di dalam loop jika bisa dioptimalkan.
- Gunakan `chunk()` atau `lazy()` untuk pemrosesan dataset besar.
- Hindari penggunaan `SELECT *` ketika hanya beberapa kolom yang dibutuhkan.

## 11. Error Handling and Logging

- Gunakan exception handling Laravel.
- Jangan menyembunyikan error secara diam-diam.
- Gunakan Log facade untuk mencatat error penting.
- Jangan mencatat informasi sensitif.
- Berikan pesan error yang mudah dipahami pengguna.
- Gunakan exception khusus jika memang dibutuhkan.
- Jangan menggunakan `dd()` atau `dump()` pada kode production.

Contoh:

```php
use Illuminate\Support\Facades\Log;

Log::error('Failed to process product', [
    'product_id' => $productId,
]);
```

## 12. Testing Guidelines

Setiap fitur penting harus memiliki automated tests.

Gunakan PHPUnit atau Pest sesuai konfigurasi proyek.

### Prioritas Pengujian

1. Authentication.
2. Authorization.
3. Validation.
4. CRUD operations.
5. Database relationships.
6. Business logic.
7. API responses.
8. Error handling.

Contoh menjalankan test:

```bash
php artisan test
```

### Ketentuan

- Gunakan database testing terpisah.
- Gunakan `RefreshDatabase` jika sesuai.
- Jangan menjalankan test yang berpotensi menghapus database development atau production.
- Pastikan test tidak bergantung pada urutan eksekusi.
- Tambahkan test untuk setiap bug yang diperbaiki jika memungkinkan.

## 13. Code Quality

Sebelum menyelesaikan pekerjaan:

- Pastikan tidak ada syntax error.
- Pastikan namespace dan import benar.
- Hapus kode yang tidak digunakan.
- Hindari duplikasi kode.
- Gunakan type hints dan return types.
- Gunakan strict comparison (`===`) jika sesuai.
- Gunakan early return untuk mengurangi nested conditions.
- Ikuti formatting yang digunakan proyek.
- Jangan melakukan refactoring besar di luar ruang lingkup pekerjaan.

Jika proyek menggunakan Laravel Pint, jalankan:

```bash
./vendor/bin/pint
```

Jalankan formatter hanya pada file yang relevan jika perubahan formatting global tidak diminta.

## 14. Artisan Commands

Gunakan Artisan untuk membuat komponen Laravel.

Contoh:

```bash
php artisan make:model Product -m
php artisan make:controller ProductController
php artisan make:request StoreProductRequest
php artisan make:resource ProductResource
php artisan make:migration create_products_table
php artisan make:seeder ProductSeeder
php artisan make:test ProductTest
```

### Catatan

- `make:service` bukan command bawaan Laravel.
- Gunakan pembuatan file manual atau generator yang memang tersedia pada proyek.
- Jangan membuat ulang komponen yang sudah tersedia.

## 15. Git Guidelines

- Gunakan commit message yang jelas.
- Jangan commit `.env`.
- Jangan commit credentials atau secret.
- Jangan memasukkan file hasil build yang tidak diperlukan.
- Jangan menghapus perubahan milik developer tanpa izin.
- Jangan melakukan force push tanpa persetujuan.
- Jangan melakukan reset atau clean yang dapat menghilangkan pekerjaan pengguna.

Contoh commit message:

```text
feat: add product management
fix: resolve product validation issue
refactor: simplify product service
test: add product feature tests
docs: update API documentation
```

## 16. AI Agent Working Rules

### Sebelum Melakukan Perubahan Kode

- Baca `AGENTS.md`.
- Periksa struktur proyek.
- Periksa `composer.json` dan versi dependensi.
- Periksa implementasi fitur yang sudah ada.
- Identifikasi file yang berkaitan dengan tugas.
- Pahami pola coding yang sudah digunakan.
- Hindari mengasumsikan bahwa semua fitur Laravel tersedia tanpa memeriksa dependensi.

### Saat Mengerjakan Tugas

- Fokus pada kebutuhan yang diminta.
- Gunakan pendekatan yang paling sederhana dan maintainable.
- Gunakan komponen Laravel yang sesuai.
- Jangan mengubah file yang tidak berkaitan.
- Jangan menghapus fitur yang sudah ada.
- Jangan membuat dependency baru tanpa alasan.
- Jangan mengubah konfigurasi environment tanpa persetujuan.
- Jangan menjalankan operasi destruktif terhadap database.
- Jika terdapat ketidakjelasan arsitektur, periksa implementasi yang ada sebelum mengambil keputusan.

### Setelah Menyelesaikan Tugas

- Periksa seluruh perubahan.
- Jalankan test yang relevan.
- Jalankan formatter jika tersedia.
- Pastikan tidak ada syntax error.
- Laporkan file yang dibuat atau diubah.
- Laporkan test yang dijalankan dan hasilnya.
- Jelaskan jika ada bagian yang belum dapat diverifikasi.

## 17. Important Restrictions

AI Agent DILARANG:

- Mengubah atau menghapus data production.
- Menjalankan `php artisan migrate:fresh` tanpa persetujuan.
- Menjalankan `php artisan db:wipe` tanpa persetujuan.
- Menghapus data apa pun pada database yang sedang digunakan di `.env`, termasuk `DELETE`, `TRUNCATE`, `DROP`, atau reset data.
- Mengubah konfigurasi database production.
- Menghapus migration lama yang sudah digunakan.
- Menghapus file atau fitur tanpa alasan yang jelas.
- Mengganti teknologi utama proyek tanpa persetujuan.
- Menambahkan package yang tidak diperlukan.
- Menyimpan credentials atau secrets dalam source code.
- Mengabaikan error atau test failure.

## 18. Definition of Done

Sebuah pekerjaan dinyatakan selesai apabila:

- Fitur sesuai dengan permintaan.
- Kode mengikuti standar Laravel 12.
- Struktur database menggunakan migration jika diperlukan.
- Validasi dan keamanan sudah diperhatikan.
- Tidak ada perubahan tidak relevan.
- Automated tests relevan berhasil dijalankan.
- Tidak ada syntax error.
- Tidak ada credentials atau secrets yang terekspos.
- Hasil pekerjaan telah dijelaskan dengan jelas.

## Final Instruction

Selalu prioritaskan kode yang aman, sederhana, konsisten, dan mudah dipelihara.

Ikuti arsitektur dan konvensi proyek yang sudah ada. Jangan membuat asumsi atau melakukan perubahan destruktif tanpa persetujuan.

Jika terdapat beberapa pendekatan implementasi, pilih pendekatan yang paling sesuai dengan standar Laravel 12 dan kebutuhan proyek.

Instruksi dalam dokumen ini berlaku untuk seluruh pekerjaan AI Coding Agent di dalam proyek ini.
