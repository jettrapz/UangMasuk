# **Dokumen Spesifikasi Akses Pengguna (RBAC) & Authentication Flow**

Dokumen ini mengatur alur autentikasi dan spesifikasi pembagian hak akses pengguna (Role-Based Access Control) untuk **Sistem Internal Pencatatan Uang Masuk Perusahaan**.

## **1\. Alur Registrasi & Autentikasi Pengguna**

---

Untuk menjaga keamanan transaksi keuangan dan mencegah akses tidak sah, sistem ini menerapkan ketentuan autentikasi sebagai berikut:

* **Tanpa Registrasi Terbuka:** Website tidak menyediakan formulir pendaftaran (register) mandiri untuk umum.  
* **Autentikasi Berbasis Login:** Pengguna hanya dapat masuk ke sistem melalui halaman Login yang aman.  
* **Pemberian Akun Terpusat:** Akun baru dibuat secara internal oleh *Super Admin* atau Tim IT. Karyawan akan menerima kredensial sementara dan diwajibkan mengganti kata sandi pada saat login pertama kali.  
* **Integrasi Single Sign-On (SSO):** Opsional menggunakan SSO (seperti Google Workspace atau Microsoft Azure AD) yang terikat langsung pada domain email resmi perusahaan.

## **2\. Deskripsi Peran Pengguna (User Roles)**

### ---

**A. Admin (Operasional Keuangan)**

Peran ini ditujukan untuk staf operasional yang bertugas melakukan pencatatan transaksi uang masuk sehari-hari.

* Menginput data transaksi keuangan masuk dan mengunggah dokumen/bukti transfer.  
* Melihat daftar transaksi harian dan bulanan yang relevan dengan tugasnya.  
* Mengedit draft transaksi milik sendiri sebelum dikunci atau disetujui.  
* Melihat ringkasan indikator utama pada dashboard operasional.

### **B. Super Admin (Pengawas & Administrator)**

Peran ini memiliki hak penuh atas manajemen sistem, validasi data, serta pengawasan audit keuangan secara terpusat.

* Mengelola akun pengguna (membuat, memperbarui, dan menonaktifkan akun Admin).  
* Melakukan verifikasi, persetujuan (approval), atau penolakan transaksi yang dimasukkan oleh Admin.  
* Mengakses laporan analytics menyeluruh dan melakukan ekspor data (Excel/PDF).  
* Memiliki wewenang untuk melakukan koreksi data transaksi dan memantau riwayat aktivitas (Audit Log).

## **3\. Matriks Hak Akses (Role-Based Access Control)**

---

| Fitur / Modul | Fungsi Spesifik | Admin | Super Admin   |
| :---- | :---- | :---- | :---- |
| **Autentikasi & User Management** | Login ke Sistem | **Ya** | **Ya** |
|  | Ubah Kata Sandi Mandiri | **Ya** | **Ya** |
|  | Tambah / Edit / Nonaktifkan User Admin | **Tidak Access** | **Ya** |
| **Dashboard** | Lihat Ringkasan Transaksi Harian | **Ya** | **Ya** |
|  | Lihat Analytics & Laporan Akumulasi | **Tidak Access** | **Ya** |
|  | Ekspor Laporan Keuangan (Excel/PDF) | **Tidak Access** | **Ya** |
| **Pencatatan Uang Masuk** | Input Data Transaksi Baru & Lampiran | **Ya** | **Ya** |
|  | Edit Transaksi Draft (Milik Sendiri) | **Ya** | **Ya** |
|  | Edit / Hapus Semua Transaksi Terverifikasi | **Tidak Access** | **Ya** |
|  | Approval / Verifikasi Transaksi Masuk | **Tidak Access** | **Ya** |
| **Audit & System Log** | Melihat Audit Log Aktivitas Pengguna | **Tidak Access** | **Ya** |

## **4\. Prinsip Keamanan & Rekomendasi Teknis**

* ---

  **Penerapan Soft Delete:** Penghapusan data transaksi tidak menggunakan *hard delete* untuk mencegah kehilangan jejak audit. Transaksi yang dihapus/dibatalkan hanya ditandai statusnya.  
* **Audit Trail Sistem:** Setiap aktivitas penambahan, pengubahan, dan penghapusan data wajib dicatat secara otomatis oleh sistem (mencakup ID Pengguna, timestamp, dan IP address).  
* **Pemisahan Tugas (Separation of Duties):** Petugas yang menginput data (Admin) sebaiknya berbeda dengan petugas yang menyetujui data transaksi (Super Admin).