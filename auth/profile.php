<?php
/**
 * JelajahNusa - API Endpoint: Kelola Profil & Password Pengguna
 * Lokasi: auth/profile.php
 *
 * Semua operasi mensyaratkan:
 *  1. Session login yang valid ($_SESSION['user']['id'])
 *  2. CSRF token yang cocok (dikirim sebagai POST field 'csrf_token')
 *
 * Routes (POST):
 *   action=update_name      → perbarui nama pengguna
 *   action=change_password  → ubah password (verifikasi lama, simpan baru)
 *   action=get_csrf         → ambil CSRF token baru (GET, tanpa token)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../config/database.php';

// ─── Helpers ─────────────────────────────────────────────────────────────────

function jsonOut(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Pastikan user sudah login; kembalikan user_id dari session. */
function requireAuth(): int
{
    if (!isset($_SESSION['user']['id']) || !is_int($_SESSION['user']['id'])) {
        jsonOut(['success' => false, 'auth' => false,
                 'message' => 'Silakan masuk terlebih dahulu.'], 401);
    }
    return (int) $_SESSION['user']['id'];
}

/**
 * Buat atau kembalikan CSRF token yang disimpan di session.
 * Token di-regenerate setiap kali halaman profil dimuat.
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_profile'])) {
        $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_profile'];
}

/** Verifikasi CSRF token dan tolak jika tidak cocok. */
function verifyCsrf(): void
{
    $submitted = trim($_POST['csrf_token'] ?? '');
    $stored    = $_SESSION['csrf_profile'] ?? '';

    if ($submitted === '' || $stored === '' || !hash_equals($stored, $submitted)) {
        jsonOut(['success' => false,
                 'message' => 'Token keamanan tidak valid atau sudah kedaluwarsa. Muat ulang halaman dan coba lagi.'], 403);
    }

    // Rotate token setelah dipakai (anti-replay)
    $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
}

// ─── Router ──────────────────────────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'];

// ══════════════════════════════════════════════════════════════════════════════
// GET: get_csrf — ambil token baru tanpa perlu token lama
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'GET' && ($_GET['action'] ?? '') === 'get_csrf') {
    requireAuth(); // harus login
    // Regenerate token setiap kali halaman profil dimuat
    $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
    jsonOut(['success' => true, 'csrf_token' => $_SESSION['csrf_profile']]);
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: update_name — perbarui nama pengguna
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && ($_POST['action'] ?? '') === 'update_name') {
    $uid = requireAuth();
    verifyCsrf();

    $name = trim($_POST['name'] ?? '');

    // Validasi backend
    if ($name === '') {
        jsonOut(['success' => false, 'message' => 'Nama lengkap tidak boleh kosong.'], 422);
    }
    if (mb_strlen($name) < 2) {
        jsonOut(['success' => false, 'message' => 'Nama minimal 2 karakter.'], 422);
    }
    if (mb_strlen($name) > 100) {
        jsonOut(['success' => false, 'message' => 'Nama maksimal 100 karakter.'], 422);
    }
    // Tolak nama yang hanya berisi angka atau karakter non-huruf
    if (!preg_match('/\p{L}/u', $name)) {
        jsonOut(['success' => false, 'message' => 'Nama harus mengandung setidaknya satu huruf.'], 422);
    }

    try {
        // Pastikan user ada (seharusnya selalu ada saat session aktif)
        $check = $pdo->prepare('SELECT id FROM users WHERE id = :uid LIMIT 1');
        $check->execute(['uid' => $uid]);
        if (!$check->fetch()) {
            jsonOut(['success' => false, 'message' => 'Akun tidak ditemukan.'], 404);
        }

        // Update hanya kolom name milik user yang sedang login
        $stmt = $pdo->prepare('UPDATE users SET name = :name WHERE id = :uid');
        $stmt->execute(['name' => $name, 'uid' => $uid]);

        // Perbarui session agar navbar langsung berubah tanpa logout-login
        $_SESSION['user']['name'] = $name;

        jsonOut([
            'success'  => true,
            'message'  => 'Nama berhasil diperbarui.',
            'new_name' => $name,
        ]);
    } catch (PDOException $e) {
        error_log('[Profile update_name] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal menyimpan perubahan. Coba lagi.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: change_password — ubah password
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $uid = requireAuth();
    verifyCsrf();

    $oldPass     = $_POST['old_password']     ?? '';
    $newPass     = $_POST['new_password']     ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Validasi kelengkapan
    if ($oldPass === '' || $newPass === '' || $confirmPass === '') {
        jsonOut(['success' => false, 'message' => 'Semua kolom kata sandi wajib diisi.'], 422);
    }

    // Validasi panjang password baru
    if (strlen($newPass) < 8) {
        jsonOut(['success' => false, 'message' => 'Kata sandi baru minimal 8 karakter.'], 422);
    }
    if (strlen($newPass) > 72) {
        // bcrypt truncates at 72 bytes — beri tahu user
        jsonOut(['success' => false, 'message' => 'Kata sandi baru maksimal 72 karakter.'], 422);
    }

    // Konfirmasi harus cocok
    if ($newPass !== $confirmPass) {
        jsonOut(['success' => false, 'message' => 'Konfirmasi kata sandi tidak cocok.'], 422);
    }

    try {
        // Ambil hash password saat ini dari DB — HANYA milik user yang login
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :uid LIMIT 1');
        $stmt->execute(['uid' => $uid]);
        $row = $stmt->fetch();

        if (!$row) {
            jsonOut(['success' => false, 'message' => 'Akun tidak ditemukan.'], 404);
        }

        // Verifikasi password lama
        if (!password_verify($oldPass, $row['password'])) {
            jsonOut(['success' => false, 'message' => 'Kata sandi lama tidak sesuai.'], 401);
        }

        // Tolak jika password baru sama dengan lama
        if (password_verify($newPass, $row['password'])) {
            jsonOut(['success' => false, 'message' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.'], 422);
        }

        // Hash password baru dengan bcrypt
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);

        // Simpan — WHERE id = :uid memastikan hanya akun sendiri yang berubah
        $upd = $pdo->prepare('UPDATE users SET password = :hash WHERE id = :uid');
        $upd->execute(['hash' => $newHash, 'uid' => $uid]);

        // Session tetap aktif — tidak ada data password di session yang perlu diperbarui
        jsonOut(['success' => true, 'message' => 'Kata sandi berhasil diubah.']);
    } catch (PDOException $e) {
        error_log('[Profile change_password] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal mengubah kata sandi. Coba lagi.'], 500);
    }
}

// ─── Fallback ─────────────────────────────────────────────────────────────────
jsonOut(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
