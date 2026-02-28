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
- **Database**: SQLite 3
- **Frontend**: Tailwind CSS v4
- **Interactivity**: Alpine.js
- **Icons**: FontAwesome & SVG

## Struktur Folder

```
/
├── database/            # File database SQLite (ttg_pondokrejo.db)
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

## Deployment (Vercel)

Aplikasi ini sudah dikonfigurasi untuk deployment di Vercel menggunakan runtime `vercel-php`.

1.  Push kode ke repository GitHub.
2.  Import project di dashboard Vercel.
3.  Vercel akan otomatis mendeteksi konfigurasi `vercel.json` dan melakukan deploy.

**Catatan Penting:**
Karena Vercel menggunakan arsitektur serverless (ephemeral filesystem), perubahan data (tambah/edit tutorial) melalui admin panel **tidak akan tersimpan permanen** di database SQLite. Untuk penggunaan produksi dengan fitur admin aktif, disarankan menggunakan hosting PHP tradisional (cPanel/VPS) atau layanan database eksternal.

## Lisensi

Proyek ini dibuat untuk kepentingan masyarakat Kelurahan Pondokrejo dan bersifat Open Source.
