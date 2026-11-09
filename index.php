<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth_base_url = 'auth/';
require_once __DIR__ . '/config/database.php';

// Sanitasi parameter redirect (Mencegah Open Redirect)
$redirect_param = trim($_GET['redirect'] ?? '');
$safe_redirect = '';
if (!empty($redirect_param)) {
    // Tolak skema protokol eksternal, protocol-relative, atau skema berbahaya
    if (
        !preg_match('/^(https?:)?\/\/|^[a-zA-Z0-9+.-]+:|^\\\\/i', $redirect_param) &&
        (
            $redirect_param === 'pages/favorit.php' ||
            $redirect_param === 'pages/rencana-perjalanan.php' ||
            strpos($redirect_param, 'pages/detail-destinasi.php') === 0 ||
            $redirect_param === 'index.php'
        )
    ) {
        $safe_redirect = $redirect_param;
    }
}
/**
 * JelajahNusa - Travel Guide & Travel Planner
 * File Utama: index.php
 * Konsep: Nature-Inspired, Aesthetic, Minimalist, Clean
 * Palet Warna: Forest Green (#345B49), Sage Green (#A8B99A), Soft Beige (#E8E6D9), Off White (#F7F7F2), Muted Green (#52665A), Dark Forest (#29352D)
 */

// ==========================================================================
// 1. DATA KATEGORI WISATA (PHP Native Array)
// ==========================================================================
$kategori_wisata = [
    [
        'id'        => 'alam',
        'nama'      => 'Wisata Alam',
        'icon'      => 'bi-tree-fill',
        'deskripsi' => 'Rimba tropis lebat, air terjun tersembunyi, dan ekosistem flora fauna endemik nusantara.',
        'foto'      => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=800&q=80',
        'jumlah'    => '180+ Destinasi'
    ],
    [
        'id'        => 'pantai',
        'nama'      => 'Wisata Pantai',
        'icon'      => 'bi-water',
        'deskripsi' => 'Pesisir berpasir putih lembut, perairan toska sejernih kristal, dan kepulauan karang eksotis.',
        'foto'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'jumlah'    => '145+ Destinasi'
    ],
    [
        'id'        => 'budaya',
        'nama'      => 'Wisata Budaya',
        'icon'      => 'bi-bank2',
        'deskripsi' => 'Candi megah warisan dunia, desa adat tradisional, keraton, dan seni pertunjukan luhur.',
        'foto'      => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
        'jumlah'    => '95+ Destinasi'
    ],
    [
        'id'        => 'pegunungan',
        'nama'      => 'Wisata Pegunungan',
        'icon'      => 'bi-image-alt',
        'deskripsi' => 'Puncak berkabut sejuk, kaldera vulkanik purba spektakuler, dan jalur pendakian legendaris.',
        'foto'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        'jumlah'    => '110+ Destinasi'
    ]
];

// ==========================================================================
// 2. DATA DESTINASI WISATA (MySQL Primary dengan Fallback PHP Native Array)
// ==========================================================================
$destinasi_fallback = [
    [
        'id'          => 1,
        'nama'        => 'Raja Ampat',
        'lokasi'      => 'Papua Barat Daya',
        'kategori'    => 'Wisata Pantai',
        'kategori_id' => 'pantai',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Gugusan pulau karst zamrud yang dikelilingi perairan toska sejernih kristal dengan keanekaragaman hayati laut terkaya di dunia.',
        'tags'        => ['Menyelam', 'Pianemo', 'Karst', 'Konservasi'],
        'highlights'  => 'Pianemo Lagoon, Kepulauan Wayag, Selam Permukaan Terumbu Karang, Pasir Timbul Mansuar'
    ],
    [
        'id'          => 2,
        'nama'        => 'Pulau Bali',
        'lokasi'      => 'Bali',
        'kategori'    => 'Wisata Budaya',
        'kategori_id' => 'budaya',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Harmoni magis antara keteduhan terasering sawah Ubud, pesona pantai selancar dunia, dan keluhuran tradisi spiritual pura.',
        'tags'        => ['Ubud Sawah', 'Pura Luhur', 'Pantai', 'Seni Tari'],
        'highlights'  => 'Terasering Tegalalang, Pura Uluwatu saat Senja, Desa Wisata Penglipuran, Jalur Pesisir Sanur'
    ],
    [
        'id'          => 3,
        'nama'        => 'Gunung Bromo',
        'lokasi'      => 'Jawa Timur',
        'kategori'    => 'Wisata Pegunungan',
        'kategori_id' => 'pegunungan',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Bentang alam kaldera purba megah dengan panorama matahari terbit berkabut di atas hamparan lautan pasir vulkanik yang mistis.',
        'tags'        => ['Matahari Terbit', 'Lautan Pasir', 'Kawah Bromo', 'Pendakian'],
        'highlights'  => 'Pemandangan Matahari Terbit Penanjakan 1, Pasir Berbisik, Pura Poten, Bukit Teletubbies Bromo'
    ],
    [
        'id'          => 4,
        'nama'        => 'Labuan Bajo',
        'lokasi'      => 'Nusa Tenggara Timur',
        'kategori'    => 'Wisata Alam',
        'kategori_id' => 'alam',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Pintu gerbang penjelajahan pulau-pulau savana dramatis, habitat purba komodo, serta keindahan unik pantai Pink Beach.',
        'tags'        => ['Pulau Padar', 'Komodo', 'Pink Beach', 'Kapal Wisata Inap'],
        'highlights'  => 'Pendakian Puncak Padar, Berlayar dengan Kapal Pinisi, Selam Permukaan di Manta Point, Senja di Pulau Kalong'
    ],
    [
        'id'          => 5,
        'nama'        => 'Danau Toba',
        'lokasi'      => 'Sumatera Utara',
        'kategori'    => 'Wisata Alam',
        'kategori_id' => 'alam',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Danau vulkanik raksasa yang tenang di tengah perbukitan hijau pinus, kaya akan warisan tradisi leluhur suku Batak di Pulau Samosir.',
        'tags'        => ['Geopark', 'Pulau Samosir', 'Budaya Batak', 'Kaldera'],
        'highlights'  => 'Desa Wisata Tomok, Bukit Holbung, Air Terjun Sipiso-piso, Pemandian Air Panas Pangururan'
    ],
    [
        'id'          => 6,
        'nama'        => 'Yogyakarta & Candi',
        'lokasi'      => 'D.I. Yogyakarta',
        'kategori'    => 'Wisata Budaya',
        'kategori_id' => 'budaya',
        'rating'      => null,
        'foto'        => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=800&q=80',
        'deskripsi'   => 'Jantung kebudayaan Jawa yang anggun, rumah bagi mahakarya dunia Candi Prambanan & Borobudur serta kehangatan suasana kota seni.',
        'tags'        => ['Warisan Dunia', 'Candi Megah', 'Keraton', 'Kuliner Khas'],
        'highlights'  => 'Kompleks Candi Prambanan, Candi Borobudur, Keraton Ngayogyakarta, Kampung Batik Kotagede'
    ]
];

$destinasi_wisata = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT d.id, d.card_nama AS nama, d.card_lokasi AS lokasi, d.card_kategori AS kategori, d.card_kategori_id AS kategori_id, review_summary.rating, COALESCE(review_summary.total_ulasan, 0) AS total_ulasan, d.card_image AS foto, d.card_deskripsi AS deskripsi, d.card_tags AS tags, d.card_highlights AS highlights FROM destinations d LEFT JOIN (SELECT destination_id, AVG(rating) AS rating, COUNT(*) AS total_ulasan FROM destination_reviews WHERE status <> 'hidden' GROUP BY destination_id) review_summary ON review_summary.destination_id = d.id ORDER BY d.id ASC");
        $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($dbRows)) {
            foreach ($dbRows as $row) {
                if (isset($row['tags']) && is_string($row['tags'])) {
                    $row['tags'] = json_decode($row['tags'], true) ?? [];
                }
                $destinasi_wisata[] = $row;
            }
        }
    }
} catch (Throwable $e) {
    error_log('[Homepage Destinations DB Error] ' . $e->getMessage());
}

// Fallback jika database belum ada data atau koneksi terkendala
if (empty($destinasi_wisata)) {
    $destinasi_wisata = $destinasi_fallback;
}

// ==========================================================================
// 3. DATA REKOMENDASI PERJALANAN
// ==========================================================================
$rekomendasi_perjalanan = [
    // --- STAY ---
    [
        'tipe'       => 'stay',
        'tipe_label' => 'Penginapan',
        'nama'       => 'Bambu Indah Eco Villa',
        'lokasi'     => 'Ubud, Bali',
        'foto'       => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Resor ramah lingkungan dengan arsitektur bambu alami yang menghadap langsung ke lembah Sungai Ayung.',
        'highlight'  => 'Resor ramah lingkungan • Kolam renang pribadi • Pemandangan sungai',
        'rating'     => 4.9
    ],
    [
        'tipe'       => 'stay',
        'tipe_label' => 'Penginapan',
        'nama'       => 'Plataran Komodo Resort',
        'lokasi'     => 'Labuan Bajo, NTT',
        'foto'       => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Vila mewah tepi pantai berpasir putih pribadi dengan akses langsung untuk menjelajahi perairan Komodo.',
        'highlight'  => 'Vila tepi pantai • Dek untuk menikmati senja • Dermaga pribadi',
        'rating'     => 4.8
    ],
    [
        'tipe'       => 'stay',
        'tipe_label' => 'Penginapan',
        'nama'       => 'Bobocabin Signature Toba',
        'lokasi'     => 'Danau Toba, Sumatera Utara',
        'foto'       => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Kabin modern berkonsep kemah mewah di perbukitan pinus yang menyajikan panorama langsung ke danau kaldera.',
        'highlight'  => 'Kabin Alam • Jendela Pintar • Api Unggun',
        'rating'     => 4.7
    ],

    // --- CULINARY ---
    [
        'tipe'       => 'culinary',
        'tipe_label' => 'Kuliner',
        'nama'       => 'Bebek Bengil & Sambal Matah',
        'lokasi'     => 'Ubud, Bali',
        'foto'       => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Kuliner bebek goreng renyah legendaris khas Bali yang disajikan dengan aneka racikan sambal matah autentik.',
        'highlight'  => 'Kuliner Ikonik • Pemandangan Tepi Sawah • Autentik',
        'rating'     => 4.8
    ],
    [
        'tipe'       => 'culinary',
        'tipe_label' => 'Kuliner',
        'nama'       => 'Gudeg Yu Djum Wijilan',
        'lokasi'     => 'Yogyakarta',
        'foto'       => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Olahan nangka muda manis gurih yang dimasak dengan tungku kayu bakar tradisional sejak puluhan tahun.',
        'highlight'  => 'Warisan Budaya • Masakan Tungku • Oleh-oleh',
        'rating'     => 4.9
    ],
    [
        'tipe'       => 'culinary',
        'tipe_label' => 'Kuliner',
        'nama'       => 'Pasar Hidangan Laut Kampung Ujung',
        'lokasi'     => 'Labuan Bajo, NTT',
        'foto'       => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Pusat kuliner hidangan laut segar tangkapan nelayan lokal yang dibakar dengan bumbu rempah khas pesisir.',
        'highlight'  => 'Ikan Bakar Segar • Suasana Tepi Laut • Ramah Keluarga',
        'rating'     => 4.7
    ],

    // --- EXPERIENCES ---
    [
        'tipe'       => 'experiences',
        'tipe_label' => 'Aktivitas',
        'nama'       => 'Pelayaran Pinisi Senja 3 Hari 2 Malam',
        'lokasi'     => 'Taman Nasional Komodo, NTT',
        'foto'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Pengalaman berlayar santai di atas kapal pinisi kayu tradisional menyusuri pulau-pulau savana eksotis.',
        'highlight'  => 'Berlayar dengan pinisi • Selam permukaan bersama pari manta • Pelayaran saat senja',
        'rating'     => 5.0
    ],
    [
        'tipe'       => 'experiences',
        'tipe_label' => 'Aktivitas',
        'nama'       => 'Tur Jeep Pribadi Menyaksikan Matahari Terbit di Bromo',
        'lokasi'     => 'Bromo Tengger Semeru, Jatim',
        'foto'       => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Petualangan jeep 4x4 melintasi lautan pasir purba menuju titik pandang terbaik matahari terbit.',
        'highlight'  => 'Tur Jip 4x4 • Matahari Terbit Keemasan • Pemandu Lokal',
        'rating'     => 4.9
    ],
    [
        'tipe'       => 'experiences',
        'tipe_label' => 'Aktivitas',
        'nama'       => 'Selam Permukaan & Kayak Karst',
        'lokasi'     => 'Misool, Raja Ampat',
        'foto'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
        'deskripsi'  => 'Menjelajahi laguna tersembunyi dengan kayak transparan dan melihat terumbu karang warna-warni terlindungi.',
        'highlight'  => 'Ekowisata Bahari • Kayak Transparan • Pemandu Adat',
        'rating'     => 4.9
    ]
];

// ==========================================================================
// 4. DATA INSPIRASI WISATA (Travel Inspiration - Editorial Layout)
// ==========================================================================
$travel_inspirations = [
    [
        'judul'     => 'Menyatu dengan Alam: Panduan Wisata Santai di Ubud Bali',
        'kategori'  => 'Kehidupan Santai & Alam',
        'tanggal'   => '28 Oktober 2026',
        'baca'      => '5 menit membaca',
        'foto'      => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=900&q=80',
        'ringkasan' => 'Bagaimana menikmati keheningan pagi di terasering sawah, berinteraksi dengan komunitas pengrajin lokal, dan merasakan kedamaian batin tanpa terburu-buru.'
    ],
    [
        'judul'     => 'Menjelajah Labuan Bajo di Luar Jalur Wisata Umum',
        'kategori'  => 'Panduan Kepulauan',
        'tanggal'   => '20 Oktober 2026',
        'baca'      => '6 menit membaca',
        'foto'      => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=900&q=80',
        'ringkasan' => 'Eksplorasi teluk-teluk tersembunyi, tradisi kampung tenun Manggarai, dan cara cerdas memilih kapal pinisi yang ramah terhadap ekosistem laut.'
    ],
    [
        'judul'     => 'Jejak Rasa Gastronomi Autentik di Jantung Yogyakarta',
        'kategori'  => 'Wisata Kuliner',
        'tanggal'   => '14 Oktober 2026',
        'baca'      => '4 menit membaca',
        'foto'      => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=80',
        'ringkasan' => 'Menelusuri resep dapur keraton kuno, kopi jos arang legendaris, dan filosofi kebersahajaan yang terkandung dalam setiap racikan kuliner tradisional.'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="content-language" content="id">
  <title>JelajahNusa — Jelajahi Indonesia dengan Caramu</title>
  <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
  <meta name="jn-auth-status" content="<?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>">
  <meta name="description" content="Platform digital panduan wisata dan rencana perjalanan untuk menemukan destinasi wisata Indonesia, rekomendasi penginapan, kuliner khas, dan menyusun rencana liburan impian.">
  <meta name="keywords" content="JelajahNusa, Panduan Wisata Indonesia, Rencana Perjalanan, Wisata Indonesia, Raja Ampat, Bali, Bromo, Labuan Bajo, Danau Toba, Yogyakarta">

  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Nature-Inspired CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- ======================================================================
       1. NAVBAR (Lengkap dengan Beranda, Jelajahi Wisata, Kategori, Rekomendasi, Planner, Tentang Kami, Favorit, Masuk & Daftar)
       ====================================================================== -->
  <nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">
      
      <!-- Brand Logo -->
      <a class="navbar-brand" href="#beranda" aria-label="JelajahNusa — Kembali ke Beranda">
        <img class="brand-logo-img"
          src="assets/images/logo-horizontal.svg"
          alt="JelajahNusa — Jelajahi Indonesia dengan Caramu"
          width="240" height="56">
      </a>

      <!-- Mobile Toggler -->
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list"></i>
      </button>

      <!-- Menus & Action -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" href="#beranda">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#destinasi">Jelajahi Wisata</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#kategori">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#rekomendasi">Rekomendasi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#planner">Rencana Perjalanan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#tentang">Tentang Kami</a>
          </li>
        </ul>

        <!-- Action Buttons (Favorit, Masuk, Daftar) -->
        <div class="d-flex align-items-center gap-2">
          
          <!-- Tombol Favorit -->
          <button type="button" class="nav-fav-pill" data-bs-toggle="modal" data-bs-target="#favoritesModal" id="navFavBtn" title="Destinasi Favorit Saya" aria-label="Favorit">
            <i class="bi bi-heart-fill text-danger"></i>
            <span class="d-none d-xl-inline">Favorit</span>
            <span class="nav-fav-badge" id="navFavCount">0</span>
          </button>

          <!-- Tombol Masuk & Daftar / Profil Sesi -->
          <?php if (isset($_SESSION['user'])): ?>
            <div class="dropdown">
              <button class="btn btn-outline-forest btn-sm rounded-pill dropdown-toggle d-flex align-items-center gap-2 px-3 py-1" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle text-success"></i>
                <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuBtn" style="border-radius: var(--radius-md);">
                <li><span class="dropdown-item-text text-muted small"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($_SESSION['user']['email']); ?></span></li>
                <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-success fw-bold small" href="admin/"><i class="bi bi-speedometer2 me-2"></i>Dasbor Administrator</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item fw-semibold" href="pages/profil.php" style="color:var(--c-dark-forest);">
                    <i class="bi bi-person-gear me-2"></i>Pengaturan Akun
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?php echo $auth_base_url; ?>logout.php" id="navLogoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
              </ul>
            </div>
          <?php else: ?>
            <!-- Tombol Masuk -->
            <button type="button" class="nav-auth-btn-login" data-bs-toggle="modal" data-bs-target="#authModal" id="navLoginBtn">
              Masuk
            </button>

            <!-- Tombol Daftar -->
            <button type="button" class="btn btn-forest btn-sm-nature" data-bs-toggle="modal" data-bs-target="#authModal" id="navRegisterBtn">
              <i class="bi bi-person-plus"></i>
              <span>Daftar</span>
            </button>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </nav>

  <!-- ======================================================================
       2. HERO SECTION & SEARCH DESTINATION
       ====================================================================== -->
  <header id="beranda" class="hero-section">
    <div class="hero-background"></div>
    <div class="hero-overlay"></div>

    <div class="container position-relative">
      <div class="hero-content">
        
        <!-- Nature-inspired Pill Badge -->
        <div class="hero-badge">
          <i class="bi bi-tree"></i>
          <span>Platform Panduan Wisata & Rencana Perjalanan Indonesia</span>
        </div>

        <!-- Headline & Description -->
        <h1 class="hero-title">
          Temukan Keindahan Indonesia, <em>Rencanakan Perjalananmu.</em>
        </h1>
        <p class="hero-desc">
          Jelajahi destinasi pilihan, temukan tempat menginap terbaik, nikmati kuliner lokal, dan susun perjalanan impianmu bersama JelajahNusa.
        </p>

        <!-- CTA Action Buttons -->
        <div class="hero-actions">
          <a href="#destinasi" class="btn btn-hero-solid">
            <i class="bi bi-geo-alt"></i>
            <span>Jelajahi Destinasi</span>
          </a>
          <a href="#planner" class="btn btn-hero-glass">
            <i class="bi bi-calendar-week"></i>
            <span>Buat Rencana Perjalanan</span>
          </a>
        </div>

        <!-- SEARCH DESTINATION: Input, Dropdown Kategori & Tombol Cari Wisata -->
        <div class="hero-search-container">
          <form id="heroSearchForm" class="hero-search-form">
            
            <!-- Input nama destinasi atau lokasi -->
            <div class="hero-search-input-group">
              <i class="bi bi-search hero-search-icon"></i>
              <input type="text" id="heroSearchInput" class="hero-search-input" placeholder="Cari nama destinasi atau lokasi impian...">
            </div>

            <div class="hero-search-divider"></div>

            <!-- Dropdown kategori wisata -->
            <div>
              <select id="heroCategorySelect" class="hero-search-select" aria-label="Pilih Kategori Wisata">
                <option value="">Semua Kategori</option>
                <option value="alam">Wisata Alam</option>
                <option value="pantai">Wisata Pantai</option>
                <option value="budaya">Wisata Budaya</option>
                <option value="pegunungan">Wisata Pegunungan</option>
              </select>
            </div>

            <!-- Tombol Cari Wisata -->
            <button type="submit" class="btn btn-forest btn-sm-nature">
              <i class="bi bi-search me-1"></i>
              <span>Cari Wisata</span>
            </button>

          </form>
        </div>

        <!-- Pencarian Populer: Raja Ampat, Bali, Bromo, Labuan Bajo, Danau Toba, Yogyakarta -->
        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mt-3 pt-1">
          <span class="text-white-50 small">Pencarian populer:</span>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Raja Ampat">Raja Ampat</a>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Bali">Bali</a>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Bromo">Bromo</a>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Labuan Bajo">Labuan Bajo</a>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Danau Toba">Danau Toba</a>
          <a href="#" class="hero-quick-tag text-white-50 text-decoration-none small px-2 py-1 rounded bg-black bg-opacity-25" data-search="Yogyakarta">Yogyakarta</a>
        </div>

      </div>
    </div>
  </header>

  <!-- ======================================================================
       3. KATEGORI WISATA SECTION (DIPULIHKAN DENGAN GAYA NATURE-INSPIRED)
       ====================================================================== -->
  <section id="kategori" class="section-kategori">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-collection"></i> Ragam Pengalaman
        </span>
        <h2 class="section-title">Kategori Wisata Nusantara</h2>
        <p class="section-subtitle">
          Pilih jenis liburan impianmu, mulai dari ketenangan rimba hijau alami hingga hembusan angin pesisir pantai tropis.
        </p>
      </div>

      <!-- Categories Grid (4 Kategori) -->
      <div class="row g-4">
        <?php foreach ($kategori_wisata as $cat): ?>
          <div class="col-lg-3 col-md-6">
            <a href="#destinasi" 
               class="nature-category-card category-card-trigger" 
               data-category-id="<?php echo htmlspecialchars($cat['id']); ?>"
               title="Filter <?php echo htmlspecialchars($cat['nama']); ?>">
              
              <!-- Background Image -->
              <div class="category-card-bg" style="background-image: url('<?php echo htmlspecialchars($cat['foto']); ?>');"></div>
              <div class="category-card-overlay"></div>

              <!-- Card Body -->
              <div class="category-card-body">
                <div class="category-icon-bubble">
                  <i class="bi <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                </div>
                <h3 class="category-card-title"><?php echo htmlspecialchars($cat['nama']); ?></h3>
                <p class="category-card-desc"><?php echo htmlspecialchars($cat['deskripsi']); ?></p>
                <div class="category-card-count">
                  <span><?php echo htmlspecialchars($cat['jumlah']); ?></span>
                  <i class="bi bi-arrow-right-short fs-5"></i>
                </div>
              </div>

            </a>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       4. DESTINASI WISATA (6 Destinasi Populer, Kartu Visual & Favorit)
       ====================================================================== -->
  <section id="destinasi" class="section-destinasi">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-compass"></i> Eksplorasi Kepulauan
        </span>
        <h2 class="section-title">Temukan Destinasi Impianmu</h2>
        <p class="section-subtitle">
          Pilihan destinasi terbaik dari pesona gugusan pulau bahari hingga kesejukan kaldera pegunungan nusantara.
        </p>
      </div>

      <!-- Notice Destinasi Tidak Ditemukan -->
      <div id="noDestinationsNotice" class="alert alert-light border text-center py-5 d-none">
        <i class="bi bi-search fs-2 text-muted d-block mb-2"></i>
        <h5 class="fw-bold text-dark">Destinasi Tidak Ditemukan</h5>
        <p class="text-muted small">Coba ubah kata kunci atau bersihkan filter pencarian.</p>
        <button id="resetDestFilterBtn" class="btn btn-outline-forest btn-sm">
          Tampilkan Semua Destinasi
        </button>
      </div>

      <!-- Destination Grid (Minimal 6 Kartu) -->
      <div class="row g-4" id="destinationGrid">
        <?php foreach ($destinasi_wisata as $dest): ?>
          <div class="col-lg-4 col-md-6 destination-card-col"
               data-title="<?php echo htmlspecialchars($dest['nama']); ?>"
               data-location="<?php echo htmlspecialchars($dest['lokasi']); ?>"
               data-category="<?php echo htmlspecialchars($dest['kategori_id']); ?>"
               data-tags="<?php echo htmlspecialchars(implode(' ', $dest['tags'])); ?>">
            
            <div class="destination-card">
              <!-- Image Box -->
              <div class="destination-img-box">
                <img src="<?php echo htmlspecialchars($dest['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($dest['nama']); ?>" 
                     class="destination-img" 
                     loading="lazy">
                
                <span class="destination-category-badge">
                  <?php echo htmlspecialchars($dest['kategori']); ?>
                </span>

                <!-- Tombol Favorit Interaktif (Fitur Pengguna) -->
                <button type="button" 
                        class="btn-card-fav" 
                        data-id="<?php echo htmlspecialchars($dest['id']); ?>"
                        data-nama="<?php echo htmlspecialchars($dest['nama']); ?>"
                        data-lokasi="<?php echo htmlspecialchars($dest['lokasi']); ?>"
                        data-foto="<?php echo htmlspecialchars($dest['foto']); ?>"
                        title="Simpan ke favorit" 
                        aria-label="Simpan ke favorit">
                  <i class="bi bi-heart"></i>
                </button>
              </div>

              <!-- Body -->
              <div class="destination-content">
                <div class="destination-meta-row">
                  <span class="destination-location">
                    <i class="bi bi-geo-alt"></i>
                    <?php echo htmlspecialchars($dest['lokasi']); ?>
                  </span>
                  <?php if ($dest['rating'] !== null): ?>
                  <span class="destination-rating-inline" title="Penilaian dari ulasan pengguna">
                    <i class="bi bi-star-fill text-warning"></i>
                    <?php echo number_format((float)$dest['rating'], 1); ?>
                    <span class="text-muted small">(<?php echo number_format((int)($dest['total_ulasan'] ?? 0)); ?>)</span>
                  </span>
                  <?php else: ?>
                  <span class="destination-rating-inline text-muted" style="font-size:0.75rem;" title="Belum tersedia">
                    <i class="bi bi-star text-muted me-1"></i>Belum tersedia
                  </span>
                  <?php endif; ?>
                </div>

                <h3 class="destination-name">
                  <?php echo htmlspecialchars($dest['nama']); ?>
                </h3>

                <p class="destination-summary">
                  <?php echo htmlspecialchars($dest['deskripsi']); ?>
                </p>

                <div class="destination-bottom">
                  <div class="d-flex gap-1 flex-wrap">
                    <?php foreach (array_slice($dest['tags'], 0, 2) as $tag): ?>
                      <span class="destination-tag-item">#<?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                  </div>

                  <!-- Tombol Lihat Detail -->
<a href="pages/detail-destinasi.php?id=<?php echo $dest['id']; ?>"
   class="btn-detail-link">
    <span>Lihat Detail</span>
    <i class="bi bi-arrow-right-short fs-6"></i>
</a>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Action: Lihat Semua Destinasi -->
      <div class="text-center mt-5 pt-3">
        <a href="#destinasi" id="viewAllDestBtn" class="btn btn-outline-forest px-4 py-2">
          <i class="bi bi-grid-3x3-gap"></i>
          <span>Lihat Semua Destinasi</span>
        </a>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       5. TRAVEL RECOMMENDATIONS (Stay, Culinary, Experiences)
       ====================================================================== -->
  <section id="rekomendasi" class="section-rekomendasi">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-4">
        <span class="section-label">
          <i class="bi bi-stars"></i> Kurasi Pengalaman Terbaik
        </span>
        <h2 class="section-title">Rekomendasi Perjalanan Pilihan</h2>
        <p class="section-subtitle">
          Temukan akomodasi menenangkan, kekayaan kuliner khas nusantara, serta pengalaman wisata autentik untuk menyempurnakan liburanmu.
        </p>
      </div>

      <!-- Filter Tabs: Semua, Stay, Culinary, Experiences -->
      <div class="text-center">
        <div class="rekomendasi-nav">
          <button type="button" class="rekomendasi-tab-btn active" data-category="all">
            <i class="bi bi-collection"></i> Semua
          </button>
          <button type="button" class="rekomendasi-tab-btn" data-category="stay">
            <i class="bi bi-house-door"></i> Penginapan
          </button>
          <button type="button" class="rekomendasi-tab-btn" data-category="culinary">
            <i class="bi bi-cup-hot"></i> Kuliner
          </button>
          <button type="button" class="rekomendasi-tab-btn" data-category="experiences">
            <i class="bi bi-compass"></i> Aktivitas
          </button>
        </div>
      </div>

      <!-- Recommendations Grid -->
      <div class="row g-4" id="recommendationGrid">
        <?php foreach ($rekomendasi_perjalanan as $rec): ?>
          <div class="col-lg-4 col-md-6 recommendation-card-col" data-type="<?php echo htmlspecialchars($rec['tipe']); ?>">
            <div class="recommendation-card">
              
              <div class="rec-img-wrap">
                <img src="<?php echo htmlspecialchars($rec['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($rec['nama']); ?>" 
                     class="rec-img" 
                     loading="lazy">
                <span class="rec-badge-type">
                  <?php echo htmlspecialchars($rec['tipe_label']); ?>
                </span>
              </div>

              <div class="rec-body">
                <span class="rec-location">
                  <i class="bi bi-geo-alt"></i>
                  <?php echo htmlspecialchars($rec['lokasi']); ?>
                </span>

                <h3 class="rec-title">
                  <?php echo htmlspecialchars($rec['nama']); ?>
                </h3>

                <p class="rec-desc">
                  <?php echo htmlspecialchars($rec['deskripsi']); ?>
                </p>

                <div class="rec-footer">
                  <span class="rec-highlight">
                    <i class="bi bi-check2-circle text-success me-1"></i>
                    <?php echo htmlspecialchars($rec['highlight']); ?>
                  </span>
                  <span class="text-muted small">
                    <i class="bi bi-star-fill text-warning me-1"></i><?php echo number_format($rec['rating'], 1); ?>
                  </span>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       6. TRAVEL PLANNER SECTION
       ====================================================================== -->
  <section id="planner" class="section-planner">
    <div class="container">
      <div class="row align-items-center g-5">
        
        <!-- Left Column: Explanation & Features -->
        <div class="col-lg-6">
          <span class="section-label section-label-forest">
            <i class="bi bi-calendar2-check"></i> Rencana Perjalanan Terpadu
          </span>
          <h2 class="section-title mb-3">
            Rencanakan Perjalananmu dalam Satu Tempat
          </h2>
          <p class="text-muted mb-4 lead" style="font-size: 1.05rem;">
            Tidak perlu repot membuka banyak aplikasi terpisah. JelajahNusa membantu mengumpulkan destinasi pilihan, penginapan ramah lingkungan, kuliner khas, dan aktivitas wisata favorit dalam satu rencana perjalanan yang rapi dan terstruktur.
          </p>

          <!-- 3 Feature Points -->
          <div class="planner-feature-item">
            <div class="planner-feature-icon">
              <i class="bi bi-map"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-1">Jadwal Perjalanan Hari demi Hari</h5>
              <p class="text-muted small mb-0">Atur jadwal dari pagi hingga malam dengan waktu perjalanan dan rute yang logis.</p>
            </div>
          </div>

          <div class="planner-feature-item">
            <div class="planner-feature-icon">
              <i class="bi bi-sliders2"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-1">Disesuaikan untuk Semua Kalangan</h5>
              <p class="text-muted small mb-0">Cocok untuk perjalanan solo mandiri, liburan romantis pasangan, maupun rekreasi keluarga.</p>
            </div>
          </div>

          <div class="planner-feature-item">
            <div class="planner-feature-icon">
              <i class="bi bi-bookmark-check"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-1">Kurasi Terpercaya & Fleksibel</h5>
              <p class="text-muted small mb-0">Tambahkan atau ubah destinasi kapan pun dengan rekomendasi lokal yang telah diverifikasi.</p>
            </div>
          </div>

          <!-- Tombol Mulai Merencanakan Perjalanan -->
          <div class="mt-4 pt-2">
            <button type="button" class="btn btn-forest px-4 py-3" data-bs-toggle="modal" data-bs-target="#plannerModal">
              <i class="bi bi-plus-circle me-1"></i>
              <span>Mulai Merencanakan</span>
            </button>
          </div>
        </div>

        <!-- Right Column: Professional Interactive Mockup -->
        <div class="col-lg-6">
          <div class="planner-card-mockup">
            
            <!-- Mockup Header -->
            <div class="planner-card-header">
              <div class="planner-trip-meta">
                <div class="planner-trip-icon">
                  <i class="bi bi-suitcase-lg"></i>
                </div>
                <div>
                  <h4 class="fw-bold text-dark mb-1" id="mockupTripTitle">Rencana Liburan 3 Hari: Eksplorasi Labuan Bajo</h4>
                  <span class="text-muted small" id="mockupTripMeta">3 Hari • Kategori: Pasangan & Keluarga • Rekomendasi Terpadu</span>
                </div>
              </div>
              <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-2 px-3 rounded-pill small">
                Aktif
              </span>
            </div>

            <!-- Timeline Mockup List -->
            <div class="planner-timeline">
              
              <!-- Hari 1 -->
              <div class="timeline-item">
                <span class="timeline-badge badge-stay">Penginapan</span>
                <div class="timeline-details">
                  <div class="timeline-title">Tiba dan masuk ke Plataran Komodo Resort</div>
                  <div class="timeline-meta">14:00 • Vila tepi pantai & istirahat sejenak setelah penerbangan</div>
                </div>
              </div>

              <div class="timeline-item">
                <span class="timeline-badge badge-culinary">Kuliner</span>
                <div class="timeline-details">
                  <div class="timeline-title">Makan Malam Ikan Bakar di Kampung Ujung</div>
                  <div class="timeline-meta">19:00 • Kuliner bahari autentik segar bersama keluarga</div>
                </div>
              </div>

              <!-- Hari 2 -->
              <div class="timeline-item">
                <span class="timeline-badge badge-exp">Aktivitas</span>
                <div class="timeline-details">
                  <div class="timeline-title">Berlayar Kapal Pinisi & Trekking Puncak Pulau Padar</div>
                  <div class="timeline-meta">07:00 • Menikmati tiga teluk warna-warni & snorkeling di Pantai Merah</div>
                </div>
              </div>

              <div class="timeline-item">
                <span class="timeline-badge badge-exp">Aktivitas</span>
                <div class="timeline-details">
                  <div class="timeline-title">Snorkeling di Manta Point & senja di Pulau Kalong</div>
                  <div class="timeline-meta">16:30 • Menyaksikan ribuan kelelawar terbang saat matahari terbenam</div>
                </div>
              </div>

              <!-- Hari 3 -->
              <div class="timeline-item">
                <span class="timeline-badge badge-culinary">Kuliner</span>
                <div class="timeline-details">
                  <div class="timeline-title">Kopi Flores & Belanja Tenun Tradisional Manggarai</div>
                  <div class="timeline-meta">10:00 • Dukungan untuk pengrajin lokal sebelum jadwal kepulangan</div>
                </div>
              </div>

            </div>

            <!-- Footer of Mockup -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
              <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Tersinkronisasi otomatis</span>
              <button class="btn btn-outline-forest btn-sm" data-bs-toggle="modal" data-bs-target="#plannerModal">
                <i class="bi bi-pencil-square me-1"></i>Sesuaikan Rencana
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ======================================================================
       7. TRAVEL INSPIRATION (Editorial Layout)
       ====================================================================== -->
  <section class="section-inspiration">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-journal-richtext"></i> Catatan Perjalanan
        </span>
        <h2 class="section-title">Inspirasi Perjalanan Nusantara</h2>
        <p class="section-subtitle">
          Artikel terkurasi, tips liburan bermakna, dan kisah keindahan tersembunyi dari para penjelajah kepulauan Indonesia.
        </p>
      </div>

      <!-- Editorial Cards Grid -->
      <div class="row g-4">
        <?php foreach ($travel_inspirations as $story): ?>
          <div class="col-lg-4 col-md-6">
            <article class="editorial-card">
              
              <div class="editorial-img-wrap">
                <img src="<?php echo htmlspecialchars($story['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($story['judul']); ?>" 
                     class="editorial-img" 
                     loading="lazy">
                <span class="editorial-tag">
                  <?php echo htmlspecialchars($story['kategori']); ?>
                </span>
              </div>

              <div class="editorial-body">
                <div class="editorial-meta">
                  <span><i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars($story['tanggal']); ?></span>
                  <span>•</span>
                  <span><i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($story['baca']); ?></span>
                </div>

                <h3 class="editorial-title">
                  <?php echo htmlspecialchars($story['judul']); ?>
                </h3>

                <p class="editorial-excerpt">
                  <?php echo htmlspecialchars($story['ringkasan']); ?>
                </p>

                <a href="#destinasi" class="editorial-link">
                  <span>Baca Selengkapnya</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </article>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       8. SECTION TENTANG JELAJAHNUSA (TENTANG KAMI)
       ====================================================================== -->
  <section id="tentang" class="section-about">
    <div class="container">
      <div class="about-quote-box">
        <div class="row align-items-center g-4">
          
          <div class="col-lg-8">
            <span class="section-label section-label-forest">
              <i class="bi bi-flower1"></i> Filosofi JelajahNusa
            </span>
            <h3 class="fw-bold text-dark mt-2 mb-3 fs-2">
              Jelajahi Indonesia dengan Caramu.
            </h3>
            <p class="text-muted mb-0 lead" style="font-size: 1.02rem;">
              JelajahNusa hadir sebagai panduan wisata dan perencana perjalanan digital yang mengedepankan kelestarian alam, keaslian budaya, dan kenyamanan wisatawan. Kami percaya bahwa setiap perjalanan di bumi nusantara harus memberikan manfaat positif bagi komunitas lokal serta menghadirkan kenangan bermakna bagi setiap pengunjung.
            </p>
          </div>

          <div class="col-lg-4 border-start-lg ps-lg-4">
            <div class="d-flex flex-column gap-3">
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                <div>
                  <strong class="d-block text-dark">Rekomendasi Terkurasi</strong>
                  <span class="text-muted small">Bebas ulasan palsu, fokus kenyamanan</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                <div>
                  <strong class="d-block text-dark">Jadwal Perjalanan Terpadu</strong>
                  <span class="text-muted small">Penginapan, kuliner, dan aktivitas dalam satu klik</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                <div>
                  <strong class="d-block text-dark">Dukungan Pariwisata Berkelanjutan</strong>
                  <span class="text-muted small">Menjaga keaslian alam & kearifan lokal</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- ======================================================================
       9. FOOTER
       ====================================================================== -->
  <footer class="custom-footer">
    <div class="container">
      <div class="row g-4">
        
        <!-- Brand & Mission -->
        <div class="col-lg-4 col-md-6">
          <a href="#beranda" class="footer-brand-title" aria-label="JelajahNusa — Jelajahi Indonesia dengan Caramu">
            <img class="footer-brand-logo"
              src="assets/images/logo-horizontal.svg"
              alt="JelajahNusa"
              width="200" height="52">
          </a>
          <p class="footer-desc">
            Platform panduan wisata dan rencana perjalanan di Nusantara. Membantu menemukan destinasi terbaik, penginapan estetik, kuliner autentik, dan merancang perjalanan impianmu di Indonesia.
          </p>
          <div class="footer-social-links">
            <a href="#" class="footer-social-btn" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="footer-social-btn" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="footer-social-btn" title="TikTok" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="#" class="footer-social-btn" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          </div>
        </div>

        <!-- Navigasi Website Lengkap -->
        <div class="col-lg-2 col-md-6 col-6">
          <h4 class="footer-col-title">Navigasi</h4>
          <ul class="footer-link-list">
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#destinasi">Jelajahi Wisata</a></li>
            <li><a href="#kategori">Kategori</a></li>
            <li><a href="#rekomendasi">Rekomendasi</a></li>
            <li><a href="#planner">Rencana Perjalanan</a></li>
            <li><a href="#tentang">Tentang Kami</a></li>
          </ul>
        </div>

        <!-- Kategori Rekomendasi & Wisata -->
        <div class="col-lg-3 col-md-6 col-6">
          <h4 class="footer-col-title">Rekomendasi</h4>
          <ul class="footer-link-list">
            <li><a href="#rekomendasi" onclick="document.querySelector('[data-category=stay]').click();">Penginapan & Resor Ramah Lingkungan</a></li>
            <li><a href="#rekomendasi" onclick="document.querySelector('[data-category=culinary]').click();">Kuliner & Rasa Lokal</a></li>
            <li><a href="#rekomendasi" onclick="document.querySelector('[data-category=experiences]').click();">Aktivitas & Tur</a></li>
            <li><a href="#kategori" onclick="document.querySelector('[data-category-id=pantai]').click();">Wisata Pantai</a></li>
            <li><a href="#kategori" onclick="document.querySelector('[data-category-id=pegunungan]').click();">Wisata Pegunungan</a></li>
          </ul>
        </div>

        <!-- Kontak & Buletin -->
        <div class="col-lg-3 col-md-6">
          <h4 class="footer-col-title">Hubungi Kami</h4>
          <div class="footer-contact-item">
            <i class="bi bi-geo-alt"></i>
            <span>Ubud - Bali & Jakarta Pusat, Indonesia</span>
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-envelope"></i>
            <span>halo@jelajahnusa.id</span>
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-whatsapp"></i>
            <span>+62 812-3456-7890</span>
          </div>

          <!-- Newsletter Form -->
          <form id="newsletterForm" class="mt-3">
            <div class="input-group">
              <input type="email" class="form-control form-control-sm" placeholder="Email Anda..." style="background-color: rgba(232, 230, 217, 0.1); border-color: rgba(232, 230, 217, 0.2); color: #fff;" required>
              <button class="btn btn-forest btn-sm" type="submit">
                <i class="bi bi-send"></i>
              </button>
            </div>
          </form>
        </div>

      </div>

      <!-- Copyright Bar -->
      <div class="footer-bottom">
        <div>
          &copy; <?php echo date('Y'); ?> <strong translate="no" class="notranslate">JelajahNusa</strong>. Seluruh Hak Cipta Dilindungi. Jelajahi Indonesia dengan Caramu.
        </div>
        <div class="d-flex gap-3">
          <a href="#" class="text-decoration-none text-white-50 small">Privasi</a>
          <span class="text-white-50">•</span>
          <a href="#" class="text-decoration-none text-white-50 small">Syarat & Ketentuan</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- ======================================================================
       BACK TO TOP BUTTON
       ====================================================================== -->
  <button id="backToTopBtn" class="back-to-top" title="Kembali ke atas" aria-label="Kembali ke atas">
    <i class="bi bi-arrow-up"></i>
  </button>

  <!-- ======================================================================
       MODAL 1: DETAIL DESTINASI WISATA
       ====================================================================== -->
  <div class="modal fade" id="destinationDetailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" id="modalCategory">Kategori</span>
            <span class="text-muted small"><i class="bi bi-geo-alt me-1 text-success"></i><span id="modalLocation">Lokasi</span></span>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body p-0">
          <div style="height: 300px; overflow: hidden; background-color: var(--c-beige);">
            <img id="modalImage" src="" alt="Destinasi" class="w-100 h-100 object-fit-cover">
          </div>
          <div class="p-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h3 class="fw-bold mb-0 text-dark" id="modalTitle">Nama Destinasi</h3>
              <div class="badge bg-light text-dark border fs-6 d-flex align-items-center gap-1">
                <i class="bi bi-star-fill text-warning"></i> <span id="modalRating">4.9</span>
              </div>
            </div>
            <p class="text-muted mt-3" id="modalDescription">Deskripsi destinasi wisata.</p>
            
            <h6 class="fw-bold text-dark mt-4 mb-2">Highlight & Keistimewaan:</h6>
            <div id="modalHighlights" class="d-flex flex-wrap"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-forest rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#plannerModal">
            <i class="bi bi-plus-circle me-1"></i> Masukkan ke Jadwal Perjalanan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       MODAL 2: INTERACTIVE TRAVEL PLANNER BUILDER
       ====================================================================== -->
  <div class="modal fade" id="plannerModal" tabindex="-1" aria-labelledby="plannerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <div class="brand-leaf-icon" style="width: 32px; height: 32px; font-size: 1rem;">
              <i class="bi bi-calendar2-plus"></i>
            </div>
            <h5 class="fw-bold text-dark mb-0" id="plannerModalTitle">Mulai Rencana Perjalanan</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted small mb-4">
            Pilih destinasi dan preferensi perjalananmu untuk menyusun rekomendasi perjalanan terpadu.
          </p>

          <form id="plannerBuilderForm">
            <!-- Destinasi Pilihan -->
            <div class="mb-3">
              <label for="planDestSelect" class="form-label small fw-bold text-dark">Destinasi Utama</label>
              <select id="planDestSelect" class="form-select" required>
                <option value="Labuan Bajo" selected>Labuan Bajo (Nusa Tenggara Timur)</option>
                <option value="Bali">Pulau Bali (Ubud & Pantai)</option>
                <option value="Raja Ampat">Raja Ampat (Papua Barat Daya)</option>
                <option value="Gunung Bromo">Gunung Bromo (Jawa Timur)</option>
                <option value="Danau Toba">Danau Toba (Sumatera Utara)</option>
                <option value="Yogyakarta">Yogyakarta & Candi</option>
              </select>
            </div>

            <!-- Durasi Hari -->
            <div class="mb-3">
              <label for="planDaysInput" class="form-label small fw-bold text-dark">Durasi Perjalanan (Hari)</label>
              <select id="planDaysInput" class="form-select" required>
                <option value="3" selected>3 Hari 2 Malam (Liburan Singkat)</option>
                <option value="4">4 Hari 3 Malam (Rekomendasi)</option>
                <option value="5">5 Hari 4 Malam (Eksplorasi Lengkap)</option>
                <option value="7">7 Hari (Ekowisata Santai)</option>
              </select>
            </div>

            <!-- Tipe Wisatawan -->
            <div class="mb-4">
              <label for="planTypeSelect" class="form-label small fw-bold text-dark">Kategori Wisatawan</label>
              <select id="planTypeSelect" class="form-select" required>
                <option value="Pasangan & Keluarga" selected>Pasangan & Keluarga</option>
                <option value="Solo Traveler">Wisatawan Solo (Mandiri)</option>
                <option value="Teman & Grup">Teman & Kelompok</option>
              </select>
            </div>

            <button type="submit" class="btn btn-forest w-100 py-2">
              <i class="bi bi-magic me-1"></i> Buat & Tampilkan Jadwal Perjalanan
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       MODAL 3: LOGIN & DAFTAR (AUTHENTICATION)
       ====================================================================== -->
  <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header border-bottom-0 pb-0">
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body p-4 pt-0">
          <div class="text-center mb-4">
            <div class="brand-leaf-icon mx-auto mb-2" style="width: 52px; height: 52px; padding: 4px;">
              <img src="assets/images/logo-icon.svg" alt="JelajahNusa" class="brand-icon-svg">
            </div>
            <h4 class="fw-bold text-dark mb-1" id="authModalTitle">Masuk ke <span translate="no" class="notranslate">JelajahNusa</span></h4>
            <p class="text-muted small">            Jelajahi Indonesia dengan Caramu.</p>
          </div>

          <!-- Tab Masuk / Daftar -->
          <div class="d-flex bg-white p-1 rounded-pill border mb-4">
            <button class="btn btn-forest flex-fill rounded-pill py-1 small fw-bold" id="tabLoginBtn" type="button">
              Masuk
            </button>
            <button class="btn btn-link text-muted text-decoration-none flex-fill rounded-pill py-1 small fw-bold" id="tabRegisterBtn" type="button">
              Daftar
            </button>
          </div>

          <!-- Alert Pesan Feedback Autentikasi -->
          <div id="authAlert" class="alert alert-danger d-none py-2 px-3 small rounded-3 mb-3" role="alert"></div>

          <!-- Form Masuk -->
          <form id="loginForm" method="POST" action="auth/login.php">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($safe_redirect); ?>">
            <div class="mb-3">
              <label for="loginEmail" class="form-label small fw-bold text-dark">Alamat Email</label>
              <input type="email" class="form-control" id="loginEmail" name="email" placeholder="nama@email.com" required autocomplete="email">
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label small fw-bold text-dark">Kata Sandi</label>
              <input type="password" class="form-control" id="loginPassword" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                <label class="form-check-label small text-muted" for="rememberMe">Ingat saya</label>
              </div>
              <a href="#" class="small text-decoration-none text-muted" onclick="alert('Silakan hubungi administrator untuk bantuan pemulihan akun.')">Lupa sandi?</a>
            </div>
            <button type="submit" class="btn btn-forest w-100 py-2">
              Masuk ke Akun
            </button>
            <div class="text-center mt-3">
              <span class="small text-muted">Belum punya akun? <a href="#" id="switchToRegister" class="text-success fw-bold text-decoration-none">Daftar sekarang</a></span>
            </div>
          </form>

          <!-- Form Daftar -->
          <form id="registerForm" class="d-none" method="POST" action="auth/register.php">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($safe_redirect); ?>">
            <div class="mb-3">
              <label for="regName" class="form-label small fw-bold text-dark">Nama Lengkap</label>
              <input type="text" class="form-control" id="regName" name="name" placeholder="Nama Anda" required autocomplete="name">
            </div>
            <div class="mb-3">
              <label for="regEmail" class="form-label small fw-bold text-dark">Alamat Email</label>
              <input type="email" class="form-control" id="regEmail" name="email" placeholder="nama@email.com" required autocomplete="email">
            </div>
            <div class="mb-3">
              <label for="regPassword" class="form-label small fw-bold text-dark">Kata Sandi</label>
              <input type="password" class="form-control" id="regPassword" name="password" placeholder="Minimal 8 karakter" required minlength="8" autocomplete="new-password">
            </div>
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="termsCheck" name="terms" checked required>
              <label class="form-check-label small text-muted" for="termsCheck">
                Saya menyetujui Ketentuan Penggunaan <span translate="no" class="notranslate">JelajahNusa</span>.
              </label>
            </div>
            <button type="submit" class="btn btn-forest w-100 py-2">
              Daftar Akun Baru
            </button>
            <div class="text-center mt-3">
              <span class="small text-muted">Sudah punya akun? <a href="#" id="switchToLogin" class="text-success fw-bold text-decoration-none">Masuk di sini</a></span>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       MODAL 4: DESTINASI FAVORIT SAYA (FITUR PENGGUNA)
       ====================================================================== -->
  <div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-heart-fill text-danger fs-5"></i>
            <h5 class="fw-bold text-dark mb-0" id="favoritesModalTitle">Destinasi Favorit Saya</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body p-4">
          
          <!-- Empty State -->
          <div id="favoritesEmptyState" class="text-center py-4">
            <i class="bi bi-heart text-muted fs-1 d-block mb-3"></i>
            <h6 class="fw-bold text-dark">Belum Ada Favorit</h6>
            <p class="text-muted small">Klik ikon hati pada kartu destinasi untuk menyimpannya ke daftar favoritmu.</p>
            <a href="#destinasi" class="btn btn-outline-forest btn-sm mt-2" data-bs-dismiss="modal">
              Jelajahi Destinasi
            </a>
          </div>

          <!-- List of Favorites (Rendered by JS) -->
          <div id="favoritesListContainer"></div>

        </div>
        <div class="modal-footer bg-white d-flex justify-content-between">
          <a href="pages/favorit.php" class="btn btn-forest rounded-pill btn-sm px-3">
            <i class="bi bi-grid me-1"></i> Buka Halaman Favorit Lengkap
          </a>
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- Custom Nature-Inspired JavaScript -->
  <script src="assets/js/script.js"></script>

</body>
</html>
