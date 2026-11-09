<?php
/**
 * JelajahNusa - Halaman Pengaturan Akun (Edit Profil & Ubah Password)
 * Lokasi: pages/profil.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi: hanya user yang sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php?auth=login&redirect=pages/profil.php');
    exit;
}

$auth_base_url = '../auth/';
$currentUser   = $_SESSION['user'];

// Generate CSRF token baru setiap kali halaman dimuat
if (empty($_SESSION['csrf_profile'])) {
    $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
} else {
    // Regenerate untuk keamanan setiap kunjungan
    $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_profile'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="content-language" content="id">
  <title>Pengaturan Akun — JelajahNusa</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <meta name="jn-auth-status" content="true">
  <meta name="description" content="Kelola pengaturan akun JelajahNusa Anda — perbarui nama dan ubah kata sandi.">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    /* ── Profil page — scoped styles ─────────────────────────────────────── */
    .profil-hero {
      background: linear-gradient(135deg, var(--c-dark-forest) 0%, var(--c-forest) 100%);
      padding: 120px 0 60px;
    }
    .profil-avatar-ring {
      width: 88px;
      height: 88px;
      border-radius: 50%;
      background: rgba(255,255,255,0.12);
      border: 3px solid rgba(255,255,255,0.30);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.4rem;
      color: #fff;
      flex-shrink: 0;
    }
    .profil-card {
      background: #fff;
      border-radius: var(--radius-lg);
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      border: 1px solid rgba(0,0,0,0.06);
      overflow: hidden;
    }
    .profil-card-header {
      background: var(--c-offwhite);
      border-bottom: 1px solid var(--c-beige);
      padding: 18px 24px;
    }
    .profil-card-header h5 {
      font-family: var(--font-heading);
      font-weight: 700;
      color: var(--c-dark-forest);
      margin: 0;
    }
    .profil-card-body {
      padding: 28px 24px;
    }
    .profil-field-label {
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--c-muted);
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 5px;
    }
    .profil-readonly-val {
      font-size: 0.97rem;
      color: var(--c-dark-forest);
      font-weight: 500;
      padding: 10px 14px;
      background: var(--c-offwhite);
      border: 1px solid var(--c-beige);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .profil-section-divider {
      border-top: 1px solid var(--c-beige);
      margin: 24px 0;
    }
    .profil-alert {
      font-size: 0.875rem;
      border-radius: var(--radius-sm);
      padding: 10px 14px;
    }
    .pass-strength-bar {
      height: 4px;
      border-radius: 2px;
      background: var(--c-beige);
      overflow: hidden;
      margin-top: 6px;
    }
    .pass-strength-fill {
      height: 100%;
      border-radius: 2px;
      transition: width 0.3s ease, background-color 0.3s ease;
      width: 0%;
    }
    .pass-strength-label {
      font-size: 0.78rem;
      margin-top: 3px;
    }
    @media (max-width: 767px) {
      .profil-card-body { padding: 20px 16px; }
      .profil-hero { padding: 110px 0 48px; }
    }
  </style>
</head>
<body style="background-color: var(--c-offwhite);">

  <!-- ======================================================================
       NAVBAR
       ====================================================================== -->
  <nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">

      <a class="navbar-brand" href="../index.php" aria-label="JelajahNusa — Kembali ke Beranda">
        <img class="brand-logo-img"
             src="../assets/images/logo-horizontal.svg"
             alt="JelajahNusa — Jelajahi Indonesia dengan Caramu"
             width="240" height="56">
      </a>

      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarContent" aria-controls="navbarContent"
              aria-expanded="false" aria-label="Buka atau tutup navigasi">
        <i class="bi bi-list"></i>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../index.php#beranda">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="../index.php#destinasi">Jelajahi Wisata</a></li>
          <li class="nav-item"><a class="nav-link" href="../index.php#kategori">Kategori</a></li>
          <li class="nav-item"><a class="nav-link" href="../index.php#rekomendasi">Rekomendasi</a></li>
          <li class="nav-item"><a class="nav-link" href="rencana-perjalanan.php">Rencana Perjalanan</a></li>
          <li class="nav-item"><a class="nav-link" href="../index.php#tentang">Tentang Kami</a></li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          <!-- Favorit -->
          <a href="favorit.php" class="nav-fav-pill" id="navFavBtn" title="Destinasi Favorit">
            <i class="bi bi-heart-fill text-danger"></i>
            <span class="d-none d-xl-inline">Favorit</span>
            <span class="nav-fav-badge" id="navFavCount">0</span>
          </a>

          <!-- Dropdown profil -->
          <div class="dropdown">
            <button class="btn btn-outline-forest btn-sm rounded-pill dropdown-toggle d-flex align-items-center gap-2 px-3 py-1"
                    type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle text-success"></i>
              <span class="fw-bold" id="navUserName"><?php echo htmlspecialchars($currentUser['name']); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuBtn"
                style="border-radius: var(--radius-md);">
              <li><span class="dropdown-item-text text-muted small"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($currentUser['email']); ?></span></li>
              <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-success fw-bold small" href="../admin/"><i class="bi bi-speedometer2 me-2"></i>Dasbor Administrator</a></li>
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
        </div>
      </div>
    </div>
  </nav>

  <!-- ======================================================================
       HERO SECTION
       ====================================================================== -->
  <header class="profil-hero">
    <div class="container">
      <div class="d-flex align-items-center gap-4 flex-wrap">
        <div class="profil-avatar-ring" aria-hidden="true">
          <i class="bi bi-person-fill"></i>
        </div>
        <div>
          <p class="text-white-50 small mb-1 text-uppercase" style="letter-spacing:0.06em;">
            Akun Pengguna
          </p>
          <h1 class="text-white fw-bold mb-1" style="font-size:clamp(1.5rem,4vw,2.2rem);" id="heroUserName">
            <?php echo htmlspecialchars($currentUser['name']); ?>
          </h1>
          <p class="text-white-50 mb-0" style="font-size:0.95rem;">
            <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($currentUser['email']); ?>
            &nbsp;·&nbsp;
            <span class="badge <?php echo $currentUser['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-success bg-opacity-25 text-success'; ?> rounded-pill px-2 py-1">
              <?php echo ($currentUser['role'] ?? 'user') === 'admin' ? 'Administrator' : 'Pengguna'; ?>
            </span>
          </p>
        </div>
      </div>
    </div>
  </header>

  <!-- ======================================================================
       MAIN CONTENT — dua kolom (Edit Profil + Ubah Password)
       ====================================================================== -->
  <main class="container py-5">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none" style="color:var(--c-forest);">Beranda</a></li>
        <li class="breadcrumb-item active text-muted" aria-current="page">Pengaturan Akun</li>
      </ol>
    </nav>

    <div class="row g-4">

      <!-- ── Kolom Kiri: Edit Profil ─────────────────────────────────────── -->
      <div class="col-lg-6">
        <div class="profil-card h-100">

          <div class="profil-card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-fill" style="color:var(--c-forest);font-size:1.1rem;"></i>
            <h5>Ubah Profil</h5>
          </div>

          <div class="profil-card-body">

            <!-- Alert feedback -->
            <div id="profileAlert" class="alert profil-alert d-none" role="alert"></div>

            <!-- Info read-only: Email -->
            <div class="mb-4">
              <p class="profil-field-label">Alamat Email</p>
              <div class="profil-readonly-val">
                <i class="bi bi-envelope text-muted"></i>
                <span><?php echo htmlspecialchars($currentUser['email']); ?></span>
                <span class="ms-auto badge bg-secondary bg-opacity-10 text-muted small">Tidak dapat diubah</span>
              </div>
              <p class="text-muted mt-1" style="font-size:0.78rem;">
                Email digunakan untuk login dan tidak dapat diubah melalui halaman ini.
              </p>
            </div>

            <!-- Info read-only: Tanggal Bergabung -->
            <div class="mb-4">
              <p class="profil-field-label">Anggota Sejak</p>
              <div class="profil-readonly-val">
                <i class="bi bi-calendar-check text-muted"></i>
                <?php
                  require_once __DIR__ . '/../config/database.php';
                  require_once __DIR__ . '/../config/helpers.php';
                  try {
                    $stmt = $pdo->prepare('SELECT created_at FROM users WHERE id = :uid LIMIT 1');
                    $stmt->execute(['uid' => (int)$currentUser['id']]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $joinDate = $row ? formatTanggalIndonesia($row['created_at']) : '—';
                  } catch (Exception $e) { $joinDate = '—'; }
                ?>
                <span><?php echo htmlspecialchars($joinDate); ?></span>
              </div>
            </div>

            <div class="profil-section-divider"></div>

            <!-- Form: Edit Nama -->
            <form id="editNameForm" autocomplete="off" novalidate>
              <input type="hidden" id="csrfTokenName" value="<?php echo htmlspecialchars($csrfToken); ?>">

              <div class="mb-4">
                <label for="inputName" class="form-label profil-field-label">
                  Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="inputName"
                       name="name"
                       value="<?php echo htmlspecialchars($currentUser['name']); ?>"
                       placeholder="Nama lengkap Anda"
                       maxlength="100"
                       required
                       autocomplete="name">
                <div class="d-flex justify-content-between mt-1">
                  <div class="text-muted" style="font-size:0.78rem;">Minimal 2 karakter, maksimal 100 karakter.</div>
                  <div class="text-muted" style="font-size:0.78rem;" id="nameCharCount">
                    <?php echo mb_strlen($currentUser['name']); ?>/100
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-forest rounded-pill px-4 py-2 fw-bold" id="saveNameBtn">
                <i class="bi bi-check2 me-1"></i> Simpan Perubahan
              </button>
            </form>

          </div><!-- /.profil-card-body -->
        </div><!-- /.profil-card -->
      </div><!-- /.col -->

      <!-- ── Kolom Kanan: Ubah Password ─────────────────────────────────── -->
      <div class="col-lg-6">
        <div class="profil-card h-100">

          <div class="profil-card-header d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill" style="color:var(--c-forest);font-size:1.1rem;"></i>
            <h5>Ubah Kata Sandi</h5>
          </div>

          <div class="profil-card-body">

            <!-- Alert feedback -->
            <div id="passwordAlert" class="alert profil-alert d-none" role="alert"></div>

            <form id="changePasswordForm" autocomplete="off" novalidate>
              <input type="hidden" id="csrfTokenPass" value="<?php echo htmlspecialchars($csrfToken); ?>">

              <!-- Kata sandi lama -->
              <div class="mb-3">
                <label for="oldPassword" class="form-label profil-field-label">
                  Kata Sandi Lama <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="password" class="form-control" id="oldPassword" name="old_password"
                         placeholder="Masukkan kata sandi saat ini" required
                         autocomplete="current-password">
                  <button class="btn btn-outline-secondary border-start-0" type="button"
                          data-toggle-pass="oldPassword" title="Tampilkan atau sembunyikan">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Kata sandi baru -->
              <div class="mb-3">
                <label for="newPassword" class="form-label profil-field-label">
                  Kata Sandi Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="password" class="form-control" id="newPassword" name="new_password"
                         placeholder="Minimal 8 karakter" required
                         autocomplete="new-password" minlength="8">
                  <button class="btn btn-outline-secondary border-start-0" type="button"
                          data-toggle-pass="newPassword" title="Tampilkan atau sembunyikan">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <!-- Indikator kekuatan password -->
                <div class="pass-strength-bar">
                  <div class="pass-strength-fill" id="passStrengthFill"></div>
                </div>
                <div class="pass-strength-label text-muted" id="passStrengthLabel"></div>
              </div>

              <!-- Konfirmasi kata sandi baru -->
              <div class="mb-4">
                <label for="confirmPassword" class="form-label profil-field-label">
                  Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                         placeholder="Ulangi kata sandi baru" required
                         autocomplete="new-password">
                  <button class="btn btn-outline-secondary border-start-0" type="button"
                          data-toggle-pass="confirmPassword" title="Tampilkan atau sembunyikan">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="text-muted mt-1" style="font-size:0.78rem;" id="confirmMatchNote"></div>
              </div>

              <button type="submit" class="btn btn-forest rounded-pill px-4 py-2 fw-bold" id="savePassBtn">
                <i class="bi bi-lock me-1"></i> Ubah Kata Sandi
              </button>
            </form>

          </div><!-- /.profil-card-body -->
        </div><!-- /.profil-card -->
      </div><!-- /.col -->

    </div><!-- /.row -->
  </main>

  <!-- ======================================================================
       FOOTER SEDERHANA
       ====================================================================== -->
  <footer class="custom-footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <a href="../index.php" class="footer-brand-title" aria-label="JelajahNusa">
            <img class="footer-brand-logo" src="../assets/images/logo-horizontal.svg"
                 alt="JelajahNusa" width="200" height="52">
          </a>
          <p class="footer-desc">
            Platform digital travel guide & travel planner nusantara.
          </p>
        </div>
        <div class="col-lg-4 col-md-6">
          <h4 class="footer-col-title">Navigasi</h4>
          <ul class="footer-link-list">
            <li><a href="../index.php#beranda">Beranda</a></li>
            <li><a href="../index.php#destinasi">Jelajahi Wisata</a></li>
            <li><a href="favorit.php">Favorit</a></li>
            <li><a href="rencana-perjalanan.php">Rencana Perjalanan</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-12">
          <h4 class="footer-col-title">Akun Saya</h4>
          <ul class="footer-link-list">
            <li><a href="profil.php" style="color:var(--c-sage);">Pengaturan Akun</a></li>
            <li><a href="favorit.php">Destinasi Favorit</a></li>
            <li><a href="../auth/logout.php">Keluar</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <div>&copy; <?php echo date('Y'); ?> <strong translate="no" class="notranslate">JelajahNusa</strong>. Seluruh Hak Cipta Dilindungi.</div>
        <div class="d-flex gap-3">
          <a href="#" class="text-decoration-none text-white-50 small">Privasi</a>
          <span class="text-white-50">•</span>
          <a href="#" class="text-decoration-none text-white-50 small">Syarat & Ketentuan</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"></script>

  <!-- script.js (scroll, navbar, favorit badge, dll.) -->
  <script src="../assets/js/script.js"></script>

  <!-- ── Script khusus halaman profil ──────────────────────────────────────── -->
  <script>
  (function () {
    'use strict';

    const API_URL = '../auth/profile.php';

    // ── Helpers ────────────────────────────────────────────────────────────
    function showAlert(elId, message, isSuccess) {
      const el = document.getElementById(elId);
      if (!el) return;
      el.textContent = message;
      el.className = `alert profil-alert ${isSuccess ? 'alert-success' : 'alert-danger'}`;
      el.classList.remove('d-none');
      el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function hideAlert(elId) {
      const el = document.getElementById(elId);
      if (el) { el.textContent = ''; el.classList.add('d-none'); }
    }

    async function apiPost(body) {
      const fd = new FormData();
      Object.entries(body).forEach(([k, v]) => fd.append(k, v ?? ''));
      const res = await fetch(API_URL, {
        method: 'POST', body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      return res.json().catch(() => null);
    }

    // Ambil CSRF token segar dari server (dipanggil setelah setiap submit)
    async function refreshCsrfTokens() {
      try {
        const res = await fetch(API_URL + '?action=get_csrf', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json().catch(() => null);
        if (data && data.csrf_token) {
          document.getElementById('csrfTokenName').value  = data.csrf_token;
          document.getElementById('csrfTokenPass').value  = data.csrf_token;
        }
      } catch (_) {}
    }

    // ── Toggle tampilkan/sembunyikan password ──────────────────────────────
    document.querySelectorAll('[data-toggle-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const inputId = btn.getAttribute('data-toggle-pass');
        const input   = document.getElementById(inputId);
        const icon    = btn.querySelector('i');
        if (!input) return;
        if (input.type === 'password') {
          input.type = 'text';
          icon.className = 'bi bi-eye-slash';
          btn.setAttribute('title', 'Sembunyikan');
        } else {
          input.type = 'password';
          icon.className = 'bi bi-eye';
          btn.setAttribute('title', 'Tampilkan');
        }
      });
    });

    // ── Counter karakter nama ──────────────────────────────────────────────
    const inputName    = document.getElementById('inputName');
    const nameCounter  = document.getElementById('nameCharCount');
    if (inputName && nameCounter) {
      inputName.addEventListener('input', () => {
        const len = inputName.value.length;
        nameCounter.textContent = len + '/100';
        nameCounter.style.color = len > 90 ? 'var(--c-danger, #dc3545)' : '';
      });
    }

    // ── Indikator kekuatan password ────────────────────────────────────────
    const newPassInput   = document.getElementById('newPassword');
    const strengthFill   = document.getElementById('passStrengthFill');
    const strengthLabel  = document.getElementById('passStrengthLabel');

    function calcPasswordStrength(pass) {
      if (!pass) return { score: 0, label: '', color: '' };
      let score = 0;
      if (pass.length >= 8)  score++;
      if (pass.length >= 12) score++;
      if (/[A-Z]/.test(pass)) score++;
      if (/[0-9]/.test(pass)) score++;
      if (/[^A-Za-z0-9]/.test(pass)) score++;

      if (score <= 1) return { score: 20, label: 'Sangat Lemah', color: '#dc3545' };
      if (score === 2) return { score: 40, label: 'Lemah', color: '#fd7e14' };
      if (score === 3) return { score: 60, label: 'Cukup', color: '#ffc107' };
      if (score === 4) return { score: 80, label: 'Kuat', color: '#198754' };
      return { score: 100, label: 'Sangat Kuat', color: '#0d6efd' };
    }

    if (newPassInput && strengthFill && strengthLabel) {
      newPassInput.addEventListener('input', () => {
        const { score, label, color } = calcPasswordStrength(newPassInput.value);
        strengthFill.style.width           = score + '%';
        strengthFill.style.backgroundColor = color;
        strengthLabel.textContent          = newPassInput.value ? label : '';
        strengthLabel.style.color          = color;
        checkConfirmMatch();
      });
    }

    // ── Cek kecocokan konfirmasi password secara live ──────────────────────
    const confirmInput   = document.getElementById('confirmPassword');
    const confirmNote    = document.getElementById('confirmMatchNote');

    function checkConfirmMatch() {
      if (!confirmInput || !newPassInput || !confirmNote) return;
      if (!confirmInput.value) { confirmNote.textContent = ''; return; }
      if (confirmInput.value === newPassInput.value) {
        confirmNote.textContent = '✓ Kata sandi cocok';
        confirmNote.style.color = '#198754';
      } else {
        confirmNote.textContent = '✗ Kata sandi tidak cocok';
        confirmNote.style.color = '#dc3545';
      }
    }

    if (confirmInput) confirmInput.addEventListener('input', checkConfirmMatch);

    // ── Form: Edit Nama ────────────────────────────────────────────────────
    const editNameForm = document.getElementById('editNameForm');
    if (editNameForm) {
      editNameForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert('profileAlert');

        const name     = document.getElementById('inputName')?.value.trim() || '';
        const csrf     = document.getElementById('csrfTokenName')?.value    || '';
        const submitBtn = document.getElementById('saveNameBtn');

        // Validasi frontend sebelum kirim
        if (name.length < 2) {
          showAlert('profileAlert', 'Nama minimal 2 karakter.', false); return;
        }
        if (name.length > 100) {
          showAlert('profileAlert', 'Nama maksimal 100 karakter.', false); return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        }

        const data = await apiPost({ action: 'update_name', name, csrf_token: csrf });

        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-check2 me-1"></i> Simpan Perubahan';
        }

        if (data && data.success) {
          showAlert('profileAlert', data.message, true);

          // Perbarui semua tampilan nama di halaman ini
          const newName = data.new_name;
          const navSpan    = document.getElementById('navUserName');
          const heroTitle  = document.getElementById('heroUserName');
          if (navSpan)   navSpan.textContent   = newName;
          if (heroTitle) heroTitle.textContent = newName;

          // Perbarui juga button dropdown utama
          const menuSpan = document.querySelector('#userMenuBtn span.fw-bold');
          if (menuSpan) menuSpan.textContent = newName;

          // Refresh token CSRF untuk request berikutnya
          await refreshCsrfTokens();
        } else {
          showAlert('profileAlert', (data && data.message) || 'Gagal menyimpan perubahan.', false);
          // Jika token expired, ambil token baru secara diam-diam
          if (data && data.message && data.message.includes('Token')) {
            await refreshCsrfTokens();
          }
        }
      });
    }

    // ── Form: Ubah Password ────────────────────────────────────────────────
    const changePassForm = document.getElementById('changePasswordForm');
    if (changePassForm) {
      changePassForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert('passwordAlert');

        const oldPass = document.getElementById('oldPassword')?.value     || '';
        const newPass = document.getElementById('newPassword')?.value     || '';
        const confPass = document.getElementById('confirmPassword')?.value || '';
        const csrf    = document.getElementById('csrfTokenPass')?.value   || '';
        const submitBtn = document.getElementById('savePassBtn');

        // Validasi frontend
        if (!oldPass || !newPass || !confPass) {
          showAlert('passwordAlert', 'Semua field kata sandi wajib diisi.', false); return;
        }
        if (newPass.length < 8) {
          showAlert('passwordAlert', 'Kata sandi baru minimal 8 karakter.', false); return;
        }
        if (newPass !== confPass) {
          showAlert('passwordAlert', 'Konfirmasi kata sandi tidak cocok.', false); return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        }

        const data = await apiPost({
          action:           'change_password',
          old_password:     oldPass,
          new_password:     newPass,
          confirm_password: confPass,
          csrf_token:       csrf,
        });

        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="bi bi-lock me-1"></i> Ubah Kata Sandi';
        }

        if (data && data.success) {
          showAlert('passwordAlert', data.message, true);
          changePassForm.reset();
          // Reset indikator
          if (strengthFill)  { strengthFill.style.width = '0%'; }
          if (strengthLabel) { strengthLabel.textContent = ''; }
          if (confirmNote)   { confirmNote.textContent = ''; }
          await refreshCsrfTokens();
        } else {
          showAlert('passwordAlert', (data && data.message) || 'Gagal mengubah kata sandi.', false);
          if (data && data.message && data.message.includes('Token')) {
            await refreshCsrfTokens();
          }
        }
      });
    }

  })();
  </script>
</body>
</html>
