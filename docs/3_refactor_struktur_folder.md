# Struktur Folder `app/` dan `resources/views/`

## 1. `app/`

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   └── TransactionController.php
│   │   │
│   │   └── SuperAdmin/
│   │       ├── DashboardController.php
│   │       └── TransactionController.php
│   │
│   ├── Requests/
│   │   └── Transaction/
│   │       ├── StoreTransactionRequest.php
│   │       └── UpdateTransactionRequest.php
│   │
│   └── Middleware/
│
├── Models/
│   ├── User.php
│   └── Transaction.php
│
├── Policies/
│   └── TransactionPolicy.php
│
├── Services/
│   ├── TransactionService.php
│   └── TransactionExportService.php
│
└── Providers/
    └── AppServiceProvider.php
```

---

## `app/Http/Controllers/`

### `Admin/DashboardController.php`

Khusus halaman dashboard Admin.

Tanggung jawab:

* Mengambil transaksi milik Admin.
* Filter transaksi berdasarkan bulan.
* Menghitung/menyiapkan statistik dashboard.
* Mengirim data ke `admin/dashboard.blade.php`.

---

### `Admin/TransactionController.php`

Khusus CRUD transaksi dari sisi Admin.

```text
index()    → daftar transaksi
create()   → form tambah
store()    → simpan transaksi
edit()     → form edit
update()   → update transaksi
destroy()  → hapus transaksi
```

Controller hanya mengatur request dan response.

---

### `SuperAdmin/DashboardController.php`

Khusus dashboard Superadmin.

Tanggung jawab:

* Mengambil seluruh transaksi.
* Filter bulan.
* Statistik seluruh transaksi.
* Mengirim data ke view Superadmin.

---

### `SuperAdmin/TransactionController.php`

Khusus pengelolaan transaksi dari sisi Superadmin.

Digunakan ketika Superadmin membutuhkan operasi transaksi yang berbeda dari Admin.

---

# `app/Http/Requests/`

Khusus **validasi request**.

```text
Requests/
└── Transaction/
    ├── StoreTransactionRequest.php
    └── UpdateTransactionRequest.php
```

### `StoreTransactionRequest.php`

Validasi ketika membuat transaksi.

Contoh:

```text
nama
tanggal_main
jenis_transfer
tanggal_transfer
nominal
jam_mulai
jam_selesai
catatan
gambar_bukti
```

### `UpdateTransactionRequest.php`

Validasi ketika mengubah transaksi.

---

# `app/Models/`

Khusus **data dan database behavior**.

### `User.php`

Menangani:

* Data user.
* Role.
* Relationship user dengan transaksi.

Contoh:

```text
User
 └── hasMany Transaction
```

### `Transaction.php`

Menangani:

* Data transaksi.
* `$fillable`.
* `$casts`.
* Relationship.
* Query scope.
* Accessor/mutator jika diperlukan.

Contoh scope:

```text
Transaction::forUser($user)
```

---

# `app/Policies/`

Khusus **authorization**.

### `TransactionPolicy.php`

Menentukan siapa yang boleh:

```text
view
create
update
delete
```

terhadap Transaction.

Contoh aturan:

```text
Superadmin
→ seluruh transaksi

Admin
→ transaksi miliknya sendiri
```

Jadi logic seperti:

```php
abort_unless(...)
```

tidak lagi ditaruh di Controller.

---

# `app/Services/`

Khusus **business logic**.

### `TransactionService.php`

Menangani proses transaksi:

```text
Create
Update
Delete
Calculate Duration
Upload Evidence
Delete Old Evidence
```

Contoh flow:

```text
Controller
    ↓
TransactionService
    ↓
Calculate Duration
    ↓
Upload File
    ↓
Transaction::create()
```

### `TransactionExportService.php`

Khusus export transaksi:

```text
Ambil transaksi
    ↓
Filter berdasarkan user/role
    ↓
Format CSV
    ↓
Download
```

Dengan begitu kode CSV tidak berada di Controller.

---

# `app/Providers/`

### `AppServiceProvider.php`

Tempat konfigurasi dan registrasi service aplikasi.

Untuk kebutuhan Ledger saat ini kemungkinan tidak banyak perubahan.

---

# 2. `resources/views/`

Struktur view:

```text
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    │
    ├── components/
    │   ├── alert.blade.php
    │   ├── modal.blade.php
    │   ├── transaction-form.blade.php
    │   └── transaction-table.blade.php
    │
    ├── auth/
    │   └── ...
    │
    └── ledger/
        ├── home.blade.php
        │
        ├── admin/
        │   ├── dashboard.blade.php
        │   └── transactions/
        │       ├── index.blade.php
        │       ├── create.blade.php
        │       └── edit.blade.php
        │
        └── superadmin/
            ├── dashboard.blade.php
            └── transactions/
                └── index.blade.php
```

---

# `views/layouts/`

Template utama aplikasi.

### `app.blade.php`

Berisi bagian yang digunakan banyak halaman:

```text
HTML
Head
CSS
Navbar
Main Content
Footer
JavaScript
```

Halaman lain menggunakan:

```blade
@extends('layouts.app')
```

---

# `views/components/`

Komponen Blade yang digunakan berulang.

### `alert.blade.php`

Untuk:

```text
Success
Error
Warning
Info
```

### `modal.blade.php`

Komponen modal yang digunakan berulang.

### `transaction-form.blade.php`

Form transaksi.

Digunakan oleh:

```text
create.blade.php
edit.blade.php
```

Daripada membuat form dua kali.

### `transaction-table.blade.php`

Tabel transaksi.

Bisa digunakan oleh:

```text
Admin
Superadmin
```

jika struktur tabelnya sama.

---

# `views/auth/`

Semua halaman authentication.

Misalnya:

```text
auth/
├── login.blade.php
├── register.blade.php
└── ...
```

Struktur sebenarnya menyesuaikan authentication package yang digunakan.

---

# `views/ledger/`

Semua halaman yang berkaitan dengan Ledger.

---

## `ledger/home.blade.php`

Halaman utama/home Ledger.

---

# `ledger/admin/`

Semua UI khusus Admin.

```text
admin/
├── dashboard.blade.php
└── transactions/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### `dashboard.blade.php`

Menampilkan:

```text
Total transaksi
Total nominal
Filter bulan
Ringkasan transaksi
```

### `transactions/index.blade.php`

Menampilkan daftar transaksi Admin.

### `transactions/create.blade.php`

Form membuat transaksi.

### `transactions/edit.blade.php`

Form mengubah transaksi.

---

# `ledger/superadmin/`

Semua UI khusus Superadmin.

```text
superadmin/
├── dashboard.blade.php
└── transactions/
    └── index.blade.php
```

### `dashboard.blade.php`

Dashboard seluruh transaksi.

### `transactions/index.blade.php`

Daftar transaksi yang dapat diakses Superadmin.

---

# Hubungan `app` dengan `views`

Untuk fitur transaksi:

```text
                    REQUEST
                       │
                       ▼
              ┌────────────────┐
              │    Controller  │
              └───────┬────────┘
                      │
          ┌───────────┼───────────┐
          ▼           ▼           ▼
       Request      Policy      Service
      Validation   Permission  Business Logic
                                  │
                                  ▼
                               Model
                                  │
                                  ▼
                              Database
                                  │
                                  ▼
                              Controller
                                  │
                                  ▼
                                View
```

Contoh konkret:

```text
Admin membuka /admin/transactions
                ↓
Admin/TransactionController
                ↓
Transaction Model
                ↓
Database
                ↓
ledger/admin/transactions/index.blade.php
                ↓
Browser
```

Untuk create:

```text
create.blade.php
      ↓
POST
      ↓
StoreTransactionRequest
      ↓
TransactionController
      ↓
TransactionService
      ↓
Transaction Model
      ↓
Database
      ↓
redirect
```

---

# Target Akhir

Jadi dua bagian utama project kita akan memiliki pembagian seperti ini:

```text
app/
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── SuperAdmin/
│   │
│   ├── Requests/
│   │   └── Transaction/
│   │
│   └── Middleware/
│
├── Models/
│   ├── User.php
│   └── Transaction.php
│
├── Policies/
│   └── TransactionPolicy.php
│
├── Services/
│   ├── TransactionService.php
│   └── TransactionExportService.php
│
└── Providers/


resources/views/
│
├── layouts/
│   └── app.blade.php
│
├── components/
│   ├── alert.blade.php
│   ├── modal.blade.php
│   ├── transaction-form.blade.php
│   └── transaction-table.blade.php
│
├── auth/
│
└── ledger/
    ├── home.blade.php
    │
    ├── admin/
    │   ├── dashboard.blade.php
    │   └── transactions/
    │       ├── index.blade.php
    │       ├── create.blade.php
    │       └── edit.blade.php
    │
    └── superadmin/
        ├── dashboard.blade.php
        └── transactions/
            └── index.blade.php
```

**Intinya:**

* `Controller` → mengatur alur.
* `Request` → validasi.
* `Policy` → izin.
* `Service` → proses bisnis.
* `Model` → data/database.
* `View` → tampilan.
* `Component` → UI yang dipakai berulang.

Dengan struktur ini, `LedgerController.php` yang sekarang bisa kita pecah dengan jelas tanpa membuat arsitektur yang terlalu rumit.
