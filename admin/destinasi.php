<?php
/**
 * JelajahNusa - Manajemen Destinasi (CRUD)
 * Lokasi: admin/destinasi.php
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/destinasi.php';

// Handle Operasi Hapus Destinasi (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Token keamanan CSRF tidak valid. Silakan coba kembali.';
        header('Location: destinasi.php');
        exit;
    }

    $deleteId = (int)($_POST['id'] ?? 0);
    if ($deleteId > 0) {
        try {
            $stmt = $pdo->prepare("SELECT nama FROM destinations WHERE id = ?");
            $stmt->execute([$deleteId]);
            $destName = $stmt->fetchColumn();

            if ($destName) {
                $delStmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
                $delStmt->execute([$deleteId]);
                $_SESSION['flash_success'] = 'Destinasi "' . $destName . '" berhasil dihapus dari database.';
            } else {
                $_SESSION['flash_error'] = 'Destinasi dengan ID tersebut tidak ditemukan.';
            }
        } catch (Throwable $e) {
            error_log('[Admin Delete Dest Error] ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Terjadi kesalahan sistem saat menghapus destinasi.';
        }
    } else {
        $_SESSION['flash_error'] = 'ID destinasi tidak valid.';
    }

    header('Location: destinasi.php');
    exit;
}

// Ambil daftar destinasi dari database
$destinasi_list = [];
$is_from_db = false;
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT id, slug, hero_image, card_image, nama, card_nama, wilayah, provinsi, kategori, card_kategori, rating, card_rating FROM destinations ORDER BY id ASC");
        $destinasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($destinasi_list)) {
            $is_from_db = true;
        }
    }
} catch (Throwable $e) {
    error_log('[Admin Destinasi DB Error] ' . $e->getMessage());
}

// Fallback jika database belum berisi data
if (empty($destinasi_list) && isset($all_destinasi) && is_array($all_destinasi)) {
    foreach ($all_destinasi as $id => $d) {
        $destinasi_list[] = [
            'id' => $id,
            'slug' => $d['slug'] ?? 'destinasi',
            'hero_image' => $d['hero_image'],
            'card_image' => $d['hero_image'],
            'nama' => $d['nama'],
            'card_nama' => $d['nama'],
            'wilayah' => $d['wilayah'],
            'provinsi' => $d['provinsi'],
            'kategori' => $d['kategori'],
            'card_kategori' => $d['kategori'],
            'rating' => $d['rating'],
            'card_rating' => $d['rating'],
        ];
    }
}

$total_destinasi = count($destinasi_list);
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Destinasi | JelajahNusa Admin</title>
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
      padding: 24px;
      border: 1px solid rgba(52, 91, 73, 0.08);
      box-shadow: 0 4px 20px rgba(41, 53, 45, 0.03);
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
    .btn-outline-forest {
      border: 1px solid var(--c-forest);
      color: var(--c-forest);
      background: transparent;
    }
    .btn-outline-forest:hover {
      background-color: var(--c-forest);
      color: #fff;
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
        <h5 class="fw-bold text-dark mb-0">Manajemen Destinasi</h5>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill small">
          <i class="bi bi-shield-lock-fill me-1"></i> Administrator
        </span>
        <span class="text-muted small fw-semibold d-none d-sm-inline">
          <?php echo htmlspecialchars($admin_user['name']); ?>
        </span>
      </div>
    </header>

    <main class="admin-body">
      <!-- Flash Messages -->
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>

      <!-- Header & Action -->
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <h4 class="fw-bold text-dark mb-1">Daftar Destinasi Wisata</h4>
          <p class="text-muted small mb-0">Kelola dan perbarui seluruh destinasi unggulan nusantara secara langsung di MySQL.</p>
        </div>
        <a href="destinasi_tambah.php" class="btn btn-forest btn-sm rounded-pill px-3 py-2 fw-semibold text-decoration-none shadow-sm">
          <i class="bi bi-plus-circle me-1"></i> Tambah Destinasi Baru
        </a>
      </div>

      <!-- Content Card -->
      <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <h6 class="fw-bold text-dark mb-0">Destinasi Aktif (<span id="destCountBadge"><?php echo $total_destinasi; ?></span>)</h6>
            <span class="badge bg-success bg-opacity-10 text-success small"><i class="bi bi-database-check me-1"></i>MySQL Live</span>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <div class="input-group input-group-sm" style="max-width: 280px;">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" id="destSearchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama, wilayah, kategori...">
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="destTable">
            <thead class="table-light">
              <tr class="small text-muted">
                <th style="width: 70px;">ID</th>
                <th>Nama Destinasi</th>
                <th>Wilayah / Provinsi</th>
                <th>Kategori</th>
                <th>Rating</th>
                <th>Status</th>
                <th class="text-end" style="width: 140px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($destinasi_list)): ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted small">Belum ada data destinasi di database.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($destinasi_list as $d): ?>
                  <tr class="dest-row" data-name="<?php echo htmlspecialchars(strtolower($d['nama'])); ?>" data-location="<?php echo htmlspecialchars(strtolower($d['wilayah'])); ?>" data-category="<?php echo htmlspecialchars(strtolower($d['kategori'])); ?>">
                    <td class="fw-bold text-muted small">#<?php echo (int)$d['id']; ?></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img src="<?php echo htmlspecialchars($d['card_image'] ?? $d['hero_image']); ?>" alt="" class="rounded-3" style="width: 44px; height: 44px; object-fit: cover;">
                        <div>
                          <span class="fw-bold text-dark small d-block"><?php echo htmlspecialchars($d['nama']); ?></span>
                          <span class="text-muted" style="font-size: 0.75rem;">Slug: <code><?php echo htmlspecialchars($d['slug']); ?></code></span>
                        </div>
                      </div>
                    </td>
                    <td class="small text-muted"><?php echo htmlspecialchars($d['wilayah']); ?></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill small"><?php echo htmlspecialchars($d['kategori']); ?></span></td>
                    <td class="small fw-semibold text-warning"><i class="bi bi-star-fill me-1"></i><?php echo number_format($d['rating'], 1); ?></td>
                    <td>
                      <?php if ($is_from_db): ?>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small"><i class="bi bi-database-check me-1"></i>Tersimpan di MySQL</span>
                      <?php else: ?>
                        <span class="badge bg-light text-secondary border small">Statis (Fallback)</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-end">
                      <div class="d-inline-flex gap-1">
                        <a href="../pages/detail-destinasi.php?id=<?php echo (int)$d['id']; ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" title="Lihat di Website" aria-label="Lihat">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="destinasi_edit.php?id=<?php echo (int)$d['id']; ?>" class="btn btn-outline-forest btn-sm rounded-pill px-2 py-1" title="Edit Destinasi" aria-label="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 btn-delete-dest" data-id="<?php echo (int)$d['id']; ?>" data-nama="<?php echo htmlspecialchars($d['nama']); ?>" title="Hapus Destinasi" aria-label="Hapus">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Konfirmasi Hapus Destinasi -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <form method="POST" action="destinasi.php">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input type="hidden" name="id" id="deleteDestId" value="">

          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold text-danger" id="deleteModalTitle">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <p class="text-muted mb-3">
              Apakah Anda yakin ingin menghapus destinasi <strong id="deleteDestName" class="text-dark"></strong> dari database?
            </p>
            <div class="p-3 bg-light rounded-3 border small text-muted">
              <i class="bi bi-info-circle text-primary me-1"></i>
              <strong>Perhatian:</strong> Tindakan ini permanen. ID destinasi lain tidak akan terpengaruh. Jika destinasi ini tersimpan di favorit localStorage pengguna, kartu referensinya mungkin tidak lagi dapat membuka halaman detail.
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4">
              <i class="bi bi-trash me-1"></i>Ya, Hapus Sekarang
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    // Modal Hapus Handler
    const deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
      const deleteModal = new bootstrap.Modal(deleteModalEl);
      const deleteDestIdInput = document.getElementById('deleteDestId');
      const deleteDestNameSpan = document.getElementById('deleteDestName');

      document.querySelectorAll('.btn-delete-dest').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const nama = btn.getAttribute('data-nama');
          deleteDestIdInput.value = id;
          deleteDestNameSpan.textContent = nama;
          deleteModal.show();
        });
      });
    }

    // Live Search Filter
    const destSearchInput = document.getElementById('destSearchInput');
    const destRows = document.querySelectorAll('.dest-row');
    if (destSearchInput && destRows.length > 0) {
      destSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        destRows.forEach(row => {
          const name = row.getAttribute('data-name') || '';
          const loc = row.getAttribute('data-location') || '';
          const cat = row.getAttribute('data-category') || '';
          if (name.includes(query) || loc.includes(query) || cat.includes(query)) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });
        const badge = document.getElementById('destCountBadge');
        if (badge) badge.textContent = visibleCount;
      });
    }
  </script>
</body>
</html>
