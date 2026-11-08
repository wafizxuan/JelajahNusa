<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth_base_url = '../auth/';
/**
 * JelajahNusa - Halaman Detail Destinasi Dinamis
 * Lokasi: pages/detail-destinasi.php
 * Konsep: Nature-Inspired, Aesthetic, Minimalist, Clean
 * Palet Warna: Forest Green (#345B49), Sage Green (#A8B99A), Soft Beige (#E8E6D9), Off White (#F7F7F2), Dark Forest (#29352D)
 */

// Muat konfigurasi database & data master destinasi komprehensif
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/destinasi.php';

// Ambil ID destinasi dari parameter URL (default ke ID 2: Bali)
$id_destinasi = isset($_GET['id']) ? (int)$_GET['id'] : 2;

$destinasi = null;

// 1. Ambil dari database MySQL sebagai sumber utama
try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$id_destinasi]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $jsonFields = ['daya_tarik', 'waktu_terbaik', 'popular_places', 'recommended_stays', 'local_culinary', 'things_to_do', 'travel_info', 'card_tags'];
            foreach ($jsonFields as $field) {
                if (isset($row[$field]) && is_string($row[$field])) {
                    $row[$field] = json_decode($row[$field], true) ?? [];
                }
            }
            $destinasi = $row;
        }
    }
} catch (Throwable $e) {
    error_log('[Detail Destinasi DB Error] ' . $e->getMessage());
}

// 2. Fallback ke data PHP statis jika database tidak mengembalikan data
if (!$destinasi && isset($all_destinasi[$id_destinasi])) {
    $destinasi = $all_destinasi[$id_destinasi];
}

// Validasi keberadaan destinasi
if (!$destinasi) {
    http_response_code(404);
    echo '<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destinasi Tidak Ditemukan | JelajahNusa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
  <div class="text-center p-5 bg-white rounded-4 shadow-sm" style="max-width: 480px;">
    <div class="display-1 text-success mb-3"><i class="bi bi-geo-alt"></i></div>
    <h1 class="h3 fw-bold text-dark mb-2">Destinasi Tidak Ditemukan</h1>
    <p class="text-muted mb-4">Mohon maaf, destinasi wisata yang Anda tuju tidak ditemukan atau parameter ID tidak valid.</p>
    <a href="../index.php#destinasi" class="btn btn-forest px-4 py-2 rounded-pill text-white" style="background-color: var(--c-forest, #345B49);">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Jelajah Wisata
    </a>
  </div>
</body>
</html>';
    exit;
}

  $reviewSummary = ['rating' => null, 'total_ulasan' => 0];
  $publicReviews = [];
  $myReview = null;
  try {
    $summaryStmt = $pdo->prepare("SELECT AVG(rating) AS rating, COUNT(*) AS total_ulasan FROM destination_reviews WHERE destination_id = :destination_id AND status <> 'hidden'");
    $summaryStmt->execute(['destination_id' => $id_destinasi]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
    if ($summary && (int)$summary['total_ulasan'] > 0) {
      $reviewSummary = [
        'rating' => (float)$summary['rating'],
        'total_ulasan' => (int)$summary['total_ulasan'],
      ];
    }

    $reviewsStmt = $pdo->prepare("SELECT r.rating, r.review_text, r.created_at, u.name FROM destination_reviews r JOIN users u ON u.id = r.user_id WHERE r.destination_id = :destination_id AND r.status <> 'hidden' ORDER BY r.created_at DESC");
    $reviewsStmt->execute(['destination_id' => $id_destinasi]);
    $publicReviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_SESSION['user']['id'])) {
      if (empty($_SESSION['csrf_review'])) {
        $_SESSION['csrf_review'] = bin2hex(random_bytes(32));
      }
      $myReviewStmt = $pdo->prepare('SELECT rating, review_text, status FROM destination_reviews WHERE destination_id = :destination_id AND user_id = :user_id LIMIT 1');
      $myReviewStmt->execute([
        'destination_id' => $id_destinasi,
        'user_id' => (int)$_SESSION['user']['id'],
      ]);
      $myReview = $myReviewStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
  } catch (Throwable $e) {
    error_log('[Destination Reviews Query Error] ' . $e->getMessage());
  }
  $reviewFlash = $_SESSION['review_flash'] ?? null;
  unset($_SESSION['review_flash']);

// Data destinasi aktif
$popular_places    = $destinasi['popular_places'];
$recommended_stays = $destinasi['recommended_stays'];
$local_culinary    = $destinasi['local_culinary'];
$things_to_do      = $destinasi['things_to_do'];
$travel_info       = $destinasi['travel_info'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jelajah <?php echo htmlspecialchars($destinasi['nama']); ?> — Wisata, Penginapan, Kuliner & Travel Guide | JelajahNusa</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <meta name="jn-auth-status" content="<?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>">
  <meta name="description" content="Panduan lengkap wisata <?php echo htmlspecialchars($destinasi['nama']); ?>: tempat wisata populer, rekomendasi penginapan dan eco-resort, kuliner khas autentik, serta panduan praktis perjalanan di JelajahNusa.">
  <meta name="keywords" content="Wisata <?php echo htmlspecialchars($destinasi['nama']); ?>, Liburan <?php echo htmlspecialchars($destinasi['nama']); ?>, Hotel <?php echo htmlspecialchars($destinasi['nama']); ?>, Kuliner <?php echo htmlspecialchars($destinasi['nama']); ?>, JelajahNusa">

  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom CSS: Base Style & Detail Destinasi -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/detail-destinasi.css">
</head>
<body>

  <!-- ======================================================================
       1. NAVBAR (Konsisten dengan Homepage JelajahNusa)
       ====================================================================== -->
  <nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">
      
      <!-- Brand Logo -->
      <a class="navbar-brand" href="../index.php" aria-label="JelajahNusa — Kembali ke Beranda">
        <img class="brand-logo-img"
          src="../assets/images/logo-horizontal.svg"
          alt="JelajahNusa — Discover Indonesia, Your Way"
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
            <a class="nav-link" href="../index.php#beranda">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="../index.php#destinasi">Jelajahi Wisata</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../index.php#kategori">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../index.php#rekomendasi">Rekomendasi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="rencana-perjalanan.php">Travel Planner</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../index.php#tentang">Tentang Kami</a>
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
                  <li><a class="dropdown-item text-success fw-bold small" href="../admin/"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item fw-semibold" href="profil.php" style="color:var(--c-dark-forest);">
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
       2. DESTINATION HERO SECTION
       ====================================================================== -->
  <header class="detail-hero-section">
    <div class="detail-hero-bg" style="background-image: url('<?php echo htmlspecialchars($destinasi['hero_image']); ?>');"></div>
    <div class="detail-hero-overlay"></div>

    <div class="container position-relative">
      <div class="detail-hero-content">
        
        <!-- Breadcrumb Navigation -->
        <div class="detail-breadcrumb">
          <a href="../index.php"><i class="bi bi-house-door me-1"></i>Beranda</a>
          <span>/</span>
          <a href="../index.php#destinasi">Destinasi</a>
          <span>/</span>
          <span class="text-white"><?php echo htmlspecialchars($destinasi['nama']); ?></span>
        </div>

        <!-- Meta Pills -->
        <div class="detail-meta-pills">
          <span class="detail-meta-pill">
            <i class="bi bi-geo-alt text-success"></i>
            <?php echo htmlspecialchars($destinasi['wilayah']); ?>
          </span>
          <span class="detail-meta-pill">
            <i class="bi bi-collection text-success"></i>
            <?php echo htmlspecialchars($destinasi['kategori']); ?>
          </span>
          <span class="detail-meta-pill">
            <?php if ($reviewSummary['rating'] !== null): ?>
              <i class="bi bi-star-fill text-warning"></i>
              <?php echo number_format($reviewSummary['rating'], 1); ?>
              (<?php echo number_format($reviewSummary['total_ulasan']); ?> ulasan)
            <?php else: ?>
              <i class="bi bi-star text-white-50"></i>
              <span class="text-white-50 small">Belum tersedia</span>
            <?php endif; ?>
          </span>
        </div>

        <!-- Headline & Description -->
        <h1 class="detail-hero-title">
          <?php echo htmlspecialchars($destinasi['nama']); ?>
        </h1>
        <p class="detail-hero-desc">
          <?php echo htmlspecialchars($destinasi['tagline']); ?>
        </p>

        <!-- Action Buttons: Simpan Favorit & Tambahkan ke Trip -->
        <div class="detail-hero-actions">
          
          <!-- Tombol Simpan Favorit (Terhubung dengan LocalStorage script.js) -->
          <button type="button" 
                  class="btn-detail-fav btn-card-fav" 
                  data-id="<?php echo htmlspecialchars($destinasi['id']); ?>"
                  data-nama="<?php echo htmlspecialchars($destinasi['nama']); ?>"
                  data-lokasi="<?php echo htmlspecialchars($destinasi['wilayah']); ?>"
                  data-foto="<?php echo htmlspecialchars($destinasi['hero_image']); ?>"
                  id="detailHeroFavBtn">
            <i class="bi bi-heart"></i>
            <span class="detail-fav-text">Simpan Favorit</span>
          </button>

          <!-- Tombol Tambahkan ke Trip -->
          <button type="button" 
                  class="btn btn-hero-solid btn-add-to-trip-trigger" 
                  data-bs-toggle="modal" 
                  data-bs-target="#addToTripModal"
                  data-nama="<?php echo htmlspecialchars($destinasi['nama']); ?>"
                  data-lokasi="<?php echo htmlspecialchars($destinasi['wilayah']); ?>"
                  data-kategori="Destinasi"
                  data-foto="<?php echo htmlspecialchars($destinasi['hero_image']); ?>"
                  id="btnAddToTripHero">
            <i class="bi bi-calendar2-plus"></i>
            <span>Tambahkan ke Trip</span>
          </button>

          <!-- Tombol Kembali ke Beranda -->
          <a href="../index.php#destinasi" class="btn btn-hero-glass">
            <i class="bi bi-arrow-left"></i>
            <span>Jelajahi Lainnya</span>
          </a>

        </div>

      </div>
    </div>
  </header>

  <!-- ======================================================================
       3. ABOUT DESTINATION & DAYA TARIK UTAMA
       ====================================================================== -->
  <section class="section-detail-about">
    <div class="container">
      <div class="row g-5 align-items-center">
        
        <!-- Left: Full Description & Best Time to Visit -->
        <div class="col-lg-6">
          <span class="section-label section-label-forest">
            <i class="bi bi-info-circle"></i> Tentang Destinasi
          </span>
          <h2 class="section-title mb-3"><?php echo htmlspecialchars($destinasi['about_heading'] ?? ('Keindahan & Pesona ' . $destinasi['nama'])); ?></h2>
          <p class="text-muted lead mb-4" style="font-size: 1.05rem;">
            <?php echo htmlspecialchars($destinasi['deskripsi']); ?>
          </p>

          <!-- Best Time to Visit Box -->
          <div class="best-time-box">
            <span class="best-time-badge">
              <i class="bi bi-brightness-high"></i> Waktu Terbaik Berkunjung
            </span>
            <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($destinasi['waktu_terbaik']['musim']); ?></h5>
            <p class="text-muted small mb-2"><?php echo htmlspecialchars($destinasi['waktu_terbaik']['cuaca']); ?></p>
            <div class="p-2 rounded bg-light border text-muted small">
              <i class="bi bi-lightbulb text-success me-1"></i>
              <strong>Saran Lokal:</strong> <?php echo htmlspecialchars($destinasi['waktu_terbaik']['tips']); ?>
            </div>
          </div>
        </div>

        <!-- Right: 4 Daya Tarik Utama Cards -->
        <div class="col-lg-6">
          <div class="about-card-box">
            <h4 class="fw-bold text-dark mb-4">Daya Tarik Utama <?php echo htmlspecialchars($destinasi['nama']); ?></h4>
            
            <?php foreach ($destinasi['daya_tarik'] as $daya): ?>
              <div class="highlight-item">
                <div class="highlight-icon-wrap">
                  <i class="bi <?php echo htmlspecialchars($daya['icon']); ?>"></i>
                </div>
                <div>
                  <h4 class="highlight-title"><?php echo htmlspecialchars($daya['judul']); ?></h4>
                  <p class="text-muted small mb-0"><?php echo htmlspecialchars($daya['desc']); ?></p>
                </div>
              </div>
            <?php endforeach; ?>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ======================================================================
       4. POPULAR PLACES (Minimal 4 Tempat: Ubud, Tanah Lot, Uluwatu, Nusa Penida)
       ====================================================================== -->
  <section class="section-detail-places">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-geo-alt"></i> Objek Wisata Ikonik
        </span>
        <h2 class="section-title">Tempat Wisata Populer di <?php echo htmlspecialchars($destinasi['nama']); ?></h2>
        <p class="section-subtitle">
          Jelajahi keindahan sudut-sudut paling memikat di <?php echo htmlspecialchars($destinasi['nama']); ?>, mulai dari keasrian alam hingga daya tarik budayanya yang memukau.
        </p>
      </div>

      <!-- Places Grid (4 Cards) -->
      <div class="row g-4">
        <?php foreach ($popular_places as $place): ?>
          <div class="col-lg-3 col-md-6">
            <div class="place-card">
              
              <div class="place-img-wrap">
                <img src="<?php echo htmlspecialchars($place['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($place['nama']); ?>" 
                     class="place-img" 
                     loading="lazy">
                <span class="place-tag-badge">
                  <?php echo htmlspecialchars($place['tag']); ?>
                </span>
              </div>

              <div class="place-body">
                <span class="place-location">
                  <i class="bi bi-pin-map"></i>
                  <?php echo htmlspecialchars($place['lokasi']); ?>
                </span>

                <h3 class="place-title">
                  <?php echo htmlspecialchars($place['nama']); ?>
                </h3>

                <p class="place-desc">
                  <?php echo htmlspecialchars($place['deskripsi']); ?>
                </p>

                <div class="pt-2">
                  <button type="button" 
                          class="btn btn-sm btn-outline-forest rounded-pill btn-add-to-trip-trigger"
                          data-bs-toggle="modal" 
                          data-bs-target="#addToTripModal"
                          data-nama="<?php echo htmlspecialchars($place['nama']); ?>"
                          data-lokasi="<?php echo htmlspecialchars($place['lokasi']); ?>"
                          data-kategori="Destinasi"
                          data-foto="<?php echo htmlspecialchars($place['foto']); ?>">
                    <i class="bi bi-calendar-plus me-1"></i> Tambah ke Trip
                  </button>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       5. RECOMMENDED STAYS (Minimal 3 Rekomendasi Penginapan)
       ====================================================================== -->
  <section class="section-detail-stays">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-house-heart"></i> Tempat Menginap Pilihan
        </span>
        <h2 class="section-title">Rekomendasi Penginapan di <?php echo htmlspecialchars($destinasi['nama']); ?></h2>
        <p class="section-subtitle">
          Istirahat dengan nyaman di pilihan akomodasi terbaik di <?php echo htmlspecialchars($destinasi['nama']); ?>, mulai dari eco-resort hingga penginapan bernuansa alam.
        </p>
      </div>

      <!-- Stays Grid -->
      <div class="row g-4">
        <?php foreach ($recommended_stays as $stay): ?>
          <div class="col-lg-4 col-md-6">
            <div class="stay-card">
              
              <div class="stay-img-wrap">
                <img src="<?php echo htmlspecialchars($stay['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($stay['nama']); ?>" 
                     class="stay-img" 
                     loading="lazy">
                <span class="stay-type-badge">
                  <?php echo htmlspecialchars($stay['jenis']); ?>
                </span>
              </div>

              <div class="stay-body">
                <span class="place-location mb-1">
                  <i class="bi bi-geo-alt"></i>
                  <?php echo htmlspecialchars($stay['lokasi']); ?>
                </span>

                <h3 class="place-title mb-2">
                  <?php echo htmlspecialchars($stay['nama']); ?>
                </h3>

                <p class="text-muted small mb-3">
                  <i class="bi bi-check2-circle text-success me-1"></i>
                  <?php echo htmlspecialchars($stay['fasilitas']); ?>
                </p>

                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                  <div>
                    <span class="text-muted small d-block">Estimasi Harga</span>
                    <span class="stay-price-tag"><?php echo htmlspecialchars($stay['harga']); ?></span>
                    <span class="text-muted small"><?php echo htmlspecialchars($stay['periode']); ?></span>
                  </div>
                  <button type="button" 
                          class="btn btn-outline-forest btn-sm rounded-pill btn-add-to-trip-trigger"
                          data-bs-toggle="modal" 
                          data-bs-target="#addToTripModal"
                          data-nama="<?php echo htmlspecialchars($stay['nama']); ?>"
                          data-lokasi="<?php echo htmlspecialchars($stay['lokasi']); ?>"
                          data-kategori="Penginapan"
                          data-foto="<?php echo htmlspecialchars($stay['foto']); ?>">
                    <i class="bi bi-calendar-plus me-1"></i> Tambah ke Trip
                  </button>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       6. LOCAL CULINARY (Minimal 3 Rekomendasi Kuliner)
       ====================================================================== -->
  <section class="section-detail-culinary">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-cup-hot"></i> Wisata Kuliner Nusantara
        </span>
        <h2 class="section-title">Kuliner Khas Autentik <?php echo htmlspecialchars($destinasi['nama']); ?></h2>
        <p class="section-subtitle">
          Rasakan kekayaan cita rasa kuliner tradisional khas <?php echo htmlspecialchars($destinasi['nama']); ?> dari racikan resep warisan leluhur.
        </p>
      </div>

      <!-- Culinary Grid -->
      <div class="row g-4">
        <?php foreach ($local_culinary as $cul): ?>
          <div class="col-lg-4 col-md-6">
            <div class="culinary-card">
              
              <div class="culinary-img-wrap">
                <img src="<?php echo htmlspecialchars($cul['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($cul['nama']); ?>" 
                     class="culinary-img" 
                     loading="lazy">
                <span class="stay-type-badge">
                  <?php echo htmlspecialchars($cul['jenis']); ?>
                </span>
              </div>

              <div class="culinary-body">
                <span class="place-location mb-1">
                  <i class="bi bi-geo-alt"></i>
                  <?php echo htmlspecialchars($cul['lokasi']); ?>
                </span>

                <h3 class="place-title mb-2">
                  <?php echo htmlspecialchars($cul['nama']); ?>
                </h3>

                <p class="place-desc mb-3">
                  <?php echo htmlspecialchars($cul['deskripsi']); ?>
                </p>

                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                  <div>
                    <span class="text-muted small d-block">Kisaran Harga</span>
                    <strong class="text-dark" style="font-family: var(--font-heading);"><?php echo htmlspecialchars($cul['harga']); ?></strong>
                  </div>
                  <button type="button" 
                          class="btn btn-outline-forest btn-sm rounded-pill btn-add-to-trip-trigger"
                          data-bs-toggle="modal" 
                          data-bs-target="#addToTripModal"
                          data-nama="<?php echo htmlspecialchars($cul['nama']); ?>"
                          data-lokasi="<?php echo htmlspecialchars($cul['lokasi']); ?>"
                          data-kategori="Kuliner"
                          data-foto="<?php echo htmlspecialchars($cul['foto']); ?>">
                    <i class="bi bi-calendar-plus me-1"></i> Tambah ke Trip
                  </button>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       7. THINGS TO DO (Aktivitas Wisata)
       ====================================================================== -->
  <section class="section-detail-activities">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-compass"></i> Aktivitas Menarik
        </span>
        <h2 class="section-title">Aktivitas & Pengalaman Berkesan</h2>
        <p class="section-subtitle">
          Koleksi pengalaman liburan yang wajib dicoba untuk menyempurnakan cerita petualanganmu di <?php echo htmlspecialchars($destinasi['nama']); ?>.
        </p>
      </div>

      <!-- Activities Grid -->
      <div class="row g-4">
        <?php foreach ($things_to_do as $act): ?>
          <div class="col-lg-3 col-md-6">
            <div class="activity-card">
              
              <div class="activity-icon-bubble">
                <i class="bi <?php echo htmlspecialchars($act['icon']); ?>"></i>
              </div>

              <span class="text-muted small mb-1">
                <i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($act['durasi']); ?>
              </span>

              <h4 class="activity-title"><?php echo htmlspecialchars($act['judul']); ?></h4>

              <p class="activity-desc"><?php echo htmlspecialchars($act['deskripsi']); ?></p>

              <div class="mt-auto pt-2">
                <span class="text-muted small">
                  <i class="bi bi-geo-alt me-1 text-success"></i><?php echo htmlspecialchars($act['lokasi']); ?>
                </span>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ======================================================================
       8. TRAVEL INFORMATION (Informasi Penting Sebelum Berkunjung)
       ====================================================================== -->
  <section class="section-detail-info">
    <div class="container">
      
      <!-- Section Header -->
      <div class="text-center mb-5">
        <span class="section-label">
          <i class="bi bi-journal-check"></i> Panduan Perjalanan
        </span>
        <h2 class="section-title">Informasi Praktis Wisata <?php echo htmlspecialchars($destinasi['nama']); ?></h2>
        <p class="section-subtitle">
          Semua hal esensial yang perlu kamu ketahui seputar estimasi biaya, transportasi, serta etika berkunjung ke <?php echo htmlspecialchars($destinasi['nama']); ?>.
        </p>
      </div>

      <!-- Information Grid (4 Cards) -->
      <div class="row g-4">
        
        <!-- Estimasi Biaya -->
        <div class="col-lg-6">
          <div class="info-card">
            <div class="info-card-header">
              <div class="info-icon">
                <i class="bi <?php echo htmlspecialchars($travel_info['biaya']['icon']); ?>"></i>
              </div>
              <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($travel_info['biaya']['title']); ?></h4>
            </div>
            <ul class="info-list">
              <?php foreach ($travel_info['biaya']['items'] as $item): ?>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  <span><?php echo htmlspecialchars($item); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <!-- Transportasi -->
        <div class="col-lg-6">
          <div class="info-card">
            <div class="info-card-header">
              <div class="info-icon">
                <i class="bi <?php echo htmlspecialchars($travel_info['transportasi']['icon']); ?>"></i>
              </div>
              <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($travel_info['transportasi']['title']); ?></h4>
            </div>
            <ul class="info-list">
              <?php foreach ($travel_info['transportasi']['items'] as $item): ?>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  <span><?php echo htmlspecialchars($item); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <!-- Tips Perjalanan -->
        <div class="col-lg-6">
          <div class="info-card">
            <div class="info-card-header">
              <div class="info-icon">
                <i class="bi <?php echo htmlspecialchars($travel_info['tips']['icon']); ?>"></i>
              </div>
              <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($travel_info['tips']['title']); ?></h4>
            </div>
            <ul class="info-list">
              <?php foreach ($travel_info['tips']['items'] as $item): ?>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  <span><?php echo htmlspecialchars($item); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <!-- Waktu Berkunjung -->
        <div class="col-lg-6">
          <div class="info-card">
            <div class="info-card-header">
              <div class="info-icon">
                <i class="bi <?php echo htmlspecialchars($travel_info['musim']['icon']); ?>"></i>
              </div>
              <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($travel_info['musim']['title']); ?></h4>
            </div>
            <ul class="info-list">
              <?php foreach ($travel_info['musim']['items'] as $item): ?>
                <li>
                  <i class="bi bi-check-circle-fill"></i>
                  <span><?php echo htmlspecialchars($item); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ======================================================================
       9. RATING & ULASAN INTERNAL
       ====================================================================== -->
<?php require __DIR__ . '/review-section.php'; ?>

  <!-- ======================================================================
       10. CREATE YOUR TRIP SECTION
       ====================================================================== -->
  <section class="section-detail-cta">
      <div class="detail-cta-card">
        <span class="section-label" style="background-color: rgba(247, 247, 242, 0.15); color: var(--c-beige);">
          <i class="bi bi-calendar2-heart"></i> Itinerary Builder
        </span>
        <h2 class="text-white fw-bold mb-3 fs-1">
          Siap Menyusun Perjalanan Impianmu ke <?php echo htmlspecialchars($destinasi['nama']); ?>?
        </h2>
        <p class="text-white-50 mx-auto mb-4 lead" style="max-width: 680px;">
          Kumpulkan destinasi favorit, tempat menginap terbaik, kuliner autentik, dan aktivitas wisata di atas ke dalam satu rencana perjalanan yang fleksibel.
        </p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
          <!-- Tombol Tambahkan ke Rencana Perjalanan -->
          <button type="button" 
                  class="btn btn-hero-solid px-4 py-3" 
                  data-bs-toggle="modal" 
                  data-bs-target="#plannerModal">
            <i class="bi bi-plus-circle me-1"></i>
            <span>Tambahkan ke Rencana Perjalanan</span>
          </button>

          <!-- Kembali ke Beranda -->
          <a href="../index.php" class="btn btn-hero-glass px-4 py-3">
            <i class="bi bi-house-door me-1"></i>
            <span>Kembali ke Beranda</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ======================================================================
       10. FOOTER (Konsisten dengan Homepage JelajahNusa)
       ====================================================================== -->
  <footer class="custom-footer">
    <div class="container">
      <div class="row g-4">
        
        <!-- Brand & Mission -->
        <div class="col-lg-4 col-md-6">
          <a href="../index.php" class="footer-brand-title" aria-label="JelajahNusa — Discover Indonesia, Your Way">
            <img class="footer-brand-logo"
              src="../assets/images/logo-horizontal.svg"
              alt="JelajahNusa"
              width="200" height="52">
          </a>
          <p class="footer-desc">
            Platform digital travel guide & travel planner nusantara. Membantu menemukan destinasi terbaik, penginapan estetik, kuliner autentik, dan merancang itinerary perjalanan impianmu di Indonesia.
          </p>
          <div class="footer-social-links">
            <a href="#" class="footer-social-btn" title="Instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="footer-social-btn" title="YouTube" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="footer-social-btn" title="TikTok" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="#" class="footer-social-btn" title="Facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          </div>
        </div>

        <!-- Navigasi Website -->
        <div class="col-lg-2 col-md-6 col-6">
          <h4 class="footer-col-title">Navigasi</h4>
          <ul class="footer-link-list">
            <li><a href="../index.php#beranda">Beranda</a></li>
            <li><a href="../index.php#destinasi">Jelajahi Wisata</a></li>
            <li><a href="../index.php#kategori">Kategori</a></li>
            <li><a href="../index.php#rekomendasi">Rekomendasi</a></li>
            <li><a href="rencana-perjalanan.php">Travel Planner</a></li>
            <li><a href="../index.php#tentang">Tentang Kami</a></li>
          </ul>
        </div>

        <!-- Rekomendasi Destinasi Populer -->
        <div class="col-lg-3 col-md-6 col-6">
          <h4 class="footer-col-title">Destinasi Terpopuler</h4>
          <ul class="footer-link-list">
            <li><a href="detail-destinasi.php?id=2" class="<?php echo ($destinasi['id'] == 2) ? 'text-white fw-bold' : ''; ?>">Pulau Bali</a></li>
            <li><a href="detail-destinasi.php?id=1" class="<?php echo ($destinasi['id'] == 1) ? 'text-white fw-bold' : ''; ?>">Raja Ampat</a></li>
            <li><a href="detail-destinasi.php?id=3" class="<?php echo ($destinasi['id'] == 3) ? 'text-white fw-bold' : ''; ?>">Gunung Bromo</a></li>
            <li><a href="detail-destinasi.php?id=4" class="<?php echo ($destinasi['id'] == 4) ? 'text-white fw-bold' : ''; ?>">Labuan Bajo</a></li>
            <li><a href="detail-destinasi.php?id=5" class="<?php echo ($destinasi['id'] == 5) ? 'text-white fw-bold' : ''; ?>">Danau Toba</a></li>
            <li><a href="detail-destinasi.php?id=6" class="<?php echo ($destinasi['id'] == 6) ? 'text-white fw-bold' : ''; ?>">Yogyakarta & Candi</a></li>
          </ul>
        </div>

        <!-- Kontak & Buletin -->
        <div class="col-lg-3 col-md-6">
          <h4 class="footer-col-title">Informasi Kontak</h4>
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
          &copy; <?php echo date('Y'); ?> <strong>JelajahNusa</strong>. Seluruh Hak Cipta Dilindungi. Discover Indonesia, Your Way.
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
       MODAL: TRAVEL PLANNER BUILDER (INTERAKTIF DENGAN PRESET BALI)
       ====================================================================== -->
  <div class="modal fade" id="plannerModal" tabindex="-1" aria-labelledby="plannerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <div class="brand-leaf-icon" style="width: 32px; height: 32px; font-size: 1rem;">
              <i class="bi bi-calendar2-plus"></i>
            </div>
            <h5 class="fw-bold text-dark mb-0" id="plannerModalTitle">Tambahkan <?php echo htmlspecialchars($destinasi['nama']); ?> ke Rencana Perjalanan</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted small mb-4">
            Susun rencana liburan impian ke <?php echo htmlspecialchars($destinasi['nama']); ?> dengan rekomendasi destinasi, penginapan, dan kuliner terpadu.
          </p>

          <form id="plannerBuilderForm">
            <!-- Destinasi Pilihan (Default: Bali) -->
            <div class="mb-3">
              <label for="planDestSelect" class="form-label small fw-bold text-dark">Destinasi Utama</label>
              <select id="planDestSelect" class="form-select" required>
                <option value="Bali" <?php echo ($destinasi['id'] == 2) ? 'selected' : ''; ?>>Pulau Bali (Ubud, Uluwatu & Pantai)</option>
                <option value="Labuan Bajo" <?php echo ($destinasi['id'] == 4) ? 'selected' : ''; ?>>Labuan Bajo (Nusa Tenggara Timur)</option>
                <option value="Raja Ampat" <?php echo ($destinasi['id'] == 1) ? 'selected' : ''; ?>>Raja Ampat (Papua Barat Daya)</option>
                <option value="Gunung Bromo" <?php echo ($destinasi['id'] == 3) ? 'selected' : ''; ?>>Gunung Bromo (Jawa Timur)</option>
                <option value="Danau Toba" <?php echo ($destinasi['id'] == 5) ? 'selected' : ''; ?>>Danau Toba (Sumatera Utara)</option>
                <option value="Yogyakarta" <?php echo ($destinasi['id'] == 6) ? 'selected' : ''; ?>>Yogyakarta & Candi</option>
              </select>
            </div>

            <!-- Durasi Hari -->
            <div class="mb-3">
              <label for="planDaysInput" class="form-label small fw-bold text-dark">Durasi Perjalanan (Hari)</label>
              <select id="planDaysInput" class="form-select" required>
                <option value="3">3 Hari 2 Malam (Liburan Singkat)</option>
                <option value="4" selected>4 Hari 3 Malam (Rekomendasi Terbaik)</option>
                <option value="5">5 Hari 4 Malam (Eksplorasi Lengkap)</option>
                <option value="7">7 Hari (Ekowisata Santai)</option>
              </select>
            </div>

            <!-- Tipe Wisatawan -->
            <div class="mb-4">
              <label for="planTypeSelect" class="form-label small fw-bold text-dark">Kategori Traveler</label>
              <select id="planTypeSelect" class="form-select" required>
                <option value="Pasangan & Keluarga" selected>Pasangan & Keluarga</option>
                <option value="Solo Traveler">Solo Traveler (Mandiri & Santai)</option>
                <option value="Teman & Grup">Teman & Kelompok Traveler</option>
              </select>
            </div>

            <button type="submit" class="btn btn-forest w-100 py-2">
              <i class="bi bi-magic me-1"></i> Simpan ke Rencana Perjalanan
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       MODAL: DESTINASI FAVORIT SAYA
       ====================================================================== -->
  <div class="modal fade" id="favoritesModal" tabindex="-1" aria-labelledby="favoritesModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-heart-fill text-danger fs-5"></i>
            <h5 class="fw-bold text-dark mb-0" id="favoritesModalTitle">Destinasi Favorit Saya</h5>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          
          <!-- Empty State -->
          <div id="favoritesEmptyState" class="text-center py-4">
            <i class="bi bi-heart text-muted fs-1 d-block mb-3"></i>
            <h6 class="fw-bold text-dark">Belum Ada Favorit</h6>
            <p class="text-muted small">Klik tombol "Simpan Favorit" untuk menyimpan destinasi ini ke daftar favoritmu.</p>
          </div>

          <!-- List of Favorites (Rendered by JS) -->
          <div id="favoritesListContainer"></div>

        </div>
        <div class="modal-footer bg-white d-flex justify-content-between">
          <a href="favorit.php" class="btn btn-forest rounded-pill btn-sm px-3">
            <i class="bi bi-grid me-1"></i> Buka Halaman Favorit Lengkap
          </a>
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ======================================================================
       MODAL: LOGIN & DAFTAR (AUTHENTICATION)
       ====================================================================== -->
  <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header border-bottom-0 pb-0">
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4 pt-0">
          <div class="text-center mb-4">
            <div class="brand-leaf-icon mx-auto mb-2" style="width: 52px; height: 52px; padding: 4px;">
              <img src="../assets/images/logo-icon.svg" alt="JelajahNusa" class="brand-icon-svg">
            </div>
            <h4 class="fw-bold text-dark mb-1" id="authModalTitle">Masuk ke JelajahNusa</h4>
            <p class="text-muted small">Discover Indonesia, Your Way.</p>
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
          <form id="loginForm" method="POST" action="../auth/login.php">
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
          <form id="registerForm" class="d-none" method="POST" action="../auth/register.php">
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
                Saya menyetujui Ketentuan Penggunaan JelajahNusa.
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
       MODAL: TAMBAHKAN KE TRIP (#addToTripModal)
       Target dari semua tombol .btn-add-to-trip-trigger di halaman ini.
       ====================================================================== -->
  <div class="modal fade" id="addToTripModal" tabindex="-1" aria-labelledby="addToTripModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">

        <div class="modal-header border-bottom-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="brand-leaf-icon" style="width: 36px; height: 36px; font-size: 1rem;">
              <i class="bi bi-calendar2-plus"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-0" id="addToTripModalTitle">Tambahkan ke Rencana Perjalanan</h5>
              <span class="text-muted small" id="addToTripItemSubtitle">Pilih trip atau buat baru</span>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">

          <!-- Alert feedback -->
          <div id="addToTripAlert" class="alert d-none py-2 px-3 small rounded-3 mb-3" role="alert"></div>

          <!-- INFO: item yang akan ditambahkan -->
          <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:var(--c-offwhite); border:1px solid var(--c-beige);">
            <img id="addToTripItemPhoto"
                 src="" alt="" loading="lazy"
                 style="width:60px;height:60px;object-fit:cover;border-radius:10px;flex-shrink:0;"
                 onerror="this.style.display='none'">
            <div class="overflow-hidden">
              <div class="fw-bold text-dark text-truncate" id="addToTripItemName">—</div>
              <div class="text-muted small" id="addToTripItemMeta"></div>
            </div>
          </div>

          <!-- Tabs: Pilih Trip Lama vs Buat Baru -->
          <ul class="nav nav-pills mb-4 p-1 rounded-pill border bg-white" id="addToTripTabs" role="tablist" style="gap:4px;">
            <li class="nav-item flex-fill" role="presentation">
              <button class="nav-link active w-100 rounded-pill fw-bold small py-1"
                      id="tab-existing-btn" data-bs-toggle="pill"
                      data-bs-target="#tabExisting" type="button" role="tab">
                <i class="bi bi-list-check me-1"></i> Trip Saya
              </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
              <button class="nav-link w-100 rounded-pill fw-bold small py-1"
                      id="tab-new-btn" data-bs-toggle="pill"
                      data-bs-target="#tabNew" type="button" role="tab">
                <i class="bi bi-plus-circle me-1"></i> Buat Trip Baru
              </button>
            </li>
          </ul>

          <div class="tab-content" id="addToTripTabContent">

            <!-- ── Tab 1: Pilih Trip yang Sudah Ada ─────────────────────── -->
            <div class="tab-pane fade show active" id="tabExisting" role="tabpanel">

              <!-- State: loading -->
              <div id="existingTripsLoading" class="text-center py-3">
                <span class="spinner-border spinner-border-sm text-success me-2"></span>Memuat daftar trip...
              </div>

              <!-- State: belum login -->
              <div id="existingTripsGuest" class="text-center py-4 d-none">
                <i class="bi bi-lock fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted mb-3 small">Login terlebih dahulu untuk menyimpan ke rencana perjalanan.</p>
                <button type="button" class="btn btn-forest btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal"
                        data-bs-toggle="modal"
                        data-bs-target="#authModal">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Login Sekarang
                </button>
              </div>

              <!-- State: belum ada trip -->
              <div id="existingTripsEmpty" class="text-center py-4 d-none">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted mb-3 small">Kamu belum punya rencana perjalanan. Buat trip baru terlebih dahulu.</p>
                <button type="button" class="btn btn-outline-forest btn-sm rounded-pill px-4" id="switchToNewTripTab">
                  <i class="bi bi-plus-circle me-1"></i> Buat Trip Baru
                </button>
              </div>

              <!-- Form pilih trip + hari -->
              <form id="addToExistingTripForm" class="d-none">
                <div class="mb-3">
                  <label for="existingTripSelect" class="form-label small fw-bold text-dark">
                    Pilih Rencana Perjalanan <span class="text-danger">*</span>
                  </label>
                  <select id="existingTripSelect" class="form-select" required>
                    <!-- Diisi JS -->
                  </select>
                  <div class="text-muted small mt-1" id="existingTripInfo"></div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label for="existingDaySelect" class="form-label small fw-bold text-dark">
                      Hari Perjalanan <span class="text-danger">*</span>
                    </label>
                    <select id="existingDaySelect" class="form-select" required>
                      <!-- Diisi JS berdasarkan trip terpilih -->
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <label for="existingTimeInput" class="form-label small fw-bold text-dark">Waktu / Jam</label>
                    <input type="text" class="form-control" id="existingTimeInput" placeholder="Contoh: 09:00">
                  </div>
                </div>

                <div class="mb-4">
                  <label for="existingNoteInput" class="form-label small fw-bold text-dark">Catatan (Opsional)</label>
                  <input type="text" class="form-control" id="existingNoteInput"
                         placeholder="Contoh: Datang sebelum matahari terbenam">
                </div>

                <button type="submit" class="btn btn-forest w-100 py-2 rounded-pill fw-bold" id="addToExistingTripBtn">
                  <i class="bi bi-plus-circle me-1"></i> Tambahkan ke Trip Ini
                </button>
              </form>
            </div>

            <!-- ── Tab 2: Buat Trip Baru ──────────────────────────────── -->
            <div class="tab-pane fade" id="tabNew" role="tabpanel">

              <!-- State: belum login -->
              <div id="newTripGuest" class="text-center py-4 d-none">
                <i class="bi bi-lock fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted mb-3 small">Login terlebih dahulu untuk membuat rencana perjalanan.</p>
                <button type="button" class="btn btn-forest btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal"
                        data-bs-toggle="modal"
                        data-bs-target="#authModal">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Login Sekarang
                </button>
              </div>

              <form id="createNewTripForm" class="<?php echo isset($_SESSION['user']) ? '' : 'd-none'; ?>">
                <div class="mb-3">
                  <label for="newTripName" class="form-label small fw-bold text-dark">
                    Nama Perjalanan <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="newTripName"
                         placeholder="Contoh: Liburan Eksplorasi <?php echo htmlspecialchars($destinasi['nama'] ?? 'Nusantara'); ?> 2026"
                         maxlength="150" required>
                </div>

                <div class="mb-3">
                  <label for="newTripDest" class="form-label small fw-bold text-dark">
                    Destinasi / Wilayah <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="newTripDest"
                         value="<?php echo htmlspecialchars($destinasi['wilayah'] ?? $destinasi['nama'] ?? ''); ?>"
                         placeholder="Contoh: Bali, Indonesia" maxlength="200" required>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label for="newTripStart" class="form-label small fw-bold text-dark">
                      Tanggal Berangkat <span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control" id="newTripStart" required>
                  </div>
                  <div class="col-sm-6">
                    <label for="newTripEnd" class="form-label small fw-bold text-dark">
                      Tanggal Pulang <span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control" id="newTripEnd" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="newTripCategory" class="form-label small fw-bold text-dark">Kategori Traveler</label>
                  <select id="newTripCategory" class="form-select">
                    <option value="Pasangan & Keluarga" selected>Pasangan &amp; Keluarga</option>
                    <option value="Solo Traveler">Solo Traveler</option>
                    <option value="Teman & Grup">Teman &amp; Kelompok</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="newTripNotes" class="form-label small fw-bold text-dark">Catatan Perjalanan (Opsional)</label>
                  <textarea class="form-control" id="newTripNotes" rows="2"
                            placeholder="Contoh: Fokus ke alam Ubud dan sunset Uluwatu..."></textarea>
                </div>

                <!-- Item yang sedang dipilih akan otomatis ditambahkan ke Hari 1 -->
                <div class="alert alert-info py-2 px-3 small rounded-3 mb-4">
                  <i class="bi bi-info-circle-fill me-1"></i>
                  Item yang dipilih akan otomatis ditambahkan ke <strong>Hari 1</strong> trip baru ini.
                </div>

                <button type="submit" class="btn btn-forest w-100 py-2 rounded-pill fw-bold" id="createNewTripBtn">
                  <i class="bi bi-check2-circle me-1"></i> Buat Trip &amp; Tambahkan Item
                </button>
              </form>
            </div>

          </div><!-- /.tab-content -->
        </div><!-- /.modal-body -->

        <div class="modal-footer bg-white">
          <a href="rencana-perjalanan.php" class="btn btn-outline-forest btn-sm rounded-pill px-3">
            <i class="bi bi-map me-1"></i> Buka Travel Planner
          </a>
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- Custom Nature-Inspired JavaScript -->
  <script src="../assets/js/script.js"></script>

</body>
</html>
