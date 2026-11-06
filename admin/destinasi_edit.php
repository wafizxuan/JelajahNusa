<?php
/**
 * JelajahNusa - Edit Destinasi (Update)
 * Lokasi: admin/destinasi_edit.php
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$destId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($destId <= 0) {
    $_SESSION['flash_error'] = 'Parameter ID destinasi tidak valid.';
    header('Location: destinasi.php');
    exit;
}

// Ambil data destinasi dari database
try {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->execute([$destId]);
    $dest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dest) {
        $_SESSION['flash_error'] = 'Destinasi dengan ID #' . $destId . ' tidak ditemukan di database.';
        header('Location: destinasi.php');
        exit;
    }
} catch (Throwable $e) {
    error_log('[Admin Edit Dest Fetch Error] ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan saat memuat data destinasi: ' . $e->getMessage();
    header('Location: destinasi.php');
    exit;
}

$errors = [];
$formData = $dest; // default isi form dengan data database saat ini

// Decode tags untuk form input comma-separated
$tagsDecoded = is_string($dest['card_tags']) ? json_decode($dest['card_tags'], true) : [];
$formData['card_tags'] = is_array($tagsDecoded) ? implode(', ', $tagsDecoded) : '';

// Decode waktu terbaik untuk 3 input terpisah
$wtDecoded = is_string($dest['waktu_terbaik']) ? json_decode($dest['waktu_terbaik'], true) : [];
$formData['wt_musim'] = $wtDecoded['musim'] ?? '';
$formData['wt_cuaca'] = $wtDecoded['cuaca'] ?? '';
$formData['wt_tips']  = $wtDecoded['tips'] ?? '';

// Pretty print untuk JSON textareas agar rapi dibaca admin
$jsonFieldsToPretty = ['daya_tarik', 'popular_places', 'recommended_stays', 'local_culinary', 'things_to_do', 'travel_info'];
foreach ($jsonFieldsToPretty as $jf) {
    if (isset($formData[$jf]) && is_string($formData[$jf])) {
        $decoded = json_decode($formData[$jf], true);
        if ($decoded !== null) {
            $formData[$jf] = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}

// Proses submit form update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token keamanan CSRF tidak valid. Silakan coba kembali.';
    }

    $formData = array_merge($formData, $_POST);

    $nama           = trim($_POST['nama'] ?? '');
    $slug           = trim($_POST['slug'] ?? '');
    $provinsi       = trim($_POST['provinsi'] ?? '');
    $wilayah        = trim($_POST['wilayah'] ?? '');
    $kategori       = trim($_POST['kategori'] ?? '');
    $rating         = (float)($_POST['rating'] ?? 5.0);
    $total_ulasan   = (int)($_POST['total_ulasan'] ?? 0);
    $hero_image     = trim($_POST['hero_image'] ?? '');
    $tagline        = trim($_POST['tagline'] ?? '');
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $about_heading  = trim($_POST['about_heading'] ?? '');

    $card_nama      = trim($_POST['card_nama'] ?? '');
    $card_lokasi    = trim($_POST['card_lokasi'] ?? '');
    $card_kategori  = trim($_POST['card_kategori'] ?? '');
    $card_kategori_id = trim($_POST['card_kategori_id'] ?? 'alam');
    $card_rating    = (float)($_POST['card_rating'] ?? 5.0);
    $card_image     = trim($_POST['card_image'] ?? '');
    $card_deskripsi = trim($_POST['card_deskripsi'] ?? '');
    $card_tags_raw  = trim($_POST['card_tags'] ?? '');
    $card_highlights= trim($_POST['card_highlights'] ?? '');

    // Validasi field wajib
    if (empty($nama)) $errors[] = 'Nama destinasi wajib diisi.';
    if (empty($provinsi)) $errors[] = 'Provinsi wajib diisi.';
    if (empty($wilayah)) $errors[] = 'Wilayah lengkap wajib diisi.';
    if (empty($hero_image)) $errors[] = 'URL Hero Image wajib diisi.';
    if (empty($deskripsi)) $errors[] = 'Deskripsi lengkap wajib diisi.';

    // Auto slugify jika dikosongkan
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama), '-'));
    }

    // Periksa keunikan slug (kecuali destinasi yang sedang diedit ini)
    if (!empty($slug)) {
        $stmtSlug = $pdo->prepare("SELECT id FROM destinations WHERE slug = ? AND id != ? LIMIT 1");
        $stmtSlug->execute([$slug, $destId]);
        if ($stmtSlug->fetch()) {
            $errors[] = 'Slug URL "' . htmlspecialchars($slug) . '" sudah digunakan oleh destinasi lain. Harap pilih slug yang unik.';
        }
    }

    // Default card data jika kosong
    if (empty($card_nama)) $card_nama = $nama;
    if (empty($card_lokasi)) $card_lokasi = $provinsi;
    if (empty($card_kategori)) $card_kategori = $kategori;
    if (empty($card_image)) $card_image = $hero_image;
    if (empty($card_deskripsi)) $card_deskripsi = mb_substr($deskripsi, 0, 150) . '...';
    if (empty($about_heading)) $about_heading = 'Pesona Keindahan ' . $nama;
    if (empty($card_highlights)) $card_highlights = 'Eksplorasi alam dan pesona di ' . $nama;

    // Olah Card Tags
    if (!empty($card_tags_raw)) {
        $tagsArray = array_values(array_filter(array_map('trim', explode(',', $card_tags_raw))));
    } else {
        $tagsArray = [$kategori, 'Wisata', 'Nusantara'];
    }
    $card_tags = json_encode($tagsArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Olah Waktu Terbaik
    $waktu_terbaik_arr = [
        'musim' => trim($_POST['wt_musim'] ?? 'Mei - Oktober (Musim Kemarau)'),
        'cuaca' => trim($_POST['wt_cuaca'] ?? 'Cerah dan nyaman untuk wisata.'),
        'tips'  => trim($_POST['wt_tips'] ?? 'Bawa perlengkapan yang sesuai.')
    ];
    $waktu_terbaik = json_encode($waktu_terbaik_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Validasi 6 Field JSON
    // Fallback: jika field kosong dari POST (JS gagal), pertahankan data lama dari database
    $jsonFields = [
        'daya_tarik'        => (!empty($_POST['daya_tarik'])        ? $_POST['daya_tarik']        : ($dest['daya_tarik']         ?? '')),
        'popular_places'    => (!empty($_POST['popular_places'])    ? $_POST['popular_places']    : ($dest['popular_places']      ?? '')),
        'recommended_stays' => (!empty($_POST['recommended_stays']) ? $_POST['recommended_stays'] : ($dest['recommended_stays']   ?? '')),
        'local_culinary'    => (!empty($_POST['local_culinary'])    ? $_POST['local_culinary']    : ($dest['local_culinary']      ?? '')),
        'things_to_do'      => (!empty($_POST['things_to_do'])      ? $_POST['things_to_do']      : ($dest['things_to_do']        ?? '')),
        'travel_info'       => (!empty($_POST['travel_info'])       ? $_POST['travel_info']       : ($dest['travel_info']         ?? '')),
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

    // Jika tidak ada error, lakukan UPDATE
    if (empty($errors)) {
        try {
            $sql = "UPDATE destinations SET
                slug = :slug,
                nama = :nama,
                provinsi = :provinsi,
                wilayah = :wilayah,
                kategori = :kategori,
                rating = :rating,
                total_ulasan = :total_ulasan,
                hero_image = :hero_image,
                tagline = :tagline,
                deskripsi = :deskripsi,
                about_heading = :about_heading,
                card_nama = :card_nama,
                card_lokasi = :card_lokasi,
                card_kategori = :card_kategori,
                card_kategori_id = :card_kategori_id,
                card_rating = :card_rating,
                card_image = :card_image,
                card_deskripsi = :card_deskripsi,
                card_tags = :card_tags,
                card_highlights = :card_highlights,
                daya_tarik = :daya_tarik,
                waktu_terbaik = :waktu_terbaik,
                popular_places = :popular_places,
                recommended_stays = :recommended_stays,
                local_culinary = :local_culinary,
                things_to_do = :things_to_do,
                travel_info = :travel_info
            WHERE id = :id";

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
                ':daya_tarik'       => $validatedJson['daya_tarik'],
                ':waktu_terbaik'    => $waktu_terbaik,
                ':popular_places'   => $validatedJson['popular_places'],
                ':recommended_stays'=> $validatedJson['recommended_stays'],
                ':local_culinary'   => $validatedJson['local_culinary'],
                ':things_to_do'     => $validatedJson['things_to_do'],
                ':travel_info'      => $validatedJson['travel_info'],
                ':id'               => $destId,
            ]);

            $_SESSION['flash_success'] = 'Destinasi "' . htmlspecialchars($nama) . '" (ID #' . $destId . ') berhasil diperbarui!';
            header('Location: destinasi.php');
            exit;

        } catch (Throwable $e) {
            error_log('[Admin Edit Dest Update Error] ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan pembaruan ke database: ' . $e->getMessage();
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
  <title>Edit Destinasi #<?php echo $destId; ?> — <?php echo htmlspecialchars($dest['nama']); ?> | JelajahNusa Admin</title>
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
          <span class="fw-bold text-dark small">Edit Destinasi #<?php echo $destId; ?></span>
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

      <form method="POST" action="destinasi_edit.php?id=<?php echo $destId; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="id" value="<?php echo $destId; ?>">

        <!-- Header Card -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-light text-dark border">ID #<?php echo $destId; ?></span>
              <h4 class="fw-bold text-dark mb-0">Edit Destinasi: <?php echo htmlspecialchars($dest['nama']); ?></h4>
            </div>
            <p class="text-muted small mb-0">Perbarui informasi destinasi ini di MySQL. Seluruh perubahan langsung tersinkronisasi ke website.</p>
          </div>
          <div class="d-flex gap-2">
            <a href="destinasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">Batal</a>
            <a href="../pages/detail-destinasi.php?id=<?php echo $destId; ?>" target="_blank" class="btn btn-outline-forest btn-sm rounded-pill px-3 py-2">
              <i class="bi bi-eye me-1"></i> Preview Detail
            </a>
            <button type="submit" class="btn btn-forest btn-sm rounded-pill px-4 py-2 fw-semibold">
              <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
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
              <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($formData['nama'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="slug" class="form-label small fw-bold text-dark">Slug URL (Unik) <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($formData['slug'] ?? ''); ?>" required>
            </div>
            <div class="col-md-4">
              <label for="provinsi" class="form-label small fw-bold text-dark">Provinsi <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="provinsi" name="provinsi" value="<?php echo htmlspecialchars($formData['provinsi'] ?? ''); ?>" required>
            </div>
            <div class="col-md-5">
              <label for="wilayah" class="form-label small fw-bold text-dark">Wilayah Lengkap <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="wilayah" name="wilayah" value="<?php echo htmlspecialchars($formData['wilayah'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3">
              <label for="kategori" class="form-label small fw-bold text-dark">Kategori Detail <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo htmlspecialchars($formData['kategori'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3">
              <label for="rating" class="form-label small fw-bold text-dark">Rating Detail (1.0 - 5.0)</label>
              <input type="number" step="0.1" min="1.0" max="5.0" class="form-control" id="rating" name="rating" value="<?php echo htmlspecialchars($formData['rating'] ?? '5.0'); ?>" required>
            </div>
            <div class="col-md-3">
              <label for="total_ulasan" class="form-label small fw-bold text-dark">Total Ulasan</label>
              <input type="number" min="0" class="form-control" id="total_ulasan" name="total_ulasan" value="<?php echo htmlspecialchars($formData['total_ulasan'] ?? '0'); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="hero_image" class="form-label small fw-bold text-dark">URL Hero Image (1920px) <span class="text-danger">*</span></label>
              <input type="url" class="form-control" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($formData['hero_image'] ?? ''); ?>" required>
            </div>
            <div class="col-12">
              <label for="tagline" class="form-label small fw-bold text-dark">Tagline Inspiratif</label>
              <input type="text" class="form-control" id="tagline" name="tagline" value="<?php echo htmlspecialchars($formData['tagline'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="about_heading" class="form-label small fw-bold text-dark">Heading Seksi Tentang Destinasi</label>
              <input type="text" class="form-control" id="about_heading" name="about_heading" value="<?php echo htmlspecialchars($formData['about_heading'] ?? ''); ?>">
            </div>
            <div class="col-12">
              <label for="deskripsi" class="form-label small fw-bold text-dark">Deskripsi Lengkap Halaman Detail <span class="text-danger">*</span></label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($formData['deskripsi'] ?? ''); ?></textarea>
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
              <input type="text" class="form-control" id="card_nama" name="card_nama" value="<?php echo htmlspecialchars($formData['card_nama'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label for="card_lokasi" class="form-label small fw-bold text-dark">Lokasi Kartu (Ringkas)</label>
              <input type="text" class="form-control" id="card_lokasi" name="card_lokasi" value="<?php echo htmlspecialchars($formData['card_lokasi'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label for="card_kategori" class="form-label small fw-bold text-dark">Label Kategori Kartu</label>
              <input type="text" class="form-control" id="card_kategori" name="card_kategori" value="<?php echo htmlspecialchars($formData['card_kategori'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label for="card_kategori_id" class="form-label small fw-bold text-dark">ID Kategori Filter Homepage</label>
              <select class="form-select" id="card_kategori_id" name="card_kategori_id" required>
                <option value="pantai" <?php echo (($formData['card_kategori_id'] ?? '') === 'pantai') ? 'selected' : ''; ?>>pantai (Wisata Pantai)</option>
                <option value="budaya" <?php echo (($formData['card_kategori_id'] ?? '') === 'budaya') ? 'selected' : ''; ?>>budaya (Wisata Budaya)</option>
                <option value="pegunungan" <?php echo (($formData['card_kategori_id'] ?? '') === 'pegunungan') ? 'selected' : ''; ?>>pegunungan (Wisata Pegunungan)</option>
                <option value="alam" <?php echo (($formData['card_kategori_id'] ?? '') === 'alam') ? 'selected' : ''; ?>>alam (Wisata Alam)</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="card_rating" class="form-label small fw-bold text-dark">Rating Kartu</label>
              <input type="number" step="0.1" min="1.0" max="5.0" class="form-control" id="card_rating" name="card_rating" value="<?php echo htmlspecialchars($formData['card_rating'] ?? '5.0'); ?>">
            </div>
            <div class="col-md-4">
              <label for="card_image" class="form-label small fw-bold text-dark">URL Gambar Kartu (800px)</label>
              <input type="url" class="form-control" id="card_image" name="card_image" value="<?php echo htmlspecialchars($formData['card_image'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label for="card_tags" class="form-label small fw-bold text-dark">Tags Kartu (Pisahkan dengan koma)</label>
              <input type="text" class="form-control" id="card_tags" name="card_tags" value="<?php echo htmlspecialchars($formData['card_tags'] ?? ''); ?>" placeholder="Contoh: Diving, Konservasi, Pantai">
            </div>
            <div class="col-md-6">
              <label for="card_highlights" class="form-label small fw-bold text-dark">Highlights Tempat di Kartu</label>
              <input type="text" class="form-control" id="card_highlights" name="card_highlights" value="<?php echo htmlspecialchars($formData['card_highlights'] ?? ''); ?>">
            </div>
            <div class="col-12">
              <label for="card_deskripsi" class="form-label small fw-bold text-dark">Ringkasan Deskripsi Kartu (1-2 Kalimat)</label>
              <textarea class="form-control" id="card_deskripsi" name="card_deskripsi" rows="2"><?php echo htmlspecialchars($formData['card_deskripsi'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <!-- 3. KONTEN TERSTRUKTUR & JSON -->
        <div class="content-card">
          <span class="section-header-pill" style="background: rgba(41, 53, 45, 0.08); color: var(--c-dark-forest);">
            <i class="bi bi-layout-text-sidebar-reverse"></i> 3. Data Kaya &amp; Panduan Wisata
          </span>
          <p class="text-muted small mb-3">Formulir visual di bawah memuat data destinasi yang tersimpan. Anda dapat mengedit, menambah, atau menghapus item langsung — tanpa perlu menulis JSON.</p>

          <!-- Waktu Terbaik -->
          <div class="card p-3 rounded-4 bg-light border-0 mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-brightness-high me-1 text-warning"></i> Waktu Terbaik Berkunjung</h6>
            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label small text-muted">Musim Ideal</label>
                <input type="text" class="form-control form-control-sm" name="wt_musim" value="<?php echo htmlspecialchars($formData['wt_musim'] ?? ''); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Kondisi Cuaca</label>
                <input type="text" class="form-control form-control-sm" name="wt_cuaca" value="<?php echo htmlspecialchars($formData['wt_cuaca'] ?? ''); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Tips Lokal</label>
                <input type="text" class="form-control form-control-sm" name="wt_tips" value="<?php echo htmlspecialchars($formData['wt_tips'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <!-- Daya Tarik Utama — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="daya_tarik" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-gem text-success me-1"></i> Daya Tarik Utama
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="daya_tarik" name="daya_tarik" rows="4"><?php echo htmlspecialchars($formData['daya_tarik'] ?? ''); ?></textarea>
          </div>

          <!-- Tempat Wisata Populer — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="popular_places" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-pin-map-fill text-success me-1"></i> Tempat Wisata Populer
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="popular_places" name="popular_places" rows="4"><?php echo htmlspecialchars($formData['popular_places'] ?? ''); ?></textarea>
          </div>

          <!-- Rekomendasi Penginapan — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="recommended_stays" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-house-heart-fill text-success me-1"></i> Rekomendasi Penginapan
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="recommended_stays" name="recommended_stays" rows="4"><?php echo htmlspecialchars($formData['recommended_stays'] ?? ''); ?></textarea>
          </div>

          <!-- Kuliner Khas — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="local_culinary" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-cup-hot-fill text-success me-1"></i> Kuliner Khas Autentik
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="local_culinary" name="local_culinary" rows="4"><?php echo htmlspecialchars($formData['local_culinary'] ?? ''); ?></textarea>
          </div>

          <!-- Aktivitas Wisata — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="things_to_do" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-compass-fill text-success me-1"></i> Aktivitas &amp; Pengalaman
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="things_to_do" name="things_to_do" rows="4"><?php echo htmlspecialchars($formData['things_to_do'] ?? ''); ?></textarea>
          </div>

          <!-- Panduan Perjalanan — form visual oleh JS -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="travel_info" class="form-label small fw-bold text-dark mb-0">
                <i class="bi bi-map-fill text-success me-1"></i> Panduan Perjalanan (Biaya, Transportasi, Tips, Musim)
              </label>
            </div>
            <textarea class="form-control json-code-editor" id="travel_info" name="travel_info" rows="4"><?php echo htmlspecialchars($formData['travel_info'] ?? ''); ?></textarea>
          </div>
        </div>

        <!-- Submit Bar -->
        <div class="d-flex justify-content-between align-items-center pb-5">
          <a href="destinasi.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
            <i class="bi bi-arrow-left me-1"></i> Batal / Kembali
          </a>
          <button type="submit" class="btn btn-forest rounded-pill px-5 py-2 fw-semibold shadow">
            <i class="bi bi-check2-circle me-1"></i> Simpan Pembaruan Destinasi
          </button>
        </div>
      </form>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- JSON Form Builder: form visual menggantikan textarea JSON -->
  <script src="json-form-builder.js"></script>
  <script>
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
