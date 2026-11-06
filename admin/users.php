<?php
/**
 * JelajahNusa - Manajemen Pengguna (User Management)
 * Lokasi: admin/users.php
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$currentAdminId = (int)($_SESSION['user']['id'] ?? 0);

// Handle Operasi POST (Ubah Role & Hapus Pengguna)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Token keamanan CSRF tidak valid. Silakan coba kembali.';
        header('Location: users.php');
        exit;
    }

    $action   = $_POST['action'] ?? '';
    $targetId = (int)($_POST['id'] ?? 0);

    if ($targetId <= 0) {
        $_SESSION['flash_error'] = 'ID pengguna tidak valid.';
        header('Location: users.php');
        exit;
    }

    // ATURAN KEAMANAN WAJIB: Admin dilarang mengubah role diri sendiri atau menghapus akun diri sendiri
    if ($targetId === $currentAdminId) {
        $_SESSION['flash_error'] = 'Tindakan ditolak demi keamanan: Anda tidak dapat menurunkan peran atau menghapus akun administrator Anda sendiri saat sedang aktif.';
        header('Location: users.php');
        exit;
    }

    try {
        // Ambil data user target
        $stmtTarget = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmtTarget->execute([$targetId]);
        $targetUser = $stmtTarget->fetch(PDO::FETCH_ASSOC);

        if (!$targetUser) {
            $_SESSION['flash_error'] = 'Pengguna dengan ID tersebut tidak ditemukan.';
            header('Location: users.php');
            exit;
        }

        if ($action === 'toggle_role') {
            $newRole = ($targetUser['role'] === 'admin') ? 'user' : 'admin';
            $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $updateStmt->execute([$newRole, $targetId]);

            $roleLabel = ($newRole === 'admin') ? 'Administrator' : 'User';
            $_SESSION['flash_success'] = 'Peran akun "' . htmlspecialchars($targetUser['name']) . '" (' . htmlspecialchars($targetUser['email']) . ') berhasil diubah menjadi ' . $roleLabel . '.';

        } elseif ($action === 'delete_user') {
            $delStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delStmt->execute([$targetId]);

            $_SESSION['flash_success'] = 'Akun pengguna "' . htmlspecialchars($targetUser['name']) . '" (' . htmlspecialchars($targetUser['email']) . ') berhasil dihapus dari sistem.';
        } else {
            $_SESSION['flash_error'] = 'Aksi tidak dikenal.';
        }

    } catch (Throwable $e) {
        error_log('[Admin Users Action Error] ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
    }

    header('Location: users.php');
    exit;
}

// Ambil daftar pengguna (Tegas: Kolom password TIDAK diambil demi keamanan privasi)
$users = [];
try {
    $stmt = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY id ASC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('[Admin Users Query Error] ' . $e->getMessage());
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Pengguna | JelajahNusa Admin</title>
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
        <a href="destinasi.php" class="admin-menu-link">
          <i class="bi bi-map"></i>
          <span>Destinasi Wisata</span>
        </a>
      </li>
      <li>
        <a href="users.php" class="admin-menu-link active">
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
        <h5 class="fw-bold text-dark mb-0">Manajemen Pengguna</h5>
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

      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <h4 class="fw-bold text-dark mb-1">Daftar Akun Pengguna</h4>
          <p class="text-muted small mb-0">Kelola hak akses dan peran pengguna pada platform JelajahNusa.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
            Total: <span id="userCountBadge"><?php echo count($users); ?></span> Pengguna
          </span>
        </div>
      </div>

      <!-- Preview Pengguna Terdaftar -->
      <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <h6 class="fw-bold text-dark mb-0">Akun Terdaftar di Database</h6>
            <span class="badge bg-success bg-opacity-10 text-success small"><i class="bi bi-shield-check me-1"></i>Proteksi Password Hash</span>
          </div>
          <div class="input-group input-group-sm" style="max-width: 280px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="userSearchInput" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama, email, role...">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="small text-muted">
                <th style="width: 70px;">ID</th>
                <th>Nama Lengkap</th>
                <th>Alamat Email</th>
                <th>Role / Peran</th>
                <th>Tanggal Terdaftar</th>
                <th class="text-end" style="width: 170px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted small">Belum ada data pengguna.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $u): ?>
                  <?php $isSelf = ((int)$u['id'] === $currentAdminId); ?>
                  <tr class="user-row" data-name="<?php echo htmlspecialchars(strtolower($u['name'])); ?>" data-email="<?php echo htmlspecialchars(strtolower($u['email'])); ?>" data-role="<?php echo htmlspecialchars(strtolower($u['role'] ?? 'user')); ?>">
                    <td class="fw-bold text-muted small">#<?php echo (int)$u['id']; ?></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-5 <?php echo (($u['role'] ?? '') === 'admin') ? 'text-danger' : 'text-success'; ?>"></i>
                        <div>
                          <span class="fw-semibold text-dark small d-block"><?php echo htmlspecialchars($u['name']); ?></span>
                          <?php if ($isSelf): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary small" style="font-size: 0.7rem;"><i class="bi bi-person-check me-1"></i>Akun Anda Saat Ini</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td class="small text-muted"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                      <?php if (($u['role'] ?? '') === 'admin'): ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 small fw-semibold">
                          <i class="bi bi-shield-lock me-1"></i>Administrator
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1 small fw-semibold">
                          <i class="bi bi-person me-1"></i>User
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="small text-muted">
                      <?php echo !empty($u['created_at']) ? htmlspecialchars(date('d M Y, H:i', strtotime($u['created_at']))) : '-'; ?>
                    </td>
                    <td class="text-end">
                      <?php if ($isSelf): ?>
                        <span class="text-muted small fst-italic" style="font-size: 0.78rem;">Terkunci (Sesi Aktif)</span>
                      <?php else: ?>
                        <div class="d-inline-flex gap-1">
                          <!-- Tombol Ubah Role -->
                          <button type="button" class="btn btn-outline-forest btn-sm rounded-pill px-2 py-1 btn-role-user" 
                                  data-id="<?php echo (int)$u['id']; ?>" 
                                  data-name="<?php echo htmlspecialchars($u['name']); ?>" 
                                  data-role="<?php echo htmlspecialchars($u['role'] ?? 'user'); ?>"
                                  title="Ubah Role">
                            <i class="bi bi-arrow-repeat me-1"></i><?php echo (($u['role'] ?? '') === 'admin') ? 'Ubah ke User' : 'Jadikan Admin'; ?>
                          </button>
                          <!-- Tombol Hapus Akun -->
                          <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 btn-delete-user" 
                                  data-id="<?php echo (int)$u['id']; ?>" 
                                  data-name="<?php echo htmlspecialchars($u['name']); ?>"
                                  title="Hapus Akun">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      <?php endif; ?>
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

  <!-- Modal Konfirmasi Ubah Role Pengguna -->
  <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <form method="POST" action="users.php">
          <input type="hidden" name="action" value="toggle_role">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input type="hidden" name="id" id="roleUserId" value="">

          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold text-dark" id="roleModalTitle">
              <i class="bi bi-shield-exclamation text-warning me-2"></i>Konfirmasi Perubahan Peran (Role)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <p class="text-muted mb-3">
              Apakah Anda yakin ingin mengubah peran pengguna <strong id="roleUserName" class="text-dark"></strong> menjadi <strong id="roleTargetName" class="text-success"></strong>?
            </p>
            <div class="p-3 bg-light rounded-3 border small text-muted">
              <i class="bi bi-info-circle text-primary me-1"></i>
              <strong>Catatan:</strong> Jika dijadikan Administrator, pengguna ini akan mendapatkan akses penuh ke seluruh menu Admin Dashboard.
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-forest rounded-pill px-4">
              <i class="bi bi-check2-circle me-1"></i>Ya, Simpan Perubahan Peran
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Hapus Pengguna -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <form method="POST" action="users.php">
          <input type="hidden" name="action" value="delete_user">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input type="hidden" name="id" id="deleteUserId" value="">

          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold text-danger" id="deleteUserModalTitle">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Pengguna
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <p class="text-muted mb-3">
              Apakah Anda yakin ingin menghapus akun pengguna <strong id="deleteUserName" class="text-dark"></strong>?
            </p>
            <div class="p-3 bg-light rounded-3 border small text-muted">
              <i class="bi bi-exclamation-circle text-danger me-1"></i>
              <strong>Peringatan:</strong> Pengguna ini tidak akan dapat login kembali ke JelajahNusa. Tindakan ini bersifat permanen.
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4">
              <i class="bi bi-trash me-1"></i>Ya, Hapus Akun
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

    // Modal Ubah Role
    const roleModalEl = document.getElementById('roleModal');
    if (roleModalEl) {
      const roleModal = new bootstrap.Modal(roleModalEl);
      const roleUserId = document.getElementById('roleUserId');
      const roleUserName = document.getElementById('roleUserName');
      const roleTargetName = document.getElementById('roleTargetName');

      document.querySelectorAll('.btn-role-user').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name');
          const role = btn.getAttribute('data-role');
          const target = (role === 'admin') ? 'User (Pengguna Biasa)' : 'Administrator';

          roleUserId.value = id;
          roleUserName.textContent = name;
          roleTargetName.textContent = target;
          roleModal.show();
        });
      });
    }

    // Modal Hapus Pengguna
    const deleteUserModalEl = document.getElementById('deleteUserModal');
    if (deleteUserModalEl) {
      const deleteUserModal = new bootstrap.Modal(deleteUserModalEl);
      const deleteUserId = document.getElementById('deleteUserId');
      const deleteUserName = document.getElementById('deleteUserName');

      document.querySelectorAll('.btn-delete-user').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name');

          deleteUserId.value = id;
          deleteUserName.textContent = name;
          deleteUserModal.show();
        });
      });
    }

    // Live Search Filter User
    const userSearchInput = document.getElementById('userSearchInput');
    const userRows = document.querySelectorAll('.user-row');
    if (userSearchInput && userRows.length > 0) {
      userSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        userRows.forEach(row => {
          const name = row.getAttribute('data-name') || '';
          const email = row.getAttribute('data-email') || '';
          const role = row.getAttribute('data-role') || '';
          if (name.includes(query) || email.includes(query) || role.includes(query)) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });
        const badge = document.getElementById('userCountBadge');
        if (badge) badge.textContent = visibleCount;
      });
    }
  </script>
</body>
</html>
