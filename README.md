# 🌿 JelajahNusa — Discover Indonesia, Your Way

**JelajahNusa** adalah platform digital *Travel Guide & Travel Planner* Indonesia yang menyatukan eksplorasi destinasi, penginapan ramah lingkungan (*Stay*), kuliner lokal (*Culinary*), aktivitas berkesan (*Experiences*), serta perencanaan perjalanan (*Itinerary*) dalam satu platform modern dan berkesinambungan.

---

## 🎨 Konsep Desain: Nature-Inspired, Aesthetic, Minimalist & Clean

- **Palet Warna Alami (Bebas oranye & bebas neon, tanpa gradasi berlebihan)**:
  - **Forest Green** (`#345B49`): Warna utama yang teduh untuk tombol utama, identitas brand, dan aksen alam.
  - **Sage Green** (`#A8B99A`): Warna aksen lembut untuk highlight, garis penanda, dan badge.
  - **Soft Beige** (`#E8E6D9`): Warna penyeimbang hangat bernuansa pasir pantai dan bebatuan alami.
  - **Off White** (`#F7F7F2`): Warna dasar latar belakang bersih yang nyaman di mata.
  - **Muted Green** (`#52665A`): Warna teks sekunder dan deskripsi yang teduh.
  - **Dark Forest** (`#29352D`): Warna teks judul dan latar footer elegan yang kokoh.
- **Tipografi**:
  - Headings: **Plus Jakarta Sans** (bersih, tegas, berkarakter).
  - Body Text: **Inter** (keterbacaan optimal).

---

## 🧭 Komponen & Fitur Lengkap (Hasil Audit & Pemulihan Harmonis)

### 1. NAVBAR LENGKAP & RESPONSIF
- **Logo JelajahNusa**: Dilengkapi ikon kompas alami dan tagline *"Discover Indonesia, Your Way"*.
- **Menu Navigasi**:
  1. *Beranda* (`#beranda`)
  2. *Jelajahi Wisata* (`#destinasi`)
  3. *Kategori* (`#kategori`)
  4. *Rekomendasi* (`#rekomendasi`)
  5. *Travel Planner* (`#planner`)
  6. *Tentang Kami* (`#tentang`)
- **Fitur Pengguna di Navbar**:
  - **Tombol Favorit**: Menampilkan badge jumlah destinasi favorit tersimpan secara *real-time* dan membuka modal daftar destinasi favorit.
  - **Tombol Masuk**: Membuka modal login pengguna pada tab *Masuk*.
  - **Tombol Daftar**: Membuka modal registrasi akun baru pada tab *Daftar*.

### 2. HERO SECTION & PENCARIAN DESTINASI (SEARCH DESTINATION)
- Fotografi lanskap alam Indonesia dengan *dark forest overlay* kontras tinggi.
- Headline: *"Temukan Keindahan Indonesia, Rencanakan Perjalananmu."*
- Deskripsi inspiratif dan tombol CTA (*"Jelajahi Destinasi"* & *"Buat Rencana Perjalanan"*).
- **Search Destination Bar**:
  - Input pencarian: *"Cari nama destinasi atau lokasi impian..."*.
  - Dropdown Kategori Wisata (*Semua Kategori, Wisata Alam, Wisata Pantai, Wisata Budaya, Wisata Pegunungan*).
  - Tombol **"Cari Wisata"** dengan ikon pencarian.
- **Pencarian Populer**: Tombol cepat untuk *Raja Ampat, Bali, Bromo, Labuan Bajo, Danau Toba, dan Yogyakarta*.

### 3. KATEGORI WISATA SECTION (DIPULIHKAN DENGAN GAYA NATURE-INSPIRED)
- 4 Kategori Wisata:
  - **Wisata Alam**: Rimba tropis lebat, air terjun tersembunyi, ekosistem flora fauna endemik.
  - **Wisata Pantai**: Pesisir berpasir putih lembut, perairan toska sejernih kristal, pulau karang.
  - **Wisata Budaya**: Candi megah warisan dunia, desa adat tradisional, keraton, seni tari.
  - **Wisata Pegunungan**: Puncak berkabut sejuk, kaldera vulkanik purba, jalur pendakian.
- Interaktif: Mengklik kartu kategori otomatis mengaktifkan filter kategori dan menggulir ke bagian destinasi terkait.

### 4. DESTINASI WISATA (6 DESTINASI UTAMA)
- Judul: *"Temukan Destinasi Impianmu"*.
- 6 Kartu Destinasi Ikonik:
  1. **Raja Ampat** (Papua Barat Daya)
  2. **Pulau Bali** (Bali)
  3. **Gunung Bromo** (Jawa Timur)
  4. **Labuan Bajo** (Nusa Tenggara Timur)
  5. **Danau Toba** (Sumatera Utara)
  6. **Yogyakarta & Candi** (D.I. Yogyakarta)
- Setiap kartu dilengkapi: foto destinasi, badge kategori, rating bintang, nama, lokasi dengan ikon pin, deskripsi ringkas, tag kegiatan.
- **Fitur Favorit Interaktif (Bukan Tombol Pajangan)**:
  - Ikon hati di pojok kartu destinasi.
  - Tersimpan secara persisten di browser (`localStorage`).
  - Dilengkapi notifikasi *toast* alami dan sinkronisasi otomatis dengan modal daftar favorit.
- **Tombol "Lihat Detail"**: Membuka modal popup dengan foto besar, penjelasan lengkap, dan daya tarik utama.
- **Tombol "Lihat Semua Destinasi"**: Mereset filter untuk menampilkan seluruh destinasi.

### 5. REKOMENDASI PERJALANAN (STAY, CULINARY, EXPERIENCES)
- Tab filter: *Semua*, *Stay*, *Culinary*, *Experiences*.
- **Stay**: *Bambu Indah Eco Villa Ubud*, *Plataran Komodo Resort*, *Bobocabin Signature Toba*.
- **Culinary**: *Bebek Bengil Ubud*, *Gudeg Yu Djum Wijilan*, *Kampung Ujung Seafood Market*.
- **Experiences**: *Pinisi Sunset Liveaboard Komodo*, *Private Jeep Sunrise Bromo*, *Snorkeling & Kayaking Misool*.

### 6. TRAVEL PLANNER SECTION
- Headline: *"Rencanakan Perjalananmu dalam Satu Tempat"*.
- **Mockup Itinerary Hari-demi-Hari**: Menampilkan visualisasi rencana perjalanan 3 hari di Labuan Bajo terpadu (*Stay, Culinary, Experiences*).
- **Tombol "Mulai Merencanakan"**: Membuka modal *Interactive Planner Builder* untuk memilih destinasi, durasi hari, dan tipe traveler (*Solo, Pasangan/Keluarga, Teman/Grup*), yang langsung memperbarui tampilan rencana perjalanan.

### 7. TRAVEL INSPIRATION (EDITORIAL LAYOUT)
- Artikel perjalanan bergaya majalah editorial dengan visual foto besar dan tipografi minimalis:
  - *"Menyatu dengan Alam: Panduan Slow Travel di Ubud Bali"*
  - *"Menjelajah Labuan Bajo di Luar Jalur Wisata Umum"*
  - *"Jejak Rasa Gastronomi Autentik di Jantung Yogyakarta"*

### 8. SECTION TENTANG KAMI & FOOTER
- Narasi komitmen pariwisata berkelanjutan dan ramah lingkungan.
- Navigasi lengkap, link rekomendasi, kontak resmi, media sosial, dan formulir buletin (*newsletter*).

### 9. FITUR PENGGUNA TERPADU
- **Login & Registrasi**: Form autentikasi responsif dengan peralihan tab *Masuk* dan *Daftar*.
- **Favorit Destinasi**: Penyimpanan destinasi favorit pengguna dengan tombol hapus dan notifikasi *toast* interaktif.

---

## 📁 Struktur Berkas Proyek

```text
jelajahnusa/
│
├── index.php                 # Halaman utama homepage (PHP Native)
├── README.md                 # Dokumentasi proyek & audit fitur
│
├── pages/
│   └── detail-destinasi.php  # Halaman detail destinasi (Contoh: Bali, Indonesia)
│
├── assets/
│   ├── css/
│   │   ├── style.css         # Styling global Nature-Inspired, Aesthetic, Clean
│   │   └── detail-destinasi.css # Styling kustom halaman detail destinasi
│   ├── js/
│   │   └── script.js         # Interaksi frontend (Favorit, Planner, Modals, Toast)
│   └── images/               # Direktori gambar lokal
│
├── config/                   # Folder persiapan konfigurasi database MySQL
└── admin/                    # Folder persiapan admin
```

---

## 🏝️ Halaman Detail Destinasi (Bali, Indonesia)

Halaman baru [`pages/detail-destinasi.php`](pages/detail-destinasi.php) menyajikan panduan mendalam untuk destinasi unggulan Bali:
1. **Navbar Konsisten**: Navigasi lengkap dengan akses kembali ke homepage dan indikator favorit.
2. **Destination Hero**: Banner lanskap beresolusi tinggi, informasi wilayah, rating ulasan, tombol interaktif *Simpan Favorit*, dan *Tambahkan ke Trip*.
3. **About Destination**: Narasi kekayaan alam dan budaya, 4 pilar daya tarik utama, serta panduan musim & waktu terbaik berkunjung.
4. **Popular Places**: 4 destinasi populer di Bali (*Ubud & Tegalalang*, *Pura Tanah Lot*, *Pura Tebing Uluwatu*, *Pulau Nusa Penida*).
5. **Recommended Stays**: 3 pilihan akomodasi (*Bambu Indah Eco Luxury Resort*, *The Seminyak Beach Resort & Spa*, *Munduk Moding Nature Resort*) dengan estimasi harga dan fasilitas.
6. **Local Culinary**: 3 hidangan khas (*Bebek Betutu & Sambal Matah*, *Sop Ikan Sanur*, *Sate Lilit & Lawar Bali*) lengkap dengan lokasi dan estimasi harga.
7. **Things to Do**: 4 aktivitas wisata unggulan (*Snorkeling Manta Ray*, *Tari Kecak Sunset*, *Sunrise Trekking Gunung Batur*, *Arung Jeram Sungai Ayung*).
8. **Travel Information**: Panduan komprehensif mengenai estimasi biaya (Backpacker, Menengah, Mewah), opsi transportasi, tips etika budaya pura, dan iklim musim.
9. **Create Your Trip**: Section ajakan aksi untuk menyusun itinerary dengan tombol interaktif perencana perjalanan.

---

## 🚀 Cara Menjalankan Melalui Laragon

1. Buka aplikasi **Laragon** di komputer Anda.
2. Klik tombol **"Start All"** untuk menjalankan server Apache.
3. Buka browser dan kunjungi salah satu URL:
   - **Halaman Utama (Homepage)**:
     ```url
     http://jelajahnusa.test
     ```
     atau
     ```url
     http://localhost/jelajahnusa/
     ```
   - **Halaman Detail Destinasi (Bali)**:
     ```url
     http://jelajahnusa.test/pages/detail-destinasi.php
     ```
     atau
     ```url
     http://localhost/jelajahnusa/pages/detail-destinasi.php
     ```
4. Coba seluruh fitur yang telah dipulihkan:
   - Klik menu **Kategori** pada navbar atau klik salah satu kartu kategori untuk memfilter destinasi.
   - Klik ikon hati pada kartu destinasi untuk mencoba fitur **Favorit**. Klik tombol **Favorit** di navbar untuk melihat daftar yang tersimpan.
   - Gunakan form **Cari Wisata** dengan memilih dropdown kategori wisata atau mengetik kata kunci.
   - Klik tombol **Daftar** atau **Masuk** di navbar untuk melihat modal autentikasi pada tab yang tepat.
#   J e l a j a h N u s a  
 