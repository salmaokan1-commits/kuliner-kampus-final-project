# Kuliner Kampus

Kuliner Kampus adalah aplikasi pemesanan makanan berbasis **CodeIgniter 4**. Aplikasi ini mendukung fitur login, role pengguna, pengelolaan tempat kuliner/menu, pemesanan, upload bukti pembayaran transfer bank, serta verifikasi pembayaran oleh merchant.

## Kebutuhan Sistem

Pastikan perangkat sudah memiliki:

- PHP 8.1 atau lebih baru
- Composer
- MySQL/MariaDB
- Web server seperti Apache/XAMPP
- Ekstensi PHP umum CodeIgniter 4:
  - `intl`
  - `mbstring`
  - `json`
  - `mysqli` atau `mysqlnd`
  - `curl`

## Cara Instalasi

1. Clone atau salin project ke folder web server.

   Contoh jika menggunakan XAMPP:

   ```bash
   C:\xampp\htdocs\Kuliner Kampus
   ```

2. Masuk ke folder project.

   ```bash
   cd "C:\xampp\htdocs\Kuliner Kampus"
   ```

3. Install dependency menggunakan Composer.

   ```bash
   composer install
   ```

4. Buat database baru di MySQL/phpMyAdmin.

   Contoh nama database:

   ```text
   kuliner_kampus
   ```

5. Salin file `env` menjadi `.env` jika belum ada.

   ```bash
   copy env .env
   ```

   Jika menggunakan terminal Git Bash/Linux/macOS:

   ```bash
   cp env .env
   ```

6. Atur konfigurasi database pada file `.env`.

7. Jalankan migration database.

   ```bash
   php spark migrate
   ```

8. Jika project memiliki data SQL/dummy tambahan, import data tersebut melalui phpMyAdmin atau jalankan seeder jika tersedia.

9. Jalankan project.

   ```bash
   php spark serve
   ```

10. Buka aplikasi melalui browser.

   ```text
   http://localhost:8080
   ```

   Jika menggunakan XAMPP tanpa `php spark serve`, arahkan browser ke folder `public` atau sesuaikan virtual host Apache agar document root mengarah ke:

   ```text
   C:\xampp\htdocs\Kuliner Kampus\public
   ```

## Konfigurasi `.env`

Pastikan file `.env` berada di root project. Minimal konfigurasi yang perlu disesuaikan:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

# Database
database.default.hostname = localhost
database.default.database = kuliner_kampus
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Jika menggunakan XAMPP default, biasanya username database adalah `root` dan password dikosongkan.

Jika menjalankan project melalui Apache/XAMPP dengan URL berbeda, ubah `app.baseURL` sesuai alamat project, misalnya:

```env
app.baseURL = 'http://localhost/Kuliner%20Kampus/public/'
```

## Akun Demo

Gunakan akun berikut untuk mencoba fitur aplikasi.

| Role | Username/Login | Password | Keterangan |
|---|---|---|---|
| Merchant | `djava resto` | `djavaresto123` | Digunakan untuk mengelola kuliner/menu dan verifikasi pembayaran. |
| User/Pelanggan | `pelanggan` | `pelanggan123` | Digunakan untuk memesan makanan dan upload bukti pembayaran. |

> Catatan: Jika login menggunakan email, gunakan email yang sesuai dengan data pada database. Jika akun demo belum tersedia setelah instalasi, tambahkan akun tersebut melalui fitur daftar akun atau insert langsung ke tabel `users` dengan password yang sudah di-hash.

## Fitur Utama

- Login dan registrasi pengguna
- Role pengguna seperti merchant dan pelanggan
- Pengelolaan tempat kuliner
- Pengelolaan menu makanan
- Pemesanan makanan
- Detail pesanan dan invoice
- Upload bukti pembayaran transfer bank
- Dashboard merchant untuk verifikasi pembayaran
- Persetujuan atau penolakan pembayaran
- Countdown timer batas waktu pembayaran

## Struktur Project Penting

| Path | Fungsi |
|---|---|
| `app/Controllers` | Berisi controller utama seperti `Auth`, `Kuliner`, `Menu`, `Pesanan`, dan `Transaksi`. |
| `app/Models` | Berisi model database seperti `UserModel`, `KulinerModel`, `MenuModel`, dan `PesananModel`. |
| `app/Views` | Berisi halaman tampilan aplikasi. |
| `app/Database/Migrations` | Berisi file migration untuk struktur database. |
| `app/Config/Routes.php` | Berisi konfigurasi route aplikasi. |
| `public/uploads` | Folder penyimpanan file upload seperti bukti pembayaran. |

## Catatan Pengembangan

- Pastikan folder `writable` dan folder upload di dalam `public/uploads` dapat ditulis oleh server.
- Jangan gunakan konfigurasi `.env` development untuk server produksi.
- Untuk produksi, arahkan document root web server ke folder `public` agar file internal CodeIgniter tidak terekspos.
