<?php
/**
 * JelajahNusa - Middleware Keamanan & Autentikasi Admin
 * Lokasi: admin/auth_check.php
 * Konsep: Proteksi Server-Side Role-Based Access Control (RBAC) & CSRF Defense
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verifikasi Status Login: Tamu (Guest) dilarang mengakses
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php?auth=login&redirect=admin/');
    exit;
}

// 2. Verifikasi Hak Akses Role: Hanya role 'admin' yang diizinkan
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="content-language" content="id">
      <title>403 - Akses Terbatas | Administrator JelajahNusa</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      <style>
        :root {
          --c-forest: #345B49;
          --c-sage: #A8B99A;
          --c-beige: #E8E6D9;
          --c-offwhite: #F7F7F2;
          --c-dark-forest: #29352D;
        }
        body {
          font-family: 'Plus Jakarta Sans', sans-serif;
          background-color: var(--c-offwhite);
          color: var(--c-dark-forest);
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0;
          padding: 20px;
        }
        .card-forbidden {
          background: #ffffff;
          border-radius: 20px;
          border: 1px solid rgba(52, 91, 73, 0.12);
          box-shadow: 0 12px 36px rgba(41, 53, 45, 0.08);
          max-width: 500px;
          padding: 42px 32px;
          text-align: center;
        }
        .icon-circle {
          width: 72px;
          height: 72px;
          border-radius: 50%;
          background: rgba(220, 53, 69, 0.1);
          color: #dc3545;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 2rem;
          margin: 0 auto 18px;
        }
        .btn-forest {
          background-color: var(--c-forest);
          color: #ffffff;
          border: none;
          transition: all 0.25s ease;
        }
        .btn-forest:hover {
          background-color: var(--c-dark-forest);
          color: #ffffff;
          transform: translateY(-2px);
        }
      </style>
    </head>
    <body>
      <div class="card-forbidden">
        <div class="icon-circle">
          <i class="bi bi-shield-slash"></i>
        </div>
        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold px-3 py-1 rounded-pill mb-2">403 — Akses Terlarang</span>
        <h3 class="fw-bold mb-2">Akses Ditolak</h3>
        <p class="text-muted small mb-4">
          Akun Anda saat ini terdaftar sebagai <strong>Pengguna</strong>. Area dasbor administrator hanya dapat diakses oleh akun dengan hak istimewa <strong>Administrator</strong>.
        </p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <a href="../index.php" class="btn btn-forest rounded-pill px-4 py-2 small fw-semibold text-decoration-none">
            <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
          </a>
          <a href="../auth/logout.php" class="btn btn-outline-danger rounded-pill px-3 py-2 small fw-semibold text-decoration-none">
            <i class="bi bi-box-arrow-right me-1"></i> Keluar Sesi
          </a>
        </div>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// 3. Helper Pengamanan CSRF Token
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken(?string $token): bool {
        return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Data pengguna admin aktif
$admin_user = $_SESSION['user'];
