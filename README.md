# BEM FIKOM E-Sign
### Sistem Pengesahan Surat Resmi, Tanda Tangan Digital Kriptografis & Verifikasi Integritas Dokumen
**Badan Eksekutif Mahasiswa Fakultas Ilmu Komputer (BEM FIKOM)**

---

## 1. Ringkasan Sistem & Arsitektur
**BEM FIKOM E-Sign** adalah platform aplikasi web full-stack berbasis **Laravel 12 (PHP 8.2+)** dan **MySQL** yang dirancang khusus untuk memodernisasi tata kelola persuratan organisasi mahasiswa. Sistem ini menyediakan mekanisme pengesahan digital nir-kertas (*paperless*), penjaminan integritas berkas, serta pencegahan pemalsuan tanda tangan dan manipulasi daftar penerima surat ("titip nama").

### Fitur Kunci:
- **Logo & Cap Resmi BEM FIKOM:** Menggunakan logo resmi BEM Fakultas Ilmu Komputer pada identitas sistem, kop surat PDF, dan stempel cap tinta biru otentik pada blok pengesahan.
- **Wajib Login untuk Seluruh Layanan (Akses Terproteksi):** Pemeriksaan surat, pengunggahan draf, uji integritas PDF (SHA-256), scan QR Code, dan pengesahan mewajibkan pengguna untuk login terlebih dahulu melalui portal login terpadu.
- **Kriptografi PDF Digital Signature (X.509 / PKCS#7):** Setiap surat yang disahkan disematkan sertifikat digital resmi BEM FIKOM menggunakan `tecnickcom/tcpdf` (`setSignature()`). Dokumen yang dibuka pada *Adobe Acrobat Reader* akan menampilkan status validitas tanda tangan digital secara sah.
- **Blok Pengesahan Dinamis & QR Code Mandiri:** Menggunakan `setasign/fpdi` untuk mengimpor PDF draf tanpa merusak tata letak asli, menempelkan Cap resmi BEM transparan, goresan tanda tangan digital, teks legalitas mikro, serta **2D QR Code** bawaan TCPDF (`write2DBarcode`) yang mengarah ke URL verifikasi permanen.
- **Uji Integritas File PDF Anti-Edit (SHA-256):** Melakukan ekstraksi sidik jari kriptografi (`hash_file('sha256', ...)`) pada file PDF final. Pihak kampus cukup mengunggah (drag-and-drop) file PDF fisik untuk menguji apakah file tersebut 100% identik dengan arsip database resmi atau telah diubah/diedit.
- **Verifikasi Otoritas 6-Digit PIN:** Ketua dan Wakil Ketua BEM wajib mengonfirmasi 6-digit PIN pengesahan sebelum tanda tangan dan cap elektronik dapat dibubuhkan atau dicabut.
- **Pencabutan Pengesahan (Revoke Mechanism):** Pimpinan BEM dapat mencabut surat resmi yang telah disahkan jika ditemukan pelanggaran. Status pada scan QR Code seketika berubah menjadi banner merah peringatan pencabutan beserta alasannya.
- **Isolasi Aset Sensitif (Private Storage):** File Cap BEM PNG, tanda tangan PNG, draf PDF, file signed PDF, serta private key `.key` dan sertifikat `.crt` disimpan di `storage/app/private/` sehingga tidak pernah dapat diakses langsung via URL publik.
- **Daftar Mahasiswa Terlampir (Anti-Titip Nama):** Form dinamis berbasis Alpine.js untuk merekam Nama dan NIM seluruh mahasiswa yang sah menggunakan surat.

---

## 2. Desain Antarmuka (Deep Navy & Crisp White — Anti-AI Slop)
Antarmuka dibangun dengan gaya portal institusional modern, bersih, dan berwibawa:
- **Primary Dark:** `#0F172A` (Deep Navy)
- **Primary Accent:** `#1E3A8A` (Royal Navy)
- **Background:** `#F8FAFC` (Off-white bersih)
- **Surface / Card:** `#FFFFFF` (Border tegas `1px solid #E2E8F0`)
- **Status Emerald:** `#047857` (Sah / Valid)
- **Status Amber:** `#B45309` (Menunggu Pengesahan)
- **Status Crimson:** `#B91C1C` (Ditolak / Dicabut / File Dimodifikasi)
- **Tipografi:** *Plus Jakarta Sans* untuk antarmuka formal dan *JetBrains Mono* untuk nomor surat, NIM, UUID token, dan hash SHA-256.

---

## 3. Akun Pengguna Bawaan (Demo Credentials)

Aplikasi telah disiapkan dengan 3 akun pengurus siap pakai:

| Role | Nama Pengurus | Jabatan | Email | Kata Sandi | PIN Pengesahan |
|---|---|---|---|---|---|
| **Ketua BEM** | M. Fikri Al-Habsyi | Ketua BEM FIKOM | `ketua@bemfikom.ac.id` | `password123` | `123456` |
| **Wakil Ketua BEM** | Dina Permata Sari | Wakil Ketua BEM FIKOM | `wakil@bemfikom.ac.id` | `password123` | `654321` |
| **Sekretaris BEM** | Rizal Pratama | Sekretaris BEM FIKOM | `sekretaris@bemfikom.ac.id` | `password123` | *-* |

---

## 4. Persyaratan Sistem & Instalasi

### Prasyarat:
- **PHP:** Versi 8.2 atau lebih tinggi (ekstensi `openssl`, `gd`, `pdo_mysql`, `mbstring`, `fileinfo` aktif).
- **Composer:** Versi 2.x
- **MySQL / MariaDB:** Port 3306 (database `bem_fikom_esign`)
- **Node.js & npm**

### Langkah Menjalankan:
1. **Konfigurasi Environment Database (`.env`):**
   Pastikan konfigurasi MySQL pada file `.env` sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bem_fikom_esign
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Jalankan Migrasi & Seeder Lengkap:**
   Perintah ini akan membuat skema tabel, men-generate sertifikat digital X.509 (`bem_fikom.crt`), membuat aset cap & TTD transparan, akun demo, serta contoh surat (Sah, Menunggu, Dicabut):
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Kompilasi Aset Frontend (Vite):**
   ```bash
   npm run build
   ```
   *(Atau jalankan `npm run dev` jika ingin mode pengembangan hot-reload).*

4. **Jalankan Web Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban di: **`http://localhost:8000`**

5. **Menjalankan Pengujian Otomatis (Feature Tests):**
   ```bash
   php artisan test --filter=EsignWorkflowTest
   ```

---

## 5. Panduan Pengujian Alur Kerja (Workflows)

### A. Pengujian Verifikasi Publik & Integritas PDF (`/`)
1. Buka `http://localhost:8000` tanpa login.
2. **Tab 1 (Cari Nomor / Kode Surat):**
   - Masukkan nomor: `014/BEM-FIKOM/XI/2026` lalu klik **Cek Keabsahan**.
   - Halaman verifikasi akan terbuka menampilkan status hijau **SURAT RESMI SAH & TERDAFTAR**, pejabat penandatangan, cap stempel, dan daftar mahasiswa resmi.
3. **Tab 2 (Uji Integritas File PDF Anti-Edit):**
   - Unduh file PDF resmi dari surat `014/BEM-FIKOM/XI/2026` via tombol unduh.
   - Tarik & lepas (drag-and-drop) file PDF tersebut ke area upload Tab 2.
   - Sistem akan menghitung SHA-256 dan menampilkan notifikasi hijau **"DOKUMEN ASLI & TERVERIFIKASI (100% UTUH)"**.
   - Coba unggah file PDF lain yang sembarang/dimodifikasi: sistem seketika menampilkan peringatan merah tegas **"DOKUMEN TIDAK TERDAFTAR ATAU TELAH DIMODIFIKASI"**.

### B. Pengujian Alur Sekretaris (`/letters/create`)
1. Login sebagai Sekretaris (`sekretaris@bemfikom.ac.id` / `password123`).
2. Masuk ke menu **+ Ajukan Pengesahan Surat**.
3. Isi nomor surat, perihal, kategori, dan tanggal.
4. Pada tabel mahasiswa, gunakan tombol **+ Tambah Mahasiswa** untuk menambah Nama & NIM secara dinamis.
5. Unggah berkas draf PDF.
6. Kirim pengajuan. Surat otomatis berstatus `pending` dan masuk ke antrean pimpinan.

### C. Pengujian Alur Ketua / Wakil BEM (`/approvals`)
1. Login sebagai Ketua (`ketua@bemfikom.ac.id` / `password123`).
2. Buka menu **Antrean Pengesahan** (`/approvals`).
3. Pilih surat nomor `015/BEM-FIKOM/XI/2026` lalu klik **Tinjau & Sahkan**.
4. Halaman *split-screen* menampilkan pratinjau draf PDF di sisi kiri dan detail di sisi kanan.
5. Klik **Sahkan Surat (PIN)**, masukkan 6 digit PIN: `123456`.
6. Dokumen seketika disahkan dengan stempel Cap BEM transparan, tanda tangan, QR Code, dan sertifikat digital X.509.
7. Di halaman yang sama, Anda juga dapat menguji fitur **Cabut Pengesahan (Revoke)** dengan memasukkan alasan dan PIN.

### D. Pengujian Log Aktivitas & Jejak Audit (`/audit-logs`)
1. Buka menu **Log Aktivitas** (`/audit-logs`) saat login sebagai Ketua atau Wakil.
2. Seluruh histori: `SUBMITTED`, `APPROVED`, `REJECTED`, `REVOKED`, `VERIFIED_QR`, dan `VERIFIED_HASH_MATCH` tercatat lengkap dengan stempel waktu WIB, IP address, user, dan rincian metadata JSON.
