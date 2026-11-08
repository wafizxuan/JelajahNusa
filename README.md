# JelajahNusa

> **Discover Indonesia, Your Way**

JelajahNusa adalah aplikasi web travel guide dan travel planner untuk menemukan destinasi wisata di Indonesia, menyimpan destinasi favorit, menulis ulasan, serta menyusun rencana perjalanan. Antarmuka utama menggunakan PHP Native dan menggabungkan estetika alam dengan Bootstrap dan JavaScript.

## Ringkasan fitur

### Pengunjung dan pengguna

- Jelajahi katalog destinasi wisata dari database MySQL.
- Cari dan filter destinasi berdasarkan kategori.
- Buka halaman detail destinasi.
- Melihat halaman detail yang memuat gambar, informasi lokasi, ulasan, serta pilihan perjalanan.
- Daftar dan masuk melalui modal autentikasi.
- Menyimpan destinasi favorit ke akun pengguna.
- Membuat, melihat, memperbarui, dan menghapus rencana perjalanan.
- Menambahkan item per hari pada rencana perjalanan.
- Mengirim ulasan dan rating untuk destinasi.
- Mengubah nama profil dan kata sandi.

### Admin

- Masuk ke dashboard administrator.
- Melihat ringkasan jumlah pengguna, destinasi, dan ulasan menunggu moderasi.
- Menambah, mengubah, dan menghapus destinasi.
- Mengelola pengguna terdaftar.
- Menyetujui atau menyembunyikan ulasan pengguna.

## Teknologi

| Komponen | Teknologi |
| --- | --- |
| Backend | PHP Native |
| Database | MySQL melalui PDO |
| Frontend | HTML5, CSS3, JavaScript |
| UI framework | Bootstrap 5.3 |
| Ikon | Bootstrap Icons |
| Tipografi | Plus Jakarta Sans dan Inter |
| Server lokal | Laragon, Apache, dan MySQL |
| Integrasi opsional | Google Places API |

Tidak ada proses build atau package manager yang diwajibkan untuk menjalankan aplikasi ini.

## Konsep desain

JelajahNusa menggunakan pendekatan visual nature-inspired dengan fokus pada keterbacaan dan responsivitas.

| Warna | Nilai | Penggunaan |
| --- | --- | --- |
| Forest Green | `#345B49` | Warna utama, tombol, dan identitas |
| Sage Green | `#A8B99A` | Aksen dan highlight |
| Soft Beige | `#E8E6D9` | Elemen penyeimbang |
| Off White | `#F7F7F2` | Latar utama |
| Muted Green | `#52665A` | Teks sekunder |
| Dark Forest | `#29352D` | Judul dan footer |

## Struktur direktori

```text
jelajahnusa/
├── index.php                  # Beranda dan katalog utama
├── README.md
├── admin/                     # Dashboard dan pengelolaan admin
│   ├── index.php
│   ├── auth_check.php
│   ├── destinasi.php
│   ├── destinasi_tambah.php
│   ├── destinasi_edit.php
│   ├── reviews.php
│   └── users.php
├── assets/
│   ├── css/                   # Style utama, detail, dan travel planner
│   ├── js/                    # Interaksi frontend
│   └── images/                # Logo dan aset visual
├── auth/                      # Login, registrasi, logout, dan endpoint akun
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── check-session.php
│   ├── profile.php
│   ├── favorites.php
│   ├── reviews.php
│   ├── trips.php
│   └── places_proxy.php
├── config/
│   ├── database.php           # Konfigurasi koneksi MySQL aktif
│   ├── places_api.php         # Konfigurasi Google Places aktif
│   └── places_api.example.php
├── data/
│   └── destinasi.php          # Data fallback/pendukung destinasi
├── database/
│   ├── seed_destinations.php
│   ├── create_favorites_table.php
│   ├── create_reviews_table.php
│   ├── create_trips_tables.php
│   ├── add_google_place_id.php
│   └── migrate_rating_nullable.php
└── pages/
    ├── detail-destinasi.php
    ├── favorit.php
    ├── rencana-perjalanan.php
    ├── profil.php
    └── review-section.php
```

Direktori dan file cadangan berakhiran `.bak`, `.bak2`, atau serupa tidak digunakan oleh alur aplikasi utama.

## Persyaratan

- Laragon dengan Apache dan MySQL aktif.
- PHP 8.x atau versi yang didukung Laragon.
- Ekstensi PDO MySQL aktif.
- MySQL database bernama `jelajahnusa_db`.

## Instalasi dan menjalankan melalui Laragon

1. Pastikan folder project berada di direktori Laragon:

   ```text
   C:\laragon\www\jelajahnusa
   ```

2. Jalankan Laragon dan klik **Start All** untuk menyalakan Apache dan MySQL.

3. Buat database `jelajahnusa_db` melalui Laragon Database atau phpMyAdmin jika database belum tersedia.

4. Sesuaikan koneksi pada `config/database.php` bila diperlukan:

   ```php
   DB_HOST = '127.0.0.1';
   DB_PORT = '3306';
   DB_NAME = 'jelajahnusa_db';
   DB_USER = 'root';
   DB_PASS = '';
   ```

5. Jalankan skrip migrasi dari root project menggunakan terminal Laragon:

   ```bash
   php database/create_favorites_table.php
   php database/create_reviews_table.php
   php database/create_trips_tables.php
   php database/add_google_place_id.php
   php database/migrate_rating_nullable.php
   php database/seed_destinations.php
   ```

   Skrip migrasi yang bersifat pembatas localhost tidak boleh dijalankan langsung dari browser luar host.

6. Buat virtual host Laragon. Nama virtual host default dapat menjadi:

   ```text
   http://jelajahnusa.test
   ```

7. Jika virtual host belum dibuat, akses melalui URL Laragon:

   ```text
   http://localhost/jelajahnusa/
   ```

8. Akses halaman admin setelah memiliki akun dengan role `admin`:

   ```text
   http://localhost/jelajahnusa/admin/
   ```

## Alur penggunaan utama

1. Buka beranda dan pilih kategori atau cari nama destinasi.
2. Buka detail destinasi untuk melihat informasi dan ulasan.
3. Daftar akun atau masuk melalui modal autentikasi.
4. Simpan destinasi sebagai favorit.
5. Susun rencana perjalanan dan tambahkan item sesuai hari kunjungan.
6. Kirim ulasan. Ulasan akan tersedia di tampilan publik setelah disetujui admin.
7. Admin dapat memantau dan memoderasi ulasan melalui dashboard admin.

## Database dan API

Koneksi utama disediakan oleh `config/database.php` dan data utama disimpan pada tabel MySQL, termasuk akun pengguna, destinasi, favorit, ulasan, dan rencana perjalanan.

Konfigurasi Google Places tersedia sebagai integrasi opsional pada `config/places_api.php`. Integrasi tidak aktif secara default:

```php
define('GOOGLE_PLACES_API_KEY', getenv('GOOGLE_PLACES_API_KEY') ?: 'YOUR_GOOGLE_PLACES_API_KEY_HERE');
define('GOOGLE_PLACES_ENABLED', false);
```

Untuk mengaktifkannya:

1. Isi API key melalui environment variable `GOOGLE_PLACES_API_KEY` atau konfigurasi lokal.
2. Ubah `GOOGLE_PLACES_ENABLED` menjadi `true`.
3. Jangan menyimpan API key produksi langsung pada repository.

## Pengujian dasar

Pastikan tidak ada kesalahan PHP dengan menjalankan pemeriksaan sintaks pada file PHP aktif:

```bash
php -l index.php
php -l auth/login.php
php -l admin/auth_check.php
```

Untuk pengujian manual, verifikasi alur berikut melalui browser:

- Beranda dan pencarian destinasi dapat dibuka.
- Login dan registrasi pengguna berhasil.
- Favorit tersimpan dan terlihat pada halaman akun pengguna.
- Ulasan dapat dikirim dengan status moderasi yang sesuai.
- Rencana perjalanan dapat dibuat dan diedit.
- Dashboard admin dapat diakses oleh akun ber-role `admin`.
- CRUD destinasi dan moderasi ulasan admin berfungsi.
- Logout menghapus sesi dan melindungi halaman yang membutuhkan autentikasi.

## Keamanan

- Gunakan akun dan kredensial yang berbeda untuk lingkungan pengembangan dan produksi.
- Jangan commit `config/places_api.php` atau API key aktif.
- Jangan gunakan konfigurasi database produksi pada repository publik.
- Lindungi endpoint admin dan endpoint akun menggunakan sesi PHP serta pemeriksaan role atau autentikasi yang sesuai.
