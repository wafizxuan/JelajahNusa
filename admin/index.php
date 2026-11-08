<?php
/**
 * JelajahNusa - Dashboard Utama Administrator
 * Lokasi: admin/index.php
 * Konsep: Nature-Inspired, Aesthetic, Clean, Professional
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/destinasi.php';

// Data Ringkasan Sistem
$total_users = 0;
try {
    $total_users = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (PDOException $e) {
    error_log('[Admin Stats Error] ' . $e->getMessage());
}

// Total destinasi diambil dari database MySQL (dengan fallback ke data statis)
$total_destinasi = 0;
try {
    if (isset($pdo)) {
        $total_destinasi = (int)$pdo->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
    }
} catch (Throwable $e) {
    error_log('[Admin Destinations Count Error] ' . $e->getMessage());
}
if ($total_destinasi === 0 && isset($all_destinasi) && is_array($all_destinasi)) {
    $total_destinasi = count($all_destinasi);
}

$total_reviews = 0;
try {
  $total_reviews = (int)$pdo->query("SELECT COUNT(*) FROM destination_reviews WHERE status <> 'hidden'")->fetchColumn();
} catch (PDOException $e) {
  error_log('[Admin Reviews Count Error] ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | JelajahNusa</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3 CSS & Icons -->
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

    /* Sidebar Styling */
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
      font-weight: 600;
      font-size: 0.92rem;
      transition: all 0.2s ease;
    }

    .admin-menu-link:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.08);
    }

    .admin-menu-link.active {
      background: var(--c-forest);
      color: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .admin-footer-actions {
      padding: 18px 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    /* Main Content Area */
    .admin-main {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: margin-left 0.3s ease;
    }

    .admin-topbar {
      background: #fff;
      padding: 16px 30px;
      border-bottom: 1px solid rgba(52, 91, 73, 0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1020;
    }

    .admin-body {
      padding: 32px 30px;
      flex: 1;
    }

    /* Cards */
    .stat-card {
      background: #fff;
      border-radius: 16px;
      padding: 24px;
      border: 1px solid rgba(52, 91, 73, 0.08);
      box-shadow: 0 4px 16px rgba(41, 53, 45, 0.03);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(41, 53, 45, 0.06);
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
    }

    .welcome-hero {
      background: linear-gradient(135deg, var(--c-forest) 0%, var(--c-dark-forest) 100%);
      color: #fff;
      border-radius: 20px;
      padding: 32px 28px;
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(41, 53, 45, 0.12);
    }

    .welcome-hero::after {
      content: '';
      position: absolute;
      top: -50px;
      right: -50px;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(168, 185, 154, 0.25) 0%, transparent 70%);
      border-radius: 50%;
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
  </style>
</head>
<body>

  <!-- ======================================================================
       1. SIDEBAR NAVIGASI
       ====================================================================== -->
  <aside class="admin-sidebar" id="adminSidebar">
    
    <a href="index.php" class="admin-brand" aria-label="JelajahNusa Admin Panel">
      <img class="admin-brand-logo"
        src="../assets/images/logo-horizontal.svg"
        alt="JelajahNusa — Admin Panel"
        width="180" height="46">
      <div class="text-white-50 small mt-1" style="font-size: 0.7rem; letter-spacing: 0.06em; text-transform: uppercase; padding-left: 2px;">Admin Control Panel</div>
    </a>

    <ul class="admin-menu-list">
      <li>
        <a href="index.php" class="admin-menu-link active">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="destinasi.php" class="admin-menu-link">
          <i class="bi bi-geo-alt-fill"></i>
          <span>Manajemen Destinasi</span>
        </a>
      </li>
      <li>
        <a href="users.php" class="admin-menu-link">
          <i class="bi bi-people-fill"></i>
          <span>Manajemen Pengguna</span>
        </a>
      </li>
      <li>
        <a href="reviews.php" class="admin-menu-link">
          <i class="bi bi-chat-square-text-fill"></i>
          <span>Moderasi Ulasan</span>
        </a>
      </li>
    </ul>

    <div class="admin-footer-actions">
      <a href="../index.php" class="btn btn-outline-light btn-sm rounded-pill py-2 w-100 text-decoration-none">
        <i class="bi bi-globe2 me-1"></i> Kembali ke Website
      </a>
      <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill py-2 w-100 text-decoration-none" style="background: rgba(220, 53, 69, 0.2); border-color: rgba(220, 53, 69, 0.4); color: #ffb8b8;">
        <i class="bi bi-box-arrow-right me-1"></i> Keluar
      </a>
    </div>

  </aside>

  <!-- ======================================================================
       2. KONTEN UTAMA DASHBOARD
       ====================================================================== -->
  <div class="admin-main">
    
    <!-- Topbar -->
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link text-dark d-lg-none p-0 fs-4" type="button" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
          <i class="bi bi-list"></i>
        </button>
        <h5 class="fw-bold text-dark mb-0">Dashboard Ringkasan</h5>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill small">
          <i class="bi bi-shield-lock-fill me-1"></i> Administrator
        </span>
        <div class="dropdown">
          <button class="btn btn-light btn-sm rounded-pill dropdown-toggle px-3 py-2 fw-semibold border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle text-success me-1"></i>
            <span><?php echo htmlspecialchars($admin_user['name']); ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
            <li><span class="dropdown-item-text text-muted small"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($admin_user['email']); ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger small" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Body Content -->
    <main class="admin-body">
      
      <!-- Welcome Hero Banner -->
      <div class="welcome-hero">
        <div class="position-relative" style="z-index: 2;">
          <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 small fw-semibold mb-2">
            Panel Administrator
          </span>
          <h2 class="fw-bold mb-2">
            Selamat Datang, <?php echo htmlspecialchars($admin_user['name']); ?>!
          </h2>
          <p class="text-white-50 mb-0" style="max-width: 650px;">
            Kelola konten wisata alam nusantara, tinjau akun pengguna terdaftar, dan pastikan ekosistem JelajahNusa senantiasa menyajikan panduan perjalanan terbaik.
          </p>
        </div>
      </div>

      <!-- Quick Stats Grid -->
      <div class="row g-4 mb-4">
        
        <!-- Total Destinasi -->
        <div class="col-md-6 col-xl-4">
          <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Total Destinasi</span>
              <h2 class="fw-bold text-dark mb-0 mt-1"><?php echo $total_destinasi; ?></h2>
              <span class="text-success small fw-semibold"><i class="bi bi-check2-circle me-1"></i>Katalog Wisata Aktif</span>
            </div>
            <div class="stat-icon" style="background: rgba(52, 91, 73, 0.1); color: var(--c-forest);">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
          </div>
        </div>

        <!-- Total Pengguna -->
        <div class="col-md-6 col-xl-4">
          <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Total Pengguna</span>
              <h2 class="fw-bold text-dark mb-0 mt-1"><?php echo $total_users; ?></h2>
              <span class="text-muted small"><i class="bi bi-people me-1"></i>Akun Terdaftar di Sistem</span>
            </div>
            <div class="stat-icon" style="background: rgba(168, 185, 154, 0.2); color: var(--c-dark-forest);">
              <i class="bi bi-people-fill"></i>
            </div>
          </div>
        </div>

        <!-- Status Sistem -->
        <div class="col-md-6 col-xl-4">
          <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Status Database</span>
              <h4 class="fw-bold text-success mb-0 mt-1">Terhubung</h4>
              <span class="text-muted small"><i class="bi bi-database-check me-1"></i>MySQL 8.0 (Laragon)</span>
            </div>
            <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
              <i class="bi bi-hdd-network-fill"></i>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-xl-4">
          <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-bold text-uppercase">Ulasan Publik</span>
              <h2 class="fw-bold text-dark mb-0 mt-1"><?php echo $total_reviews; ?></h2>
              <a class="text-success small fw-semibold" href="reviews.php">Kelola ulasan <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="stat-icon" style="background: rgba(255, 193, 7, 0.14); color: #8a6500;">
              <i class="bi bi-chat-square-text"></i>
            </div>
          </div>
        </div>

      </div>

      <!-- Quick Action Cards -->
      <div class="row g-4">
        
        <div class="col-md-6">
          <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="brand-icon-box" style="background: rgba(52, 91, 73, 0.1); color: var(--c-forest);">
                <i class="bi bi-geo-alt"></i>
              </div>
              <div>
                <h5 class="fw-bold text-dark mb-0">Manajemen Destinasi</h5>
                <span class="text-muted small">Kelola katalog wisata nusantara</span>
              </div>
            </div>
            <p class="text-muted small mb-4">
              Akses daftar 6 destinasi unggulan (Raja Ampat, Bali, Bromo, Labuan Bajo, Danau Toba, Yogyakarta) dan persiapkan penambahan destinasi baru.
            </p>
            <a href="destinasi.php" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold text-decoration-none">
              <i class="bi bi-arrow-right-circle me-1"></i> Buka Manajemen Destinasi
            </a>
          </div>
        </div>

        <div class="col-md-6">
          <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="brand-icon-box" style="background: rgba(255, 193, 7, 0.14); color: #8a6500;">
                <i class="bi bi-chat-square-text"></i>
              </div>
              <div>
                <h5 class="fw-bold text-dark mb-0">Moderasi Ulasan</h5>
                <span class="text-muted small">Kelola ulasan pengguna</span>
              </div>
            </div>
            <p class="text-muted small mb-4">Tinjau ulasan pengguna dan sembunyikan atau hapus konten yang tidak sesuai.</p>
            <a href="reviews.php" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold text-decoration-none">
              <i class="bi bi-arrow-right-circle me-1"></i> Buka Moderasi Ulasan
            </a>
          </div>
        </div>

        <div class="col-md-6">
          <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="brand-icon-box" style="background: rgba(168, 185, 154, 0.2); color: var(--c-dark-forest);">
                <i class="bi bi-people"></i>
              </div>
              <div>
                <h5 class="fw-bold text-dark mb-0">Manajemen Pengguna</h5>
                <span class="text-muted small">Tinjau akun terdaftar JelajahNusa</span>
              </div>
            </div>
            <p class="text-muted small mb-4">
              Lihat seluruh pengguna yang terdaftar di sistem dengan aman tanpa menampilkan hash password ke publik atau admin.
            </p>
            <a href="users.php" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold text-decoration-none">
              <i class="bi bi-arrow-right-circle me-1"></i> Buka Manajemen Pengguna
            </a>
          </div>
        </div>

      </div>

    </main>

  </div>

  <!-- Bootstrap 5.3 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Responsive Sidebar Toggle for Mobile
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    if (sidebarToggleBtn && adminSidebar) {
      sidebarToggleBtn.addEventListener('click', () => {
        adminSidebar.classList.toggle('show');
      });
      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', (e) => {
        if (window.innerWidth < 992 && !adminSidebar.contains(e.target) && !sidebarToggleBtn.contains(e.target)) {
          adminSidebar.classList.remove('show');
        }
      });
    }
  </script>

</body>
</html>
