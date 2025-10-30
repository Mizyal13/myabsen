# MyAbsen — Employee Attendance Management (Laravel 12)

## Deskripsi Singkat
MyAbsen adalah aplikasi web untuk mengelola absensi karyawan yang mencakup proses check-in/check-out, pengajuan izin sakit maupun cuti dengan sistem persetujuan, serta pelaporan kehadiran berbasis role Admin dan Karyawan. Aplikasi dibangun menggunakan Laravel 12 (PHP 8.x) sehingga mudah dipelihara dan dikembangkan lebih lanjut.

## Stack Teknologi
- Laravel 12 & PHP 8.2+
- MySQL/MariaDB
- Blade Template Engine
- Composer (dependensi backend)
- Node.js & NPM *(opsional, untuk membangun aset frontend)*

## Fitur Utama
- **Absensi Real-Time**: check-in/check-out dengan penentuan status telat atau tepat waktu yang dapat dikonfigurasi.
- **Manajemen Izin**: pengajuan izin sakit/cuti lengkap dengan alur approval admin/atasan.
- **Dashboard Role-Based**: tampilan berbeda untuk Admin dan Karyawan sesuai hak akses.
- **Rekap & Laporan**: laporan absensi per karyawan dengan filter tanggal dan status.
- **Manajemen Berkas**: dukungan unggah serta akses dokumen melalui Laravel storage.

## Cara Menjalankan (Setup & Install)
### 1. Persiapan
- PHP 8.2+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `json`, `xml`, `ctype`, `bcmath`, `curl`
- Composer versi terbaru
- MySQL/MariaDB dan kredensial akses
- Node.js & NPM *(opsional untuk build aset frontend)*
- Web server (Apache/Nginx) atau gunakan `php artisan serve` saat pengembangan

### 2. Setup Proyek
```bash
git clone https://github.com/Mizyal13/myabsen.git
cd myabsen
composer install
```

Salin konfigurasi dasar dan generate key aplikasi:
```bash
cp .env.example .env
php artisan key:generate
```

Contoh konfigurasi `.env` (sesuaikan kredensial dan URL):
```dotenv
APP_NAME=MyAbsen
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

### 3. Setup Database
Pilih salah satu metode berikut:
```bash
# A. Migrasi + Seeder (disarankan untuk pengembangan)
php artisan migrate:fresh --seed

# B. Import file SQL yang disediakan
mysql -u root -p absensi < absensi.sql
```

### 4. Menjalankan Aplikasi
```bash
php artisan storage:link   # buat symlink storage publik
php artisan serve          # aplikasi tersedia di http://127.0.0.1:8000
```

Opsional: build aset frontend
```bash
npm install
npm run dev          # atau npm run build untuk produksi
```

## Screenshot Tampilan UI
![Halaman Login Admin](screenshot%20menu/1.%20Menu%20Login.PNG)
![Dashboard Admin](screenshot%20menu/2.%20Dashboard%20Admin.PNG)
![Daftar Absensi Karyawan](screenshot%20menu/3.%20List%20Absensi%20Karyawan.PNG)

## Tips & Troubleshooting
- **HTTP 500 / Blank Page**: cek `storage/logs/laravel.log`, pastikan versi PHP & ekstensi sesuai, dan `composer install` sukses.
- **Gagal konek database**: pastikan kredensial `.env` benar dan database tersedia.
- **Aset tidak muncul**: jalankan `npm run dev` atau `npm run build` lalu muat ulang cache browser.
- **File upload bermasalah**: pastikan `php artisan storage:link` sudah dijalankan dan folder `storage` memiliki izin tulis.

## Kontributor
- Mizyal Jillauzi — Jakarta Selatan  
  GitHub: [@Mizyal13](https://github.com/Mizyal13)
