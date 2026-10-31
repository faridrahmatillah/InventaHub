# Bahan Presentasi Teknis UAS — GudangKita

Dokumen ini merangkum implementasi yang ditemukan pada kode saat ini. Antarmuka menggunakan nama InventaHub; nama aplikasi dan materi tugas disebut GudangKita. Periksa ulang kode bila fitur berubah.

## 1. Arsitektur dan MVC

Aplikasi memakai Laravel 13 dan merender halaman di server menggunakan Blade.

- **Route** — [`routes/web.php`](../routes/web.php) mendefinisikan halaman login/registrasi dan resource kategori, produk, pemasok, transaksi, serta stok opname. Rute aplikasi berada dalam middleware `web`; area utama juga memakai `auth`. Rute admin memakai alias middleware `role` yang didaftarkan di [`bootstrap/app.php`](../bootstrap/app.php).
- **Controller** — [`app/Http/Controllers`](../app/Http/Controllers) menerima request, memvalidasi input, memanggil model, lalu mengembalikan view atau redirect.
- **Model** — [`app/Models`](../app/Models) memetakan tabel dan relasi melalui Eloquent.
- **View** — [`resources/views`](../resources/views) berisi template Blade. Layout memuat asset frontend melalui Vite.
- **Database** — [`database/migrations`](../database/migrations) mendefinisikan skema relasional. Konfigurasi contoh memakai MySQL; konfigurasi Laravel project juga menyediakan MariaDB.

Alur sederhananya: browser mengirim request ke route, middleware memeriksa akses, controller menjalankan validasi dan logika, model berinteraksi dengan database, lalu Laravel mengirim halaman atau redirect kembali ke browser.

## 2. Model dan relasi database

Relasi berikut didefinisikan pada model:

- `Category` memiliki banyak `Product`; `Product` termasuk ke satu kategori.
- `Product` memiliki banyak `Transaction` dan banyak `StockOpname`.
- `Supplier` memiliki banyak `Transaction`; transaksi dapat merujuk ke pemasok.
- `User` memiliki banyak `Transaction` dan `StockOpname`; pencatat transaksi/opname disimpan pada foreign key user.
- Model `Product`, `Category`, dan `Supplier` menggunakan soft delete. Relasi transaksi/opname ke produk dan transaksi ke pemasok menggunakan `withTrashed()` agar riwayat tetap bisa menampilkan data yang dinonaktifkan.

Foreign key, aturan hapus, dan kolom tabel dapat dilihat pada migration; misalnya [`create_products_table`](../database/migrations/2026_10_28_094336_create_products_table.php), [`create_transactions_table`](../database/migrations/2026_10_28_180838_create_transactions_table.php), serta migration pemasok dan stok opname. Penghapusan kategori yang masih memiliki produk aktif dan produk dengan stok tersisa dicegah oleh logika aplikasi; foreign key produk juga membatasi penghapusan kategori.

## 3. Siklus request: menambah produk

Contoh berikut mengikuti form dan kode yang ada:

1. Pengguna yang sudah login membuka `GET /products/create`. Route resource menuju `ProductController::create` di [`ProductController.php`](../app/Http/Controllers/ProductController.php); controller mengambil kategori untuk pilihan form.
2. [`resources/views/products/create.blade.php`](../resources/views/products/create.blade.php) menampilkan form `POST` ke route `products.store`. Blade `@csrf` menyisipkan token CSRF. Input kategori, kode, nama, stok awal, harga, dan deskripsi ditampilkan melalui komponen input.
3. Browser mengirim form. Middleware `auth` memastikan pengguna telah login dan middleware web Laravel memeriksa token CSRF.
4. `ProductController::store` menjalankan `$request->validate(...)`: kategori harus ada dan belum di-soft-delete, kode produk harus unik, nama wajib, stok bilangan bulat nonnegatif, harga numerik nonnegatif, dan deskripsi opsional. Jika validasi gagal, Laravel mengembalikan pengguna ke form dengan pesan error dan input lama.
5. Jika lolos validasi, controller memanggil `Product::create($validated)`. Kolom yang boleh diisi mass assignment dibatasi pada `$fillable` di [`Product.php`](../app/Models/Product.php). Eloquent mengirim query berparameter melalui koneksi database Laravel; tidak ada penyusunan SQL mentah dari nilai form pada alur ini.
6. Setelah penyimpanan berhasil, controller mengarahkan pengguna ke `products.index` dan menambahkan pesan sukses ke session. Route index mengambil daftar produk beserta kategori, lalu Blade mengirim HTML ke browser.
7. Jika terjadi `PDOException` saat operasi, controller memakai helper error pada [`Controller.php`](../app/Http/Controllers/Controller.php): kegagalan dicatat ke log dan request tulis diarahkan kembali dengan pesan umum. Exception database yang tidak tertangani juga memiliki renderer global di [`bootstrap/app.php`](../bootstrap/app.php).

Stok awal diisi ketika produk didaftarkan. Perubahan berikutnya dilakukan melalui transaksi atau stok opname, bukan melalui form edit produk.

## 4. Autentikasi dan authorization

- Route login dan registrasi hanya tersedia bagi guest. Route dashboard, produk, transaksi, dan stok opname dibungkus middleware `auth`.
- Registrasi di [`AuthController.php`](../app/Http/Controllers/AuthController.php) memvalidasi nama, email unik, dan password minimal delapan karakter yang dikonfirmasi; role baru selalu `staff`.
- Login memakai `Auth::attempt`, meregenerasi session saat berhasil, dan route login dibatasi `throttle:5,1`. Logout menghapus session dan membuat ulang token CSRF.
- Middleware [`EnsureRole.php`](../app/Http/Middleware/EnsureRole.php) menolak akses role yang tidak sesuai dengan HTTP 403. Kategori dan pemasok berada di grup admin; edit/update produk dibatasi untuk admin. Penghapusan produk dan transaksi juga memeriksa peran admin. Staff tetap dapat melihat produk/transaksi dan menggunakan fitur transaksi maupun stok opname sesuai rute.
- Seeder [`DatabaseSeeder.php`](../database/seeders/DatabaseSeeder.php) hanya membuat akun admin/staff pada `local` atau `testing`. Jangan menjalankannya di production.
- Rute saat ini tidak menunjukkan verifikasi email atau fitur reset password.

## 5. Validasi dan keamanan input

- Form Blade yang mengirim data menggunakan `@csrf`; token diperiksa middleware web Laravel.
- Controller memvalidasi tipe, keharusan, rentang, keunikan, serta keberadaan record sebelum menyimpan. Validasi bukan proses HTML sanitization tersendiri.
- View menampilkan nilai dengan `{{ ... }}` Blade yang melakukan escaping HTML. Ini membantu mencegah input teks menjadi markup aktif saat dirender. Hindari output Blade mentah untuk data pengguna.
- Query pada alur yang dijelaskan menggunakan Eloquent/Query Builder, bukan menggabungkan input pengguna menjadi SQL mentah.
- Password disimpan melalui `Hash::make`; model `User` juga menetapkan cast password `hashed`. Password dan remember token disembunyikan dari serialisasi model.
- `.env` lokal diabaikan Git menurut `.gitignore`. `APP_KEY`, password database, dan kredensial production harus dibuat atau diisi di lingkungan masing-masing dan tidak dimasukkan ke repository.

## 6. Error yang ditangani

- **Validasi gagal**: mekanisme validasi Laravel kembali ke form dengan error per field.
- **Akses tidak sah**: middleware role atau pemeriksaan admin menghentikan request dengan 403.
- **Stok keluar tidak cukup**: `TransactionController::store` melempar `ValidationException` untuk field quantity; transaksi database dibatalkan.
- **Database gagal**: helper controller mencatat operasi dan jenis exception, lalu mengirim halaman 503 atau redirect dengan pesan umum. Renderer global di `bootstrap/app.php` mencatat exception database yang lolos dari controller dan menampilkan halaman `errors.database`. Detail koneksi tidak disajikan ke pengguna.
- Tidak semua controller memiliki blok `try/catch` lokal; error database yang tidak ditangani lokal bergantung pada exception handler global.

## 7. Deployment dan troubleshooting

1. Siapkan server dengan PHP 8.3+, Composer, ekstensi PHP termasuk `pdo_mysql`, database MySQL/MariaDB yang kompatibel, dan akses HTTPS.
2. Arahkan document root ke folder `public`, bukan root project. `public/.htaccess` tersedia untuk Apache dan membutuhkan rewrite yang aktif. Untuk web server lain, siapkan aturan setara berdasarkan dokumentasi host; repository ini tidak menentukan platform atau menyertakan konfigurasi Nginx.
3. Atur `.env` production secara privat: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS, `APP_KEY` unik, koneksi MySQL/MariaDB, serta session cookie aman (`SESSION_SECURE_COOKIE=true`).
4. Pasang dependency PHP production dan build frontend (`npm ci`, `npm run build`) pada pipeline build. Deploy asset `public/build` termasuk `manifest.json`.
5. Setelah backup dan persetujuan operator, jalankan migration production dengan `php artisan migrate --force`. Migration tidak diuji terhadap database hosting dalam audit ini.
6. Pastikan proses PHP dapat menulis ke `storage` dan `bootstrap/cache`. Buat storage link hanya bila nanti menggunakan file di disk publik; tidak ditemukan fitur unggah file pada kode saat ini.
7. Jalankan cache konfigurasi/rute/view sesuai prosedur hosting dan ulangi cache konfigurasi ketika nilai `.env` berubah.
8. Lindungi `.env`, dump SQL, backup, serta log dari akses publik. Dump yang tersedia berada di `database/exports/gudangkita.sql`; file ini tidak diubah atau diuji impor sebagai bagian audit.

Gejala umum:

- `Unknown database` atau `Access denied`: periksa nama database, kredensial dan grant, host, port, serta layanan DB.
- `could not find driver`: aktifkan `pdo_mysql` untuk PHP CLI dan PHP web.
- `No application encryption key`: buat key khusus environment dengan `php artisan key:generate`.
- Manifest Vite hilang: build dan deploy `public/build/manifest.json`.
- 419: periksa session cookie, HTTPS, domain, dan token CSRF pada form.
- 403: periksa status login dan role pengguna.
- Error 503 database: periksa konektivitas dan log di `storage/logs`.

## 8. Status yang belum dibuktikan

- Belum ada target hosting atau konfigurasi production tertentu dalam repository; document root, rewrite, TLS, permission, backup, dan batas versi hosting perlu diverifikasi manual.
- Dump SQL belum diuji impor. Audit statis menemukan struktur 14 tabel dan enam foreign key; dump berisi beberapa data, tetapi tidak memiliki pernyataan `INSERT` untuk `transactions`.
- Tidak ada klaim bahwa aplikasi telah dideploy atau database production telah dimigrasikan.
