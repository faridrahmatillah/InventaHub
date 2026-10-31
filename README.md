# GudangKita

GudangKita adalah aplikasi web inventaris berbasis Laravel untuk mengelola produk gudang. Antarmukanya saat ini menggunakan nama InventaHub.

## Fitur yang tersedia

- Registrasi akun staff, login, dan logout.
- Dashboard berisi ringkasan kategori, produk, transaksi terbaru, serta produk dengan stok kurang dari lima.
- Produk: daftar, detail, tambah, ubah, dan soft delete. Mengubah dan menonaktifkan produk dibatasi untuk admin; produk dengan stok tersisa tidak dapat dinonaktifkan.
- Kategori dan pemasok: pengelolaan oleh admin, termasuk soft delete.
- Transaksi barang masuk dan keluar. Stok diperbarui dalam transaksi database; transaksi masuk memerlukan pemasok.
- Stok opname: admin dan staff dapat melihat serta membuat catatan. Penyimpanan catatan menyesuaikan stok secara atomik; catatan tidak memiliki aksi ubah atau hapus.
- Pembatasan akses berdasarkan login dan peran admin/staff.

Pendaftaran publik selalu membuat peran staff. Tidak ada halaman pemilihan peran admin, verifikasi email, atau alur reset password yang didefinisikan di rute aplikasi saat ini.

## Persyaratan

- PHP **8.3 atau lebih baru**, sesuai `composer.json`, dengan ekstensi PHP yang dibutuhkan Laravel dan `pdo_mysql` untuk database MySQL/MariaDB. Ekstensi `pdo_sqlite` juga diperlukan bila ingin menjalankan test suite yang dikonfigurasi memakai SQLite in-memory.
- Composer 2.x (project tidak menetapkan batas versi Composer tertentu).
- MySQL atau MariaDB yang kompatibel dengan Laravel 13. Project menyediakan konfigurasi `mysql` dan `mariadb`; versi minimum server tidak dipatok dalam manifest project.
- Node.js **20.19 atau lebih baru**, atau **22.12 atau lebih baru**, sesuai kebutuhan Vite pada `package-lock.json`; npm ikut tersedia bersama Node.js.
- Git.

## Instalasi lokal

Contoh berikut memakai PowerShell pada Windows. Sesuaikan lokasi kerja dan kredensial database dengan mesin Anda.

1. Clone repository dan masuk ke foldernya:

   ```powershell
   git clone https://github.com/faridrahmatillah/InventaHub.git
   cd InventaHub
   ```

2. Buat konfigurasi lokal dari contoh. Jangan commit `.env`:

   ```powershell
   Copy-Item .env.example .env
   ```

3. Buat database MySQL. Jalankan SQL berikut melalui klien MySQL dengan akun yang berwenang:

   ```sql
   CREATE DATABASE gudangkita
       CHARACTER SET utf8mb4
       COLLATE utf8mb4_unicode_ci;
   ```

4. Buka `.env` lokal dan sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` dengan database yang baru dibuat. Contoh ini memakai `DB_CONNECTION=mysql`; jangan memasukkan kredensial ke repository.

5. Pasang dependency PHP, buat application key lokal, lalu jalankan migration:

   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate
   ```

   Migration membuat tabel aplikasi beserta tabel pendukung database untuk session, cache, dan queue. Perintah migration tidak dijalankan otomatis oleh langkah-langkah di atas.

6. (Opsional) Buat akun lokal melalui seeder:

   ```powershell
   php artisan db:seed
   ```

   Seeder hanya membuat akun ketika environment bernilai `local` atau `testing`. Akun yang tertulis di seeder adalah `admin@example.com` dan `staff@example.com`, masing-masing dengan password awal `password`. Ganti password untuk pemakaian pribadi dan **jangan** menjalankan seeder akun ini pada production.

7. Pasang dependency frontend dan build asset:

   ```powershell
   npm ci
   npm run build
   ```

8. Jalankan server development:

   ```powershell
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000`.

## Database dan pengujian

Project menyediakan migration untuk `users`, `categories`, `products`, `transactions`, `suppliers`, dan `stock_opnames`, termasuk foreign key serta tabel pendukung Laravel. Gunakan migration sebagai sumber skema untuk instalasi baru:

```powershell
php artisan migrate
```

Pengujian PHPUnit mengatur SQLite in-memory melalui `phpunit.xml`, sehingga suite normal tidak memakai database MySQL pada `.env`. Pastikan ekstensi `pdo_sqlite` aktif:

```powershell
php artisan test
```

Dump tambahan ada di `database/exports/gudangkita.sql`. Dump tersebut bukan pengganti migration; periksa isi dan sensitivitas datanya sebelum membagikan atau mengimpornya. Status pengujian impor dump tidak dinyatakan di sini.

## Deployment

Detail deployment bergantung pada penyedia hosting dan web server yang dipilih. Sebelum deployment:

1. Arahkan document root web server ke folder `public`, bukan root repository.
2. Pastikan server meneruskan URL yang tidak cocok dengan file statis ke `public/index.php`. `public/.htaccess` tersedia untuk Apache; Apache perlu `mod_rewrite` dan izin konfigurasi direktori yang sesuai. Web server lain memerlukan aturan rewrite yang setara dari pengelolanya. Tidak ada konfigurasi Nginx atau platform hosting tertentu di repository.
3. Siapkan `.env` production di luar Git: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS, `APP_KEY` unik hasil `php artisan key:generate`, serta kredensial MySQL/MariaDB production. Jangan gunakan key lokal atau password seeder.
4. Pasang dependency production (`composer install --no-dev --optimize-autoloader`) dan siapkan frontend dengan `npm ci` lalu `npm run build` di proses build; deploy direktori `public/build` beserta manifest Vite.
5. Setelah backup dan persetujuan operator database, jalankan `php artisan migrate --force`. Jangan jalankan migration production sebelum target database dan rencana backup dipastikan.
6. Beri hak tulis yang diperlukan kepada proses PHP pada `storage` dan `bootstrap/cache`. `php artisan storage:link` hanya diperlukan bila aplikasi mulai menggunakan disk publik; fitur unggah file tidak ditemukan pada fitur saat ini.
7. Setelah konfigurasi production tersedia, cache konfigurasi/rute/view sesuai proses rilis hosting. Jika mengganti `.env`, perbarui cache konfigurasi dengan prosedur deployment hosting.
8. Gunakan HTTPS dan aktifkan `SESSION_SECURE_COOKIE=true` untuk cookie session di production. Pastikan dump, backup, log, dan `.env` tidak dapat diakses dari web.

Belum ada target hosting, konfigurasi web server production, atau bukti deployment dalam repository. Verifikasi batas versi PHP/database, HTTPS, rewrite, permission, dan prosedur backup dengan penyedia hosting sebelum rilis.

## Troubleshooting

- **`SQLSTATE[HY000] [1049] Unknown database`**: buat database, lalu cocokkan `DB_DATABASE` di `.env`.
- **`Access denied` atau koneksi ditolak**: periksa host/port, nama pengguna, password, hak akses MySQL, dan apakah layanan database berjalan.
- **Driver `mysql` tidak ditemukan**: aktifkan ekstensi PHP `pdo_mysql` untuk CLI dan PHP yang digunakan web server.
- **`No application encryption key has been specified`**: pastikan `.env` lokal tersedia, lalu jalankan `php artisan key:generate`.
- **Tabel tidak ditemukan**: setelah memeriksa target koneksi, jalankan `php artisan migrate`. Jangan memakai opsi `migrate:fresh` pada database yang berisi data.
- **Vite manifest tidak ditemukan**: jalankan `npm ci` dan `npm run build`, lalu pastikan `public/build/manifest.json` terdeploy.
- **HTTP 419 / CSRF token mismatch**: pastikan browser menerima cookie session, form memakai token CSRF, dan URL/protokol cookie sesuai konfigurasi HTTPS.
- **HTTP 403**: periksa login dan peran akun; kategori, pemasok, serta pengubahan produk dibatasi untuk admin.
- **Halaman gangguan database / HTTP 503**: periksa koneksi database dan log aplikasi di `storage/logs`; halaman error tidak menampilkan detail koneksi kepada pengunjung.

## Susunan utama kode

- `routes/web.php`: rute web dan middleware login/peran.
- `app/Http/Controllers`: alur request dan validasi.
- `app/Models`: model Eloquent dan relasi.
- `database/migrations`: definisi tabel dan foreign key.
- `resources/views`: halaman Blade server-rendered.
- `tests/Feature`: pengujian fitur yang tersedia.
- `docs/presentasi-uas.md`: penjelasan arsitektur, request lifecycle, keamanan, dan deployment untuk presentasi.
