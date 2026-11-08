<?php
/**
 * JelajahNusa - Tambah Destinasi Baru
 * Lokasi: admin/destinasi_tambah.php
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$errors = [];
$formData = [];

// Template default untuk field JSON terstruktur agar mudah diisi oleh admin
$defaultDayaTarik = json_encode([
    [
        'judul' => 'Panorama Keindahan Alam',
        'desc'  => 'Pesona bentang alam yang masih alami dan memikat bagi setiap pengunjung.',
        'icon'  => 'bi-water'
    ],
    [
        'judul' => 'Kearifan Budaya Lokal',
        'desc'  => 'Keramahan masyarakat setempat dan tradisi budaya yang terjaga dengan baik.',
        'icon'  => 'bi-bank2'
    ],
    [
        'judul' => 'Flora & Fauna Dilindungi',
        'desc'  => 'Habitat asri berbagai spesies langka dalam kawasan konservasi alam.',
        'icon'  => 'bi-shield-check'
    ],
    [
        'judul' => 'Pemandangan Matahari Terbit & Terbenam',
        'desc'  => 'Sudut pandang terbaik untuk menikmati panorama langit fajar dan senja.',
        'icon'  => 'bi-stars'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$defaultPopularPlaces = json_encode([
    [
        'id'        => 'spot-utama',
        'nama'      => 'Spot Utama & Gardu Pandang',
        'lokasi'    => 'Kawasan Utama Destinasi',
        'foto'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'tag'       => 'Ikon Wisata',
        'deskripsi' => 'Pusat daya tarik destinasi yang menyajikan sudut panorama terluas dan memukau.'
    ],
    [
        'id'        => 'desa-wisata',
        'nama'      => 'Desa Wisata Budaya',
        'lokasi'    => 'Kawasan Pesisir / Lembah',
        'foto'      => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=800&q=80',
        'tag'       => 'Desa Adat',
        'deskripsi' => 'Kawasan permukiman tradisional dengan kerajinan tangan dan tarian lokal.'
    ],
    [
        'id'        => 'jalur-alam',
        'nama'      => 'Jalur Alam & Konservasi',
        'lokasi'    => 'Taman Nasional / Hutan Lindung',
        'foto'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        'tag'       => 'Trekking Alam',
        'deskripsi' => 'Jalur jalan setapak teduh di tengah kanopi hijau alami.'
    ],
    [
        'id'        => 'pantai-danau',
        'nama'      => 'Tepi Perairan Eksotis',
        'lokasi'    => 'Pesisir / Tepi Danau',
        'foto'      => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=800&q=80',
        'tag'       => 'Wisata Air',
        'deskripsi' => 'Hamparan tepian perairan jernih yang tenang dan menyegarkan.'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$defaultRecommendedStays = json_encode([
    [
        'id'        => 'resort-alam',
        'nama'      => 'Nusantara Eco Resort & Villa',
        'lokasi'    => 'Kawasan Pesisir / Bukit',
        'jenis'     => 'Eco Luxury Resort',
        'harga'     => 'Rp 1.850.000',
        'periode'   => '/ malam',
        'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'fasilitas' => 'Infinity View • Restoran Lokal • Pemandu Privat'
    ],
    [
        'id'        => 'hotel-tepi-pantai',
        'nama'      => 'Sunset Bay Nature Lodge',
        'lokasi'    => 'Sentra Wisata Utama',
        'jenis'     => 'Boutique Nature Lodge',
        'harga'     => 'Rp 950.000',
        'periode'   => '/ malam',
        'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        'fasilitas' => 'Balkon Alam • Sarapan Nusantara • Rental Kendaraan'
    ],
    [
        'id'        => 'homestay-desa',
        'nama'      => 'Desa Asri Traditional Homestay',
        'lokasi'    => 'Desa Wisata Setempat',
        'jenis'     => 'Traditional Homestay',
        'harga'     => 'Rp 450.000',
        'periode'   => '/ malam',
        'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
        'fasilitas' => 'Keluarga Ramah • Masakan Rumahan • Suasana Tenang'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$defaultLocalCulinary = json_encode([
    [
        'id'        => 'kuliner-khas-1',
        'nama'      => 'Olahan Rempah Tradisional Khas',
        'jenis'     => 'Makanan Pokok Tradisional',
        'lokasi'    => 'Sentra Kuliner Lokal',
        'harga'     => 'Rp 35.000 - Rp 65.000',
        'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
        'deskripsi' => 'Racikan resep bumbu warisan leluhur berbahan segar rempah asli nusantara.'
    ],
    [
        'id'        => 'kuliner-khas-2',
        'nama'      => 'Hidangan Bakar Sambal Segar',
        'jenis'     => 'Hidangan Laut / Unggas Bakar',
        'lokasi'    => 'Pasar Senggol Malam',
        'harga'     => 'Rp 40.000 - Rp 75.000',
        'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
        'deskripsi' => 'Bahan segar pilihan dibakar arang kelapa disajikan dengan aneka sambal pedas sedap.'
    ],
    [
        'id'        => 'kuliner-khas-3',
        'nama'      => 'Kudapan Manis & Seduhan Kopi',
        'jenis'     => 'Camilan & Kopi Lokal',
        'lokasi'    => 'Kedai Tradisional Setempat',
        'harga'     => 'Rp 15.000 - Rp 30.000',
        'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
        'deskripsi' => 'Kue tradisional manis bertekstur lembut ditemani seduhan kopi lokal beraroma khas.'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$defaultThingsToDo = json_encode([
    [
        'icon'      => 'bi-compass',
        'judul'     => 'Eksplorasi Jalur Panorama Alam',
        'lokasi'    => 'Area Spot Utama',
        'durasi'    => '2 - 3 Jam',
        'deskripsi' => 'Menyusuri jalur pemandangan alami untuk menikmati keasrian bentang alam nusantara.'
    ],
    [
        'icon'      => 'bi-camera',
        'judul'     => 'Berburu Foto Lanskap & Senja',
        'lokasi'    => 'Gardu Pandang Senja',
        'durasi'    => 'Sore (16:30 - 18:30)',
        'deskripsi' => 'Mengabadikan momen matahari terbenam dengan siluet bentang alam memukau.'
    ],
    [
        'icon'      => 'bi-bank2',
        'judul'     => 'Belajar Seni & Budaya Tradisional',
        'lokasi'    => 'Desa Wisata Setempat',
        'durasi'    => 'Setengah Hari',
        'deskripsi' => 'Berinteraksi langsung dengan warga lokal mempelajari kerajinan tangan dan tradisi.'
    ],
    [
        'icon'      => 'bi-water',
        'judul'     => 'Aktivitas Wisata Air / Snorkeling',
        'lokasi'    => 'Tepi Perairan Alami',
        'durasi'    => 'Tur 1 Hari',
        'deskripsi' => 'Menikmati kejernihan air dan keanekaragaman biota alam yang terpelihara.'
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$defaultTravelInfo = json_encode([
    'biaya' => [
        'title' => 'Estimasi Biaya Harian',
        'icon'  => 'bi-cash-coin',
        'items' => [
            'Backpacker / Hemat: Rp 350.000 - Rp 600.000 / hari (homestay, sewa motor, kuliner lokal)',
            'Menengah / Keluarga: Rp 900.000 - Rp 1.800.000 / hari (hotel bintang 3-4, rental mobil)',
            'Mewah / Resort: Rp 2.500.000+ / hari (resort alam bintang 5, tur privat eksklusif)'
        ]
    ],
    'transportasi' => [
        'title' => 'Pilihan Transportasi',
        'icon'  => 'bi-car-front',
        'items' => [
            'Sewa Motor Kota: Rp 75.000 - Rp 120.000 / hari (sangat lincah untuk jalan-jalan)',
            'Rental Mobil + Supir: Rp 500.000 - Rp 750.000 / hari (sangat nyaman untuk wisata keluarga)',
            'Transportasi Publik & Kapal: Tersedia dengan tarif terjangkau menuju sentra wisata'
        ]
    ],
    'tips' => [
        'title' => 'Tips Perjalanan Penting',
        'icon'  => 'bi-shield-check',
        'items' => [
            'Siapkan uang tunai rupiah secukupnya karena beberapa spot pedesaan memiliki ATM terbatas',
            'Patuhi norma adat istiadat dan etika berpakaian sopan saat mengunjungi situs sakral/budaya',
            'Bawa wadah minum isi ulang guna menjaga kebersihan lingkungan alam tanpa sampah plastik'
        ]
    ],
    'musim' => [
        'title' => 'Waktu Kunjungan Terbaik',
        'icon'  => 'bi-calendar-check',
        'items' => [
            'Musim Cerah: Cuaca sangat bersahabat untuk aktivitas luar ruangan dan eksplorasi alam',
            'Musim Tenang: Waktu ideal bagi wisatawan yang menyukai ketenangan tanpa keramaian tinggi'
        ]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Proses form saat disubmit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan CSRF tidak valid. Silakan coba kembali.';
    }

    // Ambil input form
    $formData = $_POST;

    $nama           = trim($_POST['nama'] ?? '');
    $slug           = trim($_POST['slug'] ?? '');
    $provinsi       = trim($_POST['provinsi'] ?? '');
    $wilayah        = trim($_POST['wilayah'] ?? '');
    $kategori       = trim($_POST['kategori'] ?? '');
    $rating         = null;   // Hanya bisa diperbarui via Google Places API — tidak dari POST
    $total_ulasan   = null;   // Hanya bisa diperbarui via Google Places API — tidak dari POST
    $hero_image     = trim($_POST['hero_image'] ?? '');
    $tagline        = trim($_POST['tagline'] ?? '');
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $about_heading  = trim($_POST['about_heading'] ?? '');

    $card_nama      = trim($_POST['card_nama'] ?? '');
    $card_lokasi    = trim($_POST['card_lokasi'] ?? '');
    $card_kategori  = trim($_POST['card_kategori'] ?? '');
    $card_kategori_id = trim($_POST['card_kategori_id'] ?? 'alam');
    $card_rating    = null;   // Hanya bisa diperbarui via Google Places API — tidak dari POST
    $card_image     = trim($_POST['card_image'] ?? '');
    $card_deskripsi = trim($_POST['card_deskripsi'] ?? '');
    $card_tags_raw  = trim($_POST['card_tags'] ?? '');
    $card_highlights= trim($_POST['card_highlights'] ?? '');

    // Validasi Field Wajib
    if (empty($nama)) $errors[] = 'Nama destinasi wajib diisi.';
    if (empty($provinsi)) $errors[] = 'Provinsi wajib diisi.';
    if (empty($wilayah)) $errors[] = 'Wilayah lengkap wajib diisi.';
    if (empty($hero_image)) $errors[] = 'URL Hero Image wajib diisi.';
    if (empty($deskripsi)) $errors[] = 'Deskripsi lengkap wajib diisi.';

    // Auto-generate slug jika kosong
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama), '-'));
    }

    // Periksa keunikan slug di database
    if (!empty($slug)) {
        $stmtSlug = $pdo->prepare("SELECT id FROM destinations WHERE slug = ? LIMIT 1");
        $stmtSlug->execute([$slug]);
        if ($stmtSlug->fetch()) {
            $errors[] = 'Slug URL "' . htmlspecialchars($slug) . '" sudah digunakan oleh destinasi lain. Harap gunakan slug yang unik.';
        }
    }

    // Default card data jika tidak diisi terpisah
    if (empty($card_nama)) $card_nama = $nama;
    if (empty($card_lokasi)) $card_lokasi = $provinsi;
    if (empty($card_kategori)) $card_kategori = $kategori;
    if (empty($card_image)) $card_image = $hero_image;
    if (empty($card_deskripsi)) $card_deskripsi = mb_substr($deskripsi, 0, 150) . '...';
    if (empty($about_heading)) $about_heading = 'Pesona Keindahan ' . $nama;
    if (empty($card_highlights)) $card_highlights = 'Eksplorasi alam, budaya lokal, dan pengalaman berkesan di ' . $nama;

    // Olah Card Tags menjadi array JSON
    if (!empty($card_tags_raw)) {
        $tagsArray = array_values(array_filter(array_map('trim', explode(',', $card_tags_raw))));
    } else {
        $tagsArray = [$kategori, 'Wisata', 'Nusantara'];
    }
    $card_tags = json_encode($tagsArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Olah Waktu Terbaik
    $waktu_terbaik_arr = [
        'musim' => trim($_POST['wt_musim'] ?? 'Mei - Oktober (Musim Kemarau Sejuk)'),
        'cuaca' => trim($_POST['wt_cuaca'] ?? 'Cerah dan nyaman untuk wisata luar ruangan.'),
        'tips'  => trim($_POST['wt_tips'] ?? 'Disarankan memesan tiket akomodasi lebih awal pada musim liburan.')
    ];
    $waktu_terbaik = json_encode($waktu_terbaik_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Validasi 6 Field JSON
    $jsonFields = [
        'daya_tarik'        => $_POST['daya_tarik'] ?? $defaultDayaTarik,
        'popular_places'    => $_POST['popular_places'] ?? $defaultPopularPlaces,
        'recommended_stays' => $_POST['recommended_stays'] ?? $defaultRecommendedStays,
        'local_culinary'    => $_POST['local_culinary'] ?? $defaultLocalCulinary,
        'things_to_do'      => $_POST['things_to_do'] ?? $defaultThingsToDo,
        'travel_info'       => $_POST['travel_info'] ?? $defaultTravelInfo,
    ];

    $validatedJson = [];
    foreach ($jsonFields as $key => $val) {
        $decoded = json_decode($val, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = 'Format JSON pada bagian "' . ucwords(str_replace('_', ' ', $key)) . '" tidak valid: ' . json_last_error_msg();
        } else {
            $validatedJson[$key] = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    // Jika tidak ada error, lakukan INSERT ke database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO destinations (
                slug, nama, provinsi, wilayah, kategori, rating, total_ulasan, hero_image, tagline, deskripsi, about_heading,
                card_nama, card_lokasi, card_kategori, card_kategori_id, card_rating, card_image, card_deskripsi, card_tags, card_highlights,
                google_place_id,
                daya_tarik, waktu_terbaik, popular_places, recommended_stays, local_culinary, things_to_do, travel_info
            ) VALUES (
                :slug, :nama, :provinsi, :wilayah, :kategori, :rating, :total_ulasan, :hero_image, :tagline, :deskripsi, :about_heading,
                :card_nama, :card_lokasi, :card_kategori, :card_kategori_id, :card_rating, :card_image, :card_deskripsi, :card_tags, :card_highlights,
                :google_place_id,
                :daya_tarik, :waktu_terbaik, :popular_places, :recommended_stays, :local_culinary, :things_to_do, :travel_info
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':slug'             => $slug,
                ':nama'             => $nama,
                ':provinsi'         => $provinsi,
                ':wilayah'          => $wilayah,
                ':kategori'         => $kategori,
                ':rating'           => $rating,
                ':total_ulasan'     => $total_ulasan,
                ':hero_image'       => $hero_image,
                ':tagline'          => $tagline,
                ':deskripsi'        => $deskripsi,
                ':about_heading'    => $about_heading,
                ':card_nama'        => $card_nama,
                ':card_lokasi'      => $card_lokasi,
                ':card_kategori'    => $card_kategori,
                ':card_kategori_id' => $card_kategori_id,
                ':card_rating'      => $card_rating,
                ':card_image'       => $card_image,
                ':card_deskripsi'   => $card_deskripsi,
                ':card_tags'        => $card_tags,
                ':card_highlights'  => $card_highlights,
                ':google_place_id'  => trim($_POST['google_place_id'] ?? '') ?: null,
                ':daya_tarik'       => $validatedJson['daya_tarik'],
                ':waktu_terbaik'    => $waktu_terbaik,
                ':popular_places'   => $validatedJson['popular_places'],
                ':recommended_stays'=> $validatedJson['recommended_stays'],
                ':local_culinary'   => $validatedJson['local_culinary'],
                ':things_to_do'     => $validatedJson['things_to_do'],
                ':travel_info'      => $validatedJson['travel_info'],
            ]);

            $newId = (int)$pdo->lastInsertId();
            $_SESSION['flash_success'] = 'Destinasi berhasil ditambahkan!';
            header('Location: destinasi.php');
            exit;

        } catch (Throwable $e) {
            error_log('[Admin Tambah Dest Error] ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan ke database: ' . $e->getMessage();
        }
    }
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Destinasi Baru | JelajahNusa Admin</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    :root {
      --c-forest: #345B49;
      --c-sage: #A8B99A;
      --c-beige: #E8E6D9;
      --c-offwhite: #F7F7F2;
      --c-dark-forest: #29352D;
      --sidebar-width: 270px;
    }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: var(--c-offwhite);
      color: var(--c-dark-forest);
      min-height: 100vh;
      overflow-x: hidden;
    }
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--c-dark-forest);
      color: #fff;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1040;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
    }
    .admin-brand {
      padding: 20px 20px 16px;
      display: block;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      text-decoration: none;
    }
    .admin-brand-logo {
      height: 46px;
      width: auto;
      max-width: 180px;
      display: block;
    }
    .brand-icon-box {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: rgba(168, 185, 154, 0.2);
      color: var(--c-sage);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }
    .admin-menu-list {
      padding: 20px 14px;
      list-style: none;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
      overflow-y: auto;
    }
    .admin-menu-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: 12px;
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .admin-menu-link:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.06);
    }
    .admin-menu-link.active {
      color: #fff;
      background: var(--c-forest);
      box-shadow: 0 4px 12px rgba(52, 91, 73, 0.4);
    }
    .admin-main {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    @media (max-width: 991.98px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      .admin-sidebar.show {
        transform: translateX(0);
      }
      .admin-main {
        margin-left: 0;
      }
    }
    .admin-topbar {
      height: 72px;
      background: #fff;
      border-bottom: 1px solid rgba(52, 91, 73, 0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
    }
    .admin-body {
      padding: 28px;
      flex: 1;
    }
    .content-card {
      background: #fff;
      border-radius: 20px;
      padding: 28px;
      border: 1px solid rgba(52, 91, 73, 0.08);
      box-shadow: 0 4px 20px rgba(41, 53, 45, 0.03);
      margin-bottom: 24px;
    }
    .section-header-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      border-radius: 50px;
      background: rgba(52, 91, 73, 0.08);
      color: var(--c-forest);
      font-size: 0.82rem;
      font-weight: 700;
      margin-bottom: 16px;
    }
    .btn-forest {
      background-color: var(--c-forest);
      color: #fff;
      border: none;
    }
    .btn-forest:hover {
      background-color: var(--c-dark-forest);
      color: #fff;
    }
    .json-code-editor {
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 0.85rem;
      background-color: #fbfbf9;
      line-height: 1.45;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <a href="index.php" class="admin-brand" aria-label="JelajahNusa Admin Panel">
      <img class="admin-brand-logo"
        src="../assets/images/logo-horizontal.svg"
        alt="JelajahNusa — Admin Panel"
        width="180" height="46">
      <div class="text-white-50 small mt-1" style="font-size: 0.7rem; letter-spacing: 0.06em; text-transform: uppercase; padding-left: 2px;">Admin Management</div>
    </a>

    <ul class="admin-menu-list">
      <li>
        <a href="index.php" class="admin-menu-link">
          <i class="bi bi-speedometer2"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="destinasi.php" class="admin-menu-link active">
          <i class="bi bi-map"></i>
          <span>Destinasi Wisata</span>
        </a>
      </li>
      <li>
        <a href="users.php" class="admin-menu-link">
          <i class="bi bi-people"></i>
          <span>Pengguna</span>
        </a>
      </li>
    </ul>

    <div class="p-3 border-top border-white border-opacity-10 d-flex flex-column gap-2">
      <a href="../index.php" class="btn btn-outline-light btn-sm rounded-pill py-2 w-100 text-decoration-none">
        <i class="bi bi-globe2 me-1"></i> Kembali ke Website
      </a>
      <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill py-2 w-100 text-decoration-none" style="background: rgba(220, 53, 69, 0.2); border-color: rgba(220, 53, 69, 0.4); color: #ffb8b8;">
        <i class="bi bi-box-arrow-right me-1"></i> Keluar
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link text-dark d-lg-none p-0 fs-4" type="button" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
          <i class="bi bi-list"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
          <a href="destinasi.php" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Daftar Destinasi</a>
          <span class="text-muted">/</span>
          <span class="fw-bold text-dark small">Tambah Destinasi Baru</span>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill small">
          <i class="bi bi-shield-lock-fill me-1"></i> Administrator
        </span>
      </div>
    </header>

    <main class="admin-body">
      <!-- Error Feedback -->
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
            <div>
              <strong class="d-block mb-1">Terjadi kesalahan pada data yang Anda kirimkan:</strong>
              <ul class="mb-0 ps-3 small">
                <?php foreach ($errors as $err): ?>
                  <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <form method="POST" action="destinasi_tambah.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <!-- Header Card -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <h4 class="fw-bold text-dark mb-1">Form Tambah Destinasi Baru</h4>
            <p class="text-muted small mb-0">Isi data komprehensif destinasi untuk ditampilkan pada beranda dan halaman detail.</p>
          </div>
          <div class="d-flex gap-2">
            <a href="destinasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">Batal</a>
            <button type="submit" class="btn btn-forest btn-sm rounded-pill px-4 py-2 fw-semibold">
              <i class="bi bi-check2-circle me-1"></i> Simpan Destinasi ke MySQL
            </button>
          </div>
        </div>

        <!-- 1. DATA MASTER HALAMAN DETAIL -->
        <div class="content-card">
          <span class="section-header-pill">
            <i class="bi bi-file-earmark-text"></i> 1. Informasi Halaman Detail (Master)
          </span>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nama" class="form-label small fw-bold text-dark">Nama Destinasi <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($formData['nama'] ?? ''); ?>" placeholder="Contoh: Kepulauan Derawan" required>
            </div>
            <div class="col-md-6">
              <label for="slug" class="form-label small fw-bold text-dark">Slug URL (Unik) <small class="text-muted fw-normal">(Kosongkan untuk auto-generate)</small></label>
              <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>" placeholder="Contoh: kepulauan-derawan">
            </div>
            <div class="col-md-4">
              <label for="provinsi" class="form-label small fw-bold text-dark">Provinsi <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="provinsi" name="provinsi" value="<?php echo htmlspecialchars($formData['provinsi'] ?? ''); ?>" placeholder="Contoh: Kalimantan Timur" required>
            </div>
            <div class="col-md-5">
              <label for="wilayah" class="form-label small fw-bold text-dark">Wilayah Lengkap <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="wilayah" name="wilayah" value="<?php echo htmlspecialchars($formData['wilayah'] ?? ''); ?>" placeholder="Contoh: Berau, Kalimantan Timur" required>
            </div>
            <div class="col-md-3">
              <label for="kategori" class="form-label small fw-bold text-dark">Kategori Detail <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo htmlspecialchars($formData['kategori'] ?? 'Wisata Bahari & Alam'); ?>" placeholder="Contoh: Wisata Bahari & Alam" required>
            </div>
            <div class="col-md-3">
              <label for="rating" class="form-label small fw-bold text-dark">
                Rating
                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Otomatis via Google Maps</span>
              </label>
              <input type="text" class="form-control bg-light text-muted" id="rating" name="rating"
                     value="Belum tersedia" readonly tabindex="-1"
                     title="Rating diambil otomatis dari Google Maps setelah Place ID dipilih.">
            </div>
            <div class="col-md-3">
              <label for="total_ulasan" class="form-label small fw-bold text-dark">
                Total Ulasan
                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Otomatis via Google Maps</span>
              </label>
              <input type="text" class="form-control bg-light text-muted" id="total_ulasan" name="total_ulasan"
                     value="Belum tersedia" readonly tabindex="-1"
                     title="Jumlah ulasan diambil otomatis dari Google Maps.">
            </div>
            <div class="col-md-6">
              <label for="hero_image" class="form-label small fw-bold text-dark">URL Hero Image (1920px) <span class="text-danger">*</span></label>
              <input type="url" class="form-control" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($formData['hero_image'] ?? 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1920&q=80'); ?>" placeholder="https://images.unsplash.com/..." required>
            </div>
            <div class="col-12">
              <label for="tagline" class="form-label small fw-bold text-dark">Tagline Inspiratif</label>
              <input type="text" class="form-control" id="tagline" name="tagline" value="<?php echo htmlspecialchars($formData['tagline'] ?? ''); ?>" placeholder="Contoh: Surga Bawah Laut Eksotis di Kalimantan Timur">
            </div>
            <div class="col-md-6">
              <label for="about_heading" class="form-label small fw-bold text-dark">Heading Seksi Tentang Destinasi</label>
              <input type="text" class="form-control" id="about_heading" name="about_heading" value="<?php echo htmlspecialchars($formData['about_heading'] ?? ''); ?>" placeholder="Contoh: Pesona Zamrud Tropis Kalimantan">
            </div>
            <div class="col-12">
              <label for="deskripsi" class="form-label small fw-bold text-dark">Deskripsi Lengkap Halaman Detail <span class="text-danger">*</span></label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan narasi lengkap mengenai keindahan, sejarah, dan daya pikat destinasi ini..." required><?php echo htmlspecialchars($formData['deskripsi'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <!-- 2. DATA KHUSUS KARTU BERANDA -->
        <div class="content-card">
          <span class="section-header-pill" style="background: rgba(168, 185, 154, 0.2);">
            <i class="bi bi-grid-3x3-gap"></i> 2. Informasi Kartu Beranda (Homepage Card)
          </span>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="card_nama" class="form-label small fw-bold text-dark">Nama Kartu</label>
              <input type="text" class="form-control" id="card_nama" name="card_nama" value="<?php echo htmlspecialchars($formData['card_nama'] ?? ''); ?>" placeholder="Sama dengan nama atau lebih ringkas">
            </div>
            <div class="col-md-4">
              <label for="card_lokasi" class="form-label small fw-bold text-dark">Lokasi Kartu (Ringkas)</label>
              <input type="text" class="form-control" id="card_lokasi" name="card_lokasi" value="<?php echo htmlspecialchars($formData['card_lokasi'] ?? ''); ?>" placeholder="Contoh: Kalimantan Timur">
            </div>
            <div class="col-md-4">
              <label for="card_kategori" class="form-label small fw-bold text-dark">Label Kategori Kartu</label>
              <input type="text" class="form-control" id="card_kategori" name="card_kategori" value="<?php echo htmlspecialchars($formData['card_kategori'] ?? 'Wisata Pantai'); ?>" placeholder="Contoh: Wisata Pantai">
            </div>
            <div class="col-md-4">
              <label for="card_kategori_id" class="form-label small fw-bold text-dark">ID Kategori Filter Homepage</label>
              <select class="form-select" id="card_kategori_id" name="card_kategori_id" required>
                <option value="pantai" <?php echo (($formData['card_kategori_id'] ?? '') === 'pantai') ? 'selected' : ''; ?>>pantai (Wisata Pantai)</option>
                <option value="budaya" <?php echo (($formData['card_kategori_id'] ?? '') === 'budaya') ? 'selected' : ''; ?>>budaya (Wisata Budaya)</option>
                <option value="pegunungan" <?php echo (($formData['card_kategori_id'] ?? '') === 'pegunungan') ? 'selected' : ''; ?>>pegunungan (Wisata Pegunungan)</option>
                <option value="alam" <?php echo (($formData['card_kategori_id'] ?? 'alam') === 'alam') ? 'selected' : ''; ?>>alam (Wisata Alam)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="card_rating" class="form-label small fw-bold text-dark">
                Rating Kartu
                <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Otomatis via Google Maps</span>
              </label>
              <input type="text" class="form-control bg-light text-muted" id="card_rating" name="card_rating"
                     value="Belum tersedia" readonly tabindex="-1">
            </div>
            <div class="col-md-4">
              <label for="card_image" class="form-label small fw-bold text-dark">URL Gambar Kartu (800px)</label>
              <input type="url" class="form-control" id="card_image" name="card_image" value="<?php echo htmlspecialchars($formData['card_image'] ?? 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=80'); ?>">
            </div>
            <div class="col-md-6">
              <label for="card_tags" class="form-label small fw-bold text-dark">Tags Kartu (Pisahkan dengan koma)</label>
              <input type="text" class="form-control" id="card_tags" name="card_tags" value="<?php echo htmlspecialchars($formData['card_tags'] ?? 'Diving, Pulau Karst, Penyu, Snorkeling'); ?>" placeholder="Contoh: Diving, Konservasi, Pantai">
            </div>
            <div class="col-md-6">
              <label for="card_highlights" class="form-label small fw-bold text-dark">Highlights Tempat di Kartu</label>
              <input type="text" class="form-control" id="card_highlights" name="card_highlights" value="<?php echo htmlspecialchars($formData['card_highlights'] ?? ''); ?>" placeholder="Contoh: Pulau Maratua, Danau Kakaban, Sangalaki Manta">
            </div>
            <div class="col-12">
              <label for="card_deskripsi" class="form-label small fw-bold text-dark">Ringkasan Deskripsi Kartu (1-2 Kalimat)</label>
              <textarea class="form-control" id="card_deskripsi" name="card_deskripsi" rows="2" placeholder="Ringkasan padat dan menarik untuk kartu beranda..."><?php echo htmlspecialchars($formData['card_deskripsi'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <!-- 3. KONTEN TERSTRUKTUR & JSON -->
        <div class="content-card">
          <span class="section-header-pill" style="background: rgba(41, 53, 45, 0.08); color: var(--c-dark-forest);">
            <i class="bi bi-layout-text-sidebar-reverse"></i> 3. Data Kaya & Panduan Wisata
          </span>
          <p class="text-muted small mb-3">Isi setiap kategori menggunakan formulir di bawah. Anda tidak perlu menulis kode JSON — sistem akan memprosesnya secara otomatis.</p>

          <!-- Waktu Terbaik -->
          <div class="card p-3 rounded-4 bg-light border-0 mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-brightness-high me-1 text-warning"></i> Waktu Terbaik Berkunjung</h6>
            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label small text-muted">Musim Ideal</label>
                <input type="text" class="form-control form-control-sm" name="wt_musim" value="<?php echo htmlspecialchars($formData['wt_musim'] ?? 'Mei - Oktober (Musim Kemarau)'); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Kondisi Cuaca</label>
                <input type="text" class="form-control form-control-sm" name="wt_cuaca" value="<?php echo htmlspecialchars($formData['wt_cuaca'] ?? 'Laut teduh dan jarak pandang jernih sempurna.'); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Tips Lokal</label>
                <input type="text" class="form-control form-control-sm" name="wt_tips" value="<?php echo htmlspecialchars($formData['wt_tips'] ?? 'Waktu terbaik melihat ubur-ubur tanpa sengat di Danau Kakaban.'); ?>">
              </div>
            </div>
          </div>

          <!-- Daya Tarik Utama — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="daya_tarik" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-gem text-success me-1"></i> Daya Tarik Utama
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="daya_tarik" name="daya_tarik" rows="4"><?php echo htmlspecialchars($formData['daya_tarik'] ?? $defaultDayaTarik); ?></textarea>
          </div>

          <!-- Tempat Wisata Populer — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="popular_places" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-pin-map-fill text-success me-1"></i> Tempat Wisata Populer
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="popular_places" name="popular_places" rows="4"><?php echo htmlspecialchars($formData['popular_places'] ?? $defaultPopularPlaces); ?></textarea>
          </div>

          <!-- Rekomendasi Penginapan — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="recommended_stays" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-house-heart-fill text-success me-1"></i> Rekomendasi Penginapan
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="recommended_stays" name="recommended_stays" rows="4"><?php echo htmlspecialchars($formData['recommended_stays'] ?? $defaultRecommendedStays); ?></textarea>
          </div>

          <!-- Kuliner Khas — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="local_culinary" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-cup-hot-fill text-success me-1"></i> Kuliner Khas Autentik
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="local_culinary" name="local_culinary" rows="4"><?php echo htmlspecialchars($formData['local_culinary'] ?? $defaultLocalCulinary); ?></textarea>
          </div>

          <!-- Aktivitas Wisata — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="things_to_do" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-compass-fill text-success me-1"></i> Aktivitas &amp; Pengalaman
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="things_to_do" name="things_to_do" rows="4"><?php echo htmlspecialchars($formData['things_to_do'] ?? $defaultThingsToDo); ?></textarea>
          </div>

          <!-- Panduan Perjalanan — diganti form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="travel_info" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-map-fill text-success me-1"></i> Panduan Perjalanan (Biaya, Transportasi, Tips, Musim)
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="travel_info" name="travel_info" rows="4"><?php echo htmlspecialchars($formData['travel_info'] ?? $defaultTravelInfo); ?></textarea>
          </div>
        </div>

        <!-- Submit Bar -->
        <div class="d-flex justify-content-between align-items-center pb-5">
          <a href="destinasi.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
          </a>
          <button type="submit" class="btn btn-forest rounded-pill px-5 py-2 fw-semibold shadow">
            <i class="bi bi-check2-circle me-1"></i> Simpan Destinasi ke MySQL
          </button>
        </div>
      </form>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- JSON Form Builder: form visual menggantikan textarea JSON -->
  <script src="json-form-builder.js"></script>
  <!-- Google Place Picker -->
  <script src="google-place-picker.js"></script>
  <script>
    // Auto slug generator jika slug belum diisi manual
    const namaInput = document.getElementById('nama');
    const slugInput = document.getElementById('slug');
    if (namaInput && slugInput) {
      namaInput.addEventListener('blur', function() {
        if (!slugInput.value.trim()) {
          slugInput.value = this.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        }
      });
    }

    // Toggle Mobile Sidebar
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    if (sidebarToggleBtn && adminSidebar) {
      sidebarToggleBtn.addEventListener('click', () => {
        adminSidebar.classList.toggle('show');
      });
      document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && !adminSidebar.contains(e.target) && !sidebarToggleBtn.contains(e.target)) {
          adminSidebar.classList.remove('show');
        }
      });
    }
  </script>
</body>
</html>
