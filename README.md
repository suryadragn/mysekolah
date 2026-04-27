# CMS Sekolahku - Modern & Premium

CMS Sekolahku adalah sistem manajemen konten sekolah yang dirancang dengan desain modern berbasis **Glassmorphism**. Dibuat menggunakan PHP Native dan MySQL, sistem ini memudahkan pengelolaan portal informasi sekolah secara dinamis.

## ✨ Fitur Utama
- **Dashboard Admin Modern**: Tampilan premium dengan statistik real-time.
- **Pengaturan Umum Dinamis**:
  - Ubah Logo & Favicon sekolah.
  - Ganti Background Hero Section secara dinamis.
  - Kelola Tahun Ajaran & Tagline.
- **Manajemen Konten**:
  - **Berita & Artikel**: CRUD berita lengkap dengan gambar.
  - **Galeri Foto**: Kelola dokumentasi kegiatan sekolah.
  - **Sosial Media**: CRUD tautan sosial media (Facebook, IG, dll) untuk footer.
- **Sistem PPDB Online**:
  - Formulir pendaftaran siswa baru.
  - Dashboard admin untuk verifikasi data.
  - **Ekspor Data**: Tarik data pendaftar ke format Excel (CSV).
- **Pesan Masuk**: Kelola pesan dari pengunjung melalui formulir kontak.

## 🚀 Teknologi yang Digunakan
- **Frontend**: HTML5, Vanilla CSS3 (Custom Glassmorphism), JavaScript.
- **Backend**: PHP Native (PDO).
- **Database**: MySQL.
- **Icons**: Emoji & Custom CSS Shapes.

## 🛠️ Cara Instalasi (Localhost - Laragon)

1. **Clone Repository**:
   ```bash
   git clone https://github.com/suryadragn/mysekolah.git
   ```
2. **Database**:
   - Buka Database Manager (HeidiSQL/phpMyAdmin).
   - Buat database baru bernama `mysekolah`.
   - Import file `database.sql` yang ada di root folder.
3. **Konfigurasi Environment**:
   - Sesuaikan file `.env` di root directory:
     ```env
     DB_HOST=127.0.0.1
     DB_PORT=3307
     DB_NAME=mysekolah
     DB_USER=root
     DB_PASS=
     APP_NAME=MySekolah
     ```
   - *Catatan: Gunakan port 3307 jika menggunakan MySQL default Laragon yang sudah dikonfigurasi.*
4. **Jalankan Proyek**:
   - Pindahkan folder ke `C:/laragon/www/mysekolah`.
   - Akses melalui browser: `http://mysekolah.test` atau `http://localhost/mysekolah`.

## 🔐 Akun Administrator Default
- **Halaman Login**: `/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

## 📧 Kontak
Dikembangkan dengan ❤️ oleh **Antigravity** untuk **suryadragn**.
