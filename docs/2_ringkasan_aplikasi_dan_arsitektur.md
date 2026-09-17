# Ringkasan Aplikasi dan Arsitektur Ledger

## 1. Tema Aplikasi

**Ledger** adalah aplikasi internal untuk pencatatan uang masuk dan pembayaran lapangan.

Aplikasi digunakan untuk:

- Mencatat transaksi pembayaran.
- Menyimpan bukti transfer.
- Menghitung durasi atau okupansi lapangan.
- Memantau pendapatan dan transaksi.
- Menyediakan rekap bulanan.
- Membuat laporan transaksi.

Aplikasi saat ini menggunakan Laravel sebagai backend, Blade sebagai template halaman, MySQL sebagai database, dan Chart.js untuk grafik dashboard.

## 2. Role Pengguna

### Admin

Admin bertanggung jawab atas transaksi yang dibuatnya sendiri.

Fitur Admin:

- Login dengan email dan password.
- Input transaksi baru.
- Melihat transaksi milik sendiri.
- Mengedit transaksi milik sendiri.
- Menghapus transaksi milik sendiri.
- Mengunggah bukti transfer.
- Melihat dashboard pribadi.
- Melihat rekap transaksi pribadi.
- Mengekspor transaksi pribadi.
- Logout.

### Super Admin

Super Admin bertanggung jawab atas pengawasan seluruh transaksi.

Fitur Super Admin:

- Login dengan email dan password.
- Melihat seluruh transaksi.
- Input transaksi melalui halaman Super Admin.
- Mengedit transaksi apa pun.
- Menghapus transaksi apa pun.
- Melihat seluruh dashboard.
- Melihat grafik pendapatan dan okupansi.
- Melihat rekap per bulan.
- Melihat bukti transfer.
- Mengekspor seluruh transaksi.
- Logout.

Super Admin tidak seharusnya masuk sebagai role Admin. Super Admin tetap menggunakan role Super Admin, tetapi memiliki hak akses yang lebih luas.

## 3. Fitur Transaksi

Field transaksi yang tersedia:

- Nama pelanggan atau komunitas.
- Tanggal main.
- Jenis pembayaran: QRIS, Transfer, atau Cash.
- Tanggal pembayaran.
- Nominal pembayaran.
- Jam mulai.
- Jam selesai.
- Durasi atau okupansi otomatis.
- Catatan tambahan.
- Bukti transfer berupa gambar.

Operasi transaksi:

- Tambah transaksi.
- Tampilkan transaksi.
- Edit transaksi.
- Hapus transaksi dengan soft delete.
- Ganti bukti transfer.
- Buka bukti transfer melalui storage publik.
- Simpan user pembuat transaksi melalui `created_by`.
- Simpan user terakhir yang mengubah transaksi melalui `updated_by`.

## 4. Dashboard dan Rekap

### Dashboard Admin

Dashboard Admin menampilkan data milik Admin yang sedang login:

- Total transaksi.
- Total pendapatan.
- Total okupansi.
- Rata-rata nominal transaksi.
- Rekap per bulan.
- Grafik pendapatan.
- Grafik okupansi.
- Ekspor laporan transaksi pribadi.

### Dashboard Super Admin

Dashboard Super Admin menampilkan seluruh data transaksi:

- Total seluruh transaksi.
- Total seluruh pendapatan.
- Total seluruh okupansi.
- Rata-rata nominal seluruh transaksi.
- Grafik tren pendapatan.
- Grafik tren okupansi.
- Rekap transaksi berdasarkan bulan.
- Detail seluruh transaksi.
- Link untuk melihat bukti transfer.
- Ekspor seluruh data transaksi.

## 5. Format Laporan

Format kolom laporan saat ini:

| Kolom | Isi |
| --- | --- |
| ATAS NAMA / KOMUNITAS | Nama pelanggan atau komunitas |
| TGL/BULAN MAIN | Tanggal main |
| JAM MAIN | Jam mulai dan jam selesai |
| PEMBAYARAN | QRIS, Transfer, atau Cash |
| TGL PEMBAYARAN | Tanggal pembayaran |
| NOMINAL | Nominal pembayaran |
| NOTE | Catatan transaksi |

Ekspor Laravel saat ini menggunakan format CSV.

Format Excel dengan judul di baris pertama, header di baris kelima, dan warna header hijau berasal dari versi frontend lama dan belum sepenuhnya dipindahkan ke backend Laravel.

## 6. Struktur Arsitektur Saat Ini

Alur utama aplikasi:

```text
Browser
  |
  v
Route Laravel
  |
  v
Middleware auth dan role
  |
  v
LedgerController
  |
  +--> Transaction model
  |
  +--> MySQL database
  |
  +--> Blade view
  |
  +--> Laravel Storage untuk bukti transfer
```

Komponen utama:

- `routes/web.php`: route halaman, login, transaksi, dashboard, dan ekspor.
- `app/Http/Controllers/LedgerController.php`: proses transaksi, dashboard, dan laporan.
- `app/Http/Controllers/Auth/LedgerLoginController.php`: proses login dan logout.
- `app/Http/Middleware/Role.php`: pembatasan akses berdasarkan role.
- `app/Models/Transaction.php`: model transaksi dan soft delete.
- `resources/views/ledger/`: halaman Blade.
- `database/migrations/`: struktur tabel database.
- `storage/app/public/bukti/`: penyimpanan file bukti transfer.
- `public/storage`: symbolic link agar file bukti dapat diakses browser.

## 7. Pembagian Route

### Route Admin

```text
GET    /admin
GET    /admin/dashboard
GET    /admin/export
POST   /admin/transactions
GET    /admin/transactions/{transaction}/edit
PUT    /admin/transactions/{transaction}
DELETE /admin/transactions/{transaction}
```

Route tersebut hanya boleh diakses oleh user dengan role `admin`.

### Route Super Admin

```text
GET    /superadmin
GET    /superadmin/transactions/create
POST   /superadmin/transactions
GET    /superadmin/transactions/{transaction}/edit
PUT    /superadmin/transactions/{transaction}
DELETE /superadmin/transactions/{transaction}
GET    /superadmin/export
```

Route tersebut hanya boleh diakses oleh user dengan role `superadmin`.

## 8. Masalah Arsitektur yang Masih Perlu Diperhatikan

### 8.1 View input masih dipakai bersama

Saat ini halaman input Admin dan Super Admin masih menggunakan `admin.blade.php` dengan kondisi berdasarkan role.

Dampaknya:

- Konteks Admin dan Super Admin tercampur.
- Menu harus menggunakan banyak kondisi Blade.
- Perubahan fitur satu role dapat memengaruhi role lain.
- Nama file tidak menggambarkan halaman Super Admin.

Struktur yang lebih bersih:

```text
resources/views/ledger/admin.blade.php
resources/views/ledger/superadmin-input.blade.php
resources/views/ledger/admin-dashboard.blade.php
resources/views/ledger/superadmin.blade.php
```

### 8.2 Controller terlalu besar

`LedgerController` saat ini menangani:

- Input transaksi.
- Edit transaksi.
- Hapus transaksi.
- Upload bukti.
- Dashboard Admin.
- Dashboard Super Admin.
- Ekspor laporan.
- Perhitungan durasi.

Struktur yang lebih mudah dirawat:

```text
TransactionController
DashboardController
ReportController
```

Logika validasi dan upload juga dapat dipindahkan ke Form Request dan service khusus.

### 8.3 API lama masih perlu diamankan atau dihapus

Jika aplikasi sudah sepenuhnya menggunakan Blade server-side, API transaksi lama tidak diperlukan untuk halaman utama.

Pilihan yang disarankan:

- Hapus API lama jika tidak digunakan.
- Atau tambahkan middleware autentikasi jika API tetap diperlukan.

API yang terbuka tanpa autentikasi dapat memungkinkan pembacaan, penambahan, perubahan, atau penghapusan transaksi tanpa login.

### 8.4 Audit log belum lengkap

Kolom `created_by` dan `updated_by` sudah membantu mencatat pemilik dan pengubah transaksi, tetapi belum mencatat:

- IP address.
- Aktivitas login dan logout.
- Nilai data sebelum perubahan.
- Nilai data sesudah perubahan.
- Alasan penghapusan.
- Aktivitas approval atau penolakan.

### 8.5 Approval transaksi belum tersedia

Dokumen RBAC menyebutkan approval, tetapi transaksi belum memiliki status workflow.

Status yang dapat ditambahkan:

```text
draft
pending
approved
rejected
```

### 8.6 User management belum tersedia

Super Admin belum memiliki modul untuk:

- Membuat user Admin.
- Mengubah user Admin.
- Menonaktifkan user Admin.
- Reset password.
- Mengubah role.

### 8.7 Ekspor Excel belum dipindahkan sepenuhnya

Sistem saat ini menghasilkan CSV. Untuk laporan resmi seperti format spreadsheet lama, sebaiknya gunakan PhpSpreadsheet atau Laravel Excel agar dapat mendukung:

- File `.xlsx`.
- Judul laporan.
- Header mulai baris tertentu.
- Warna header hijau.
- Merge cell.
- Lebar kolom.
- Format mata uang dan tanggal.

### 8.8 Kode frontend lama masih ada

Repository masih memiliki file legacy seperti:

- `index.html`.
- Folder `js/`.
- Folder `css/`.
- `public/js/`.
- Folder `laravel-scaffold/`.

File tersebut dapat membingungkan karena sebagian tidak lagi dipakai oleh halaman Blade utama. Setelah migrasi dan backup dipastikan aman, file yang tidak digunakan dapat dipindahkan atau dihapus.

## 9. Rekomendasi Prioritas

### Prioritas 1: Keamanan

- Pastikan API memakai middleware autentikasi atau hapus jika tidak dipakai.
- Pastikan route Admin dan Super Admin memakai middleware role.
- Pastikan Admin hanya dapat mengakses transaksi miliknya.
- Pindahkan konfigurasi sensitif ke `.env`.
- Tambahkan proteksi CSRF pada seluruh form web.

### Prioritas 2: Kejelasan Role

- Pisahkan view input Admin dan Super Admin.
- Jangan mengarahkan Super Admin ke route `/admin`.
- Gunakan menu dan route sesuai role aktif.
- Tambahkan policy Laravel untuk transaksi.

### Prioritas 3: Kualitas Data

- Tambahkan status approval transaksi.
- Pertahankan soft delete.
- Tambahkan foreign key untuk `created_by` dan `updated_by`.
- Lengkapi audit log.

### Prioritas 4: Pelaporan

- Migrasikan Export Excel ke Excel `.xlsx` jika diperlukan.
- Tambahkan filter tanggal dan bulan.
- Tambahkan laporan pendapatan per metode pembayaran.
- Tambahkan laporan transaksi per Admin.

### Prioritas 5: Kebersihan Repository

- Hapus atau pindahkan folder `laravel-scaffold`.
- Hapus file frontend lama setelah dipastikan tidak dipakai.
- Pisahkan controller dan service berdasarkan tanggung jawab.
- Tambahkan test untuk role, transaksi, upload, edit, hapus, dan ekspor.

## 10. Kesimpulan

Aplikasi Ledger sudah memiliki fondasi utama untuk pencatatan transaksi dan laporan:

- Laravel.
- Blade.
- MySQL.
- Login berbasis role.
- CRUD transaksi.
- Upload bukti transfer.
- Soft delete.
- Dashboard Admin dan Super Admin.
- Grafik pendapatan dan okupansi.
- Rekap bulanan.
- Ekspor laporan.

Arsitektur saat ini sudah dapat berjalan, tetapi masih berada dalam tahap penyempurnaan migrasi. Perbaikan paling penting adalah memisahkan view Admin dan Super Admin, mengamankan atau menghapus API lama, menambahkan policy dan audit log, serta memindahkan ekspor ke format Excel jika laporan resmi membutuhkannya.
