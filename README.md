# TTG Pondokrejo

Platform tutorial digital "Teknologi Tepat Guna" untuk Kelurahan Pondokrejo, Sleman, Yogyakarta. Aplikasi ini dirancang sebagai Progressive Web App (PWA) yang ringan, cepat, dan mobile-first untuk memudahkan akses informasi bagi warga desa.

## Fitur Utama

- **Mobile-First Design**: Antarmuka yang dioptimalkan untuk penggunaan di smartphone.
- **Progressive Web App (PWA)**: Dapat diinstal di perangkat mobile dan berjalan secara offline.
- **Ringan & Cepat**: Dibangun dengan PHP Native dan SQLite tanpa framework berat.
- **Admin Panel**: Dashboard untuk mengelola konten tutorial, kategori, dan pengguna.
- **Pencarian Cepat**: Fitur pencarian tutorial yang responsif.
- **Kategori Terstruktur**: Pengelompokan tutorial berdasarkan bidang (Pertanian, Peternakan, Energi, dll).

## Teknologi

- **Backend**: PHP 8.2+ (Native)
- **Database**: SQLite 3 (Lokal) / PostgreSQL (Vercel/Neon)
- **Frontend**: Tailwind CSS v4
- **Interactivity**: Alpine.js
- **Icons**: FontAwesome & SVG

## Struktur Folder

```
/
├── database/            # File database & skema
├── includes/            # Logic PHP reusable (koneksi DB, fungsi helper)
├── public/              # Web root (file yang dapat diakses publik)
│   ├── admin/           # Halaman dashboard admin
│   ├── assets/          # File statis (CSS, JS, Gambar, Icon)
│   ├── uploads/         # File gambar tutorial yang diupload
│   └── index.php        # Halaman utama
└── src/                 # Source file untuk development (Tailwind CSS)
```

## Instalasi Lokal

1.  **Clone Repository**
    ```bash
    git clone https://github.com/diskonnekted/TTG-Pondokrejo.git
    cd TTG-Pondokrejo
    ```

2.  **Jalankan Server PHP**
    Pastikan PHP dan SQLite sudah terinstal di komputer Anda.
    ```bash
    php -S localhost:8000 -t public
    ```

3.  **Akses Aplikasi**
    Buka browser dan kunjungi `http://localhost:8000`.

4.  **Login Admin**
    - URL: `http://localhost:8000/admin/login.php`
    - Username Default: `admin`
    - Password Default: `admin123` (Disarankan segera diganti)

## Deployment (Vercel + PostgreSQL)

Aplikasi ini mendukung **Vercel Postgres (Neon)** untuk database yang persisten.

### Langkah 1: Setup Vercel
1.  Push kode ke GitHub.
2.  Import project ke Vercel.
3.  Pada dashboard Vercel, masuk ke tab **Storage**.
4.  Klik **Connect Store** -> **Create New** -> **Postgres**.
5.  Terima default settings dan klik **Create**.
6.  Setelah database dibuat, Vercel akan otomatis menambahkan Environment Variables (`POSTGRES_URL`, dll) ke project Anda.

### Langkah 2: Deploy & Install Database
1.  Redeploy project Anda (jika diperlukan) agar Environment Variables terbaca.
2.  Jalankan instalasi database dengan mengakses URL berikut di browser:
    `https://nama-project-anda.vercel.app/install.php?secret=install123`
3.  Jika sukses, Anda akan melihat pesan "Installation Complete".
4.  **PENTING:** Setelah instalasi selesai, segera hapus file `public/install.php` atau ganti secret key di `includes/config.php` (melalui Environment Variable `MIGRATE_SECRET`) untuk keamanan.

**Catatan:**
Dengan menggunakan PostgreSQL, semua data (tutorial, kategori, user) akan tersimpan secara permanen dan aman, berbeda dengan SQLite di Vercel yang bersifat sementara.

## Lisensi

Proyek ini dibuat untuk kepentingan masyarakat Kelurahan Pondokrejo dan bersifat Open Source.
