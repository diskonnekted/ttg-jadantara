# Cara Membuat File APK Android (PWA to APK)

Karena keterbatasan server (tidak ada Android Studio/Java JDK), kami tidak dapat membuat file `.apk` secara langsung di sini.

Namun, Anda dapat dengan mudah mengubah website ini menjadi aplikasi Android (APK) menggunakan layanan gratis **PWABuilder**.

## Langkah-langkah (Hanya butuh 2 menit):

1.  **Pastikan Website Online:**
    Pastikan website ini sudah di-hosting online (bukan localhost) dan dapat diakses publik dengan HTTPS.

2.  **Buka PWABuilder:**
    Kunjungi [https://www.pwabuilder.com/](https://www.pwabuilder.com/)

3.  **Masukkan URL:**
    Masukkan URL website Anda (misal: `https://ttg-pondokrejo.com`) dan klik **Start**.

4.  **Download Android Package:**
    *   Tunggu proses analisis selesai (Pastikan skornya bagus, kami sudah mengoptimalkan `manifest.json` untuk ini).
    *   Klik tombol **Package for Stores**.
    *   Pilih **Android**.
    *   Klik **Generate**.

5.  **Dapatkan APK:**
    *   PWABuilder akan memberikan file `.zip`.
    *   Ekstrak file tersebut.
    *   Di dalamnya terdapat file `.apk` (biasanya bernama `app-release-signed.apk` atau `app-debug.apk`) yang bisa langsung diinstall di HP Android.

## Fitur "Install App" Bawaan
Website ini juga sudah dilengkapi fitur **"Add to Home Screen"** otomatis.
Saat pengguna membuka website ini di HP Android (Chrome), akan muncul tombol pop-up **"Install Aplikasi"** di bagian bawah layar.
Jika diklik, website akan terinstall seperti aplikasi native (muncul di menu aplikasi, fullscreen, icon sendiri) tanpa perlu download APK manual.

## Aset Icon & Screenshot
Kami sudah menyiapkan aset yang dibutuhkan untuk proses build:
*   `public/manifest.json` (Konfigurasi Aplikasi)
*   `public/assets/images/icon-192.png` (Icon Aplikasi)
*   `public/assets/images/icon-512.png` (Icon Aplikasi Besar)
*   `public/assets/images/screenshot-mobile.png` (Screenshot Tampilan HP)
*   `public/assets/images/screenshot-desktop.png` (Screenshot Tampilan Laptop)
