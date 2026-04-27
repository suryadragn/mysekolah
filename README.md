# 🏫 CMS Sekolahku — Modern & Premium

> Platform manajemen konten sekolah berbasis PHP Native dengan desain **Glassmorphism** yang modern, elegan, dan lengkap.

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat&logo=mysql)
![License](https://img.shields.io/badge/License-Proprietary-red?style=flat)

---

## ✨ Fitur Utama

- **Dashboard Admin Modern** — Statistik real-time dengan desain Glassmorphism premium.
- **Pengaturan Dinamis** — Ganti Logo, Favicon, Background Hero, Tahun Ajaran & Tagline langsung dari admin.
- **Manajemen Konten** — CRUD Berita, Galeri Foto, dan Link Sosial Media.
- **PPDB Online** — Formulir pendaftaran siswa baru + ekspor data ke Excel.
- **Pesan Masuk** — Terima & kelola pesan dari pengunjung website.
- **Sistem Lisensi Domain-Bound** — Setiap instalasi terikat ke domain spesifik.
- **Trial 14 Hari** — Pengguna baru dapat mencoba semua fitur secara gratis.

---

## 🔐 Lisensi & Aktivasi

Aplikasi ini menggunakan sistem **Domain-Bound License**. Setiap License Key hanya berlaku untuk **satu domain spesifik**.

### Cara Mendapatkan License Key

1. **Coba Trial 14 Hari secara gratis** — langsung tersedia saat pertama kali install.
2. **Beli Lisensi Penuh** — Hubungi kami untuk mendapatkan License Key permanen untuk domain Anda.

### 📬 Kontak & Pembelian

| Platform | Link |
|---|---|
| 🐙 GitHub | [github.com/suryadragn](https://github.com/suryadragn) |
| 📧 Email | adhisurya05@gmail.com |

> **Catatan:** License Key bersifat permanen untuk satu domain. Jika domain berubah, diperlukan License Key baru.

---

## 🚀 Cara Instalasi (Localhost — Laragon)

### 1. Clone Repository
```bash
git clone https://github.com/suryadragn/mysekolah.git
cd mysekolah
```

### 2. Setup Database
- Buka **HeidiSQL** atau **phpMyAdmin**.
- Buat database baru bernama `mysekolah`.
- Import file `database.sql` yang ada di root folder.

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Lalu sesuaikan isinya:
```env
APP_NAME="Nama Sekolah Anda"
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=mysekolah
DB_USER=root
DB_PASS=
```

### 4. Jalankan Aplikasi
Akses melalui browser:
```
http://localhost/mysekolah
```

### 5. Aktivasi
Saat pertama kali diakses, aplikasi akan menampilkan halaman **Aktivasi**. Pilih salah satu:
- **Mulai Trial 14 Hari** — gratis, tanpa key.
- **Masukkan License Key** — jika sudah membeli lisensi penuh.

---

## 🔑 Akun Administrator Default

| | |
|---|---|
| **URL Login** | `/admin/login.php` |
| **Username** | `admin` |
| **Password** | `admin123` |

> ⚠️ Segera ganti password setelah instalasi pertama!

---

## 📁 Struktur Folder

```
mysekolah/
├── admin/              # Panel Admin
│   ├── layout/         # Komponen sidebar
│   ├── db.php          # Koneksi & auth
│   └── ...
├── assets/             # CSS & JS
├── uploads/            # File yang diupload
├── .env.example        # Template konfigurasi
├── activate.php        # Halaman aktivasi lisensi
├── database.sql        # Dump database
└── index.php           # Halaman utama
```

---

## ⚙️ Teknologi

- **Backend**: PHP Native (PDO)
- **Frontend**: HTML5, Vanilla CSS3 (Glassmorphism), JavaScript
- **Database**: MySQL

---

## ©️ Copyright

Copyright © 2026 **suryadragn**. All Rights Reserved.

Dilarang keras menyalin, mendistribusikan, atau menggunakan aplikasi ini tanpa izin tertulis dan License Key yang valid dari pemilik. Lihat file [LICENSE](./LICENSE) untuk detail lengkap.
