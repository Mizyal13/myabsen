
# MyAbsen — Employee Attendance Management (Laravel 12)

Aplikasi **manajemen absensi karyawan** berbasis **Laravel 12 (PHP 8.x)** untuk **check-in/check-out**, **pengajuan izin (sakit/cuti) dengan approval**, dan **laporan per karyawan**.

---

## Fitur
- **Absensi**: check-in / check-out, aturan telat/tepat waktu (konfigurable).
- **Izin**: pengajuan sakit/cuti + **approval** admin/atasan.
- **Laporan**: rekap per karyawan dengan filter.
- **Role**: minimal **Admin** & **Karyawan**.
- **Storage**: dukungan unggah/akses berkas via Laravel storage.

---

## Tech Stack
- **Framework**: Laravel 12, PHP 8.x
- **Database**: MySQL / MariaDB
- **Views**: Blade
- **Tooling**: Composer (wajib), Node.js/NPM *(opsional, untuk build asset)*

---

## Prasyarat
- PHP **8.2+** (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `json`, `xml`, `ctype`, `bcmath`, `curl`)
- Composer (terbaru)
- MySQL/MariaDB
- Node.js & NPM *(opsional)*
- Web server (Apache/Nginx) **atau** `php artisan serve` saat development

---

## Quick Start

git clone https://github.com/Mizyal13/myabsen.git
cd myabsen
composer install
cp .env.example .env  # jika .env.example belum ada, gunakan template .env di bawah
php artisan key:generate





> Salin ke berkas `.env` lalu sesuaikan nilainya.


APP_NAME=MyAbsen
APP_ENV=local
APP_KEY=base64:GENERATE_WITH_ARTISAN
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi
DB_USERNAME=root
DB_PASSWORD=your_password

# Cache / Session / Queue
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

# Filesystem
FILESYSTEM_DISK=public

# Mail (opsional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@myabsen.local"
MAIL_FROM_NAME="${APP_NAME}"

# Broadcasting (opsional)
BROADCAST_DRIVER=log


## Setup Database

Pilih salah satu:

**A. Migrate & Seed (direkomendasikan untuk dev)**


php artisan migrate:fresh --seed


**B. Import SQL (jika tersedia `absensi.sql`)**

mysql -u root -p absensi < absensi.sql


---

## Menjalankan Aplikasi

php artisan storage:link   # jika perlu akses storage publik
php artisan serve          # http://127.0.0.1:8000


*(Opsional — asset frontend)*

npm install
npm run dev   # atau npm run build



---

## Troubleshooting

* **HTTP 500 / Blank Page**: periksa `storage/logs/laravel.log`, versi PHP & ekstensi, pastikan `composer install` sukses.
* **Gagal Koneksi DB**: cek kredensial `.env`, pastikan DB ada; uji `mysql -u <user> -p`.
* **Asset tidak muncul**: jika pakai Mix/Vite, jalankan `npm run dev` dan cek path hasil build.
* **Gambar/Berkas hilang**: pastikan `php artisan storage:link` dan permission `storage`/`public`.

---

## Kontribusi

1. Fork repo
2. `git checkout -b feat/nama-fitur`
3. `git commit -m "feat: tambah fitur X"`
4. `git push origin feat/nama-fitur`
5. Buka Pull Request

---

## Lisensi

TBD (mis. **MIT** atau **All rights reserved**).

---

## Pengembang

**Mizyal Jillauzi** — Jakarta Selatan
GitHub: [https://github.com/Mizyal13](https://github.com/Mizyal13)

```

::contentReference[oaicite:0]{index=0}
```
