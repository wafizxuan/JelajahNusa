<?php
/**
 * JelajahNusa - API Endpoint: Kelola Favorit Destinasi
 * Lokasi: auth/favorites.php
 *
 * Endpoint ini mengelola CRUD favorit berbasis database.
 * SEMUA operasi tulis (add/remove) mensyaratkan session login yang valid.
 * User ID selalu diambil dari session server-side — tidak pernah dari input browser.
 *
 * Routes:
 *   GET    ?action=list              → ambil daftar favorit user yang login
 *   GET    ?action=ids               → ambil hanya array destination_id favorit
 *   POST   action=add    dest_id=N   → tambahkan destinasi ke favorit
 *   POST   action=remove dest_id=N   → hapus destinasi dari favorit
 *   POST   action=clear              → hapus semua favorit milik user
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
// Mencegah caching pada response API
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../config/database.php';

// ─── Helpers ─────────────────────────────────────────────────────────────────

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requireAuth(): int
{
    if (!isset($_SESSION['user']['id']) || !is_int($_SESSION['user']['id'])) {
        jsonResponse([
            'success'   => false,
            'auth'      => false,
            'message' => 'Silakan masuk terlebih dahulu untuk menggunakan fitur Favorit.',
        ], 401);
    }
    return (int) $_SESSION['user']['id'];
}

function validateDestId(mixed $raw): int
{
    $id = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        jsonResponse(['success' => false, 'message' => 'ID destinasi tidak valid.'], 422);
    }
    return (int) $id;
}

// ─── Router ──────────────────────────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'];
$action = match ($method) {
    'GET'  => trim($_GET['action']  ?? ''),
    'POST' => trim($_POST['action'] ?? ''),
    default => ''
};

// ── GET: list ──────────────────────────────────────────────────────────────
if ($method === 'GET' && $action === 'list') {
    $userId = requireAuth();

    try {
        $stmt = $pdo->prepare("
            SELECT
                d.id,
                d.card_nama   AS nama,
                d.card_lokasi AS lokasi,
                d.card_image  AS foto,
                d.card_kategori AS kategori,
                d.card_rating AS rating,
                uf.created_at
            FROM user_favorites uf
            JOIN destinations d ON d.id = uf.destination_id
            WHERE uf.user_id = :uid
            ORDER BY uf.created_at DESC
        ");
        $stmt->execute(['uid' => $userId]);
        $favorites = $stmt->fetchAll();

        jsonResponse(['success' => true, 'favorites' => $favorites]);
    } catch (PDOException $e) {
        error_log('[Favorites list] ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Gagal mengambil data favorit.'], 500);
    }
}

// ── GET: ids ───────────────────────────────────────────────────────────────
if ($method === 'GET' && $action === 'ids') {
    $userId = requireAuth();

    try {
        $stmt = $pdo->prepare("
            SELECT destination_id AS id
            FROM user_favorites
            WHERE user_id = :uid
        ");
        $stmt->execute(['uid' => $userId]);
        $ids = array_column($stmt->fetchAll(), 'id');

        jsonResponse(['success' => true, 'ids' => array_map('intval', $ids)]);
    } catch (PDOException $e) {
        error_log('[Favorites ids] ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Gagal mengambil ID favorit.'], 500);
    }
}

// ── POST: add ──────────────────────────────────────────────────────────────
if ($method === 'POST' && $action === 'add') {
    $userId = requireAuth();
    $destId = validateDestId($_POST['dest_id'] ?? null);

    try {
        // Verifikasi destinasi ada di database
        $check = $pdo->prepare("SELECT id FROM destinations WHERE id = :did LIMIT 1");
        $check->execute(['did' => $destId]);
        if (!$check->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Destinasi tidak ditemukan.'], 404);
        }

        // INSERT IGNORE agar tidak error jika sudah ada (idempotent)
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO user_favorites (user_id, destination_id)
            VALUES (:uid, :did)
        ");
        $stmt->execute(['uid' => $userId, 'did' => $destId]);

        $inserted = $stmt->rowCount();
        $message  = $inserted > 0
            ? 'Destinasi berhasil ditambahkan ke favorit.'
            : 'Destinasi sudah ada di daftar favorit Anda.';

        // Ambil total favorit terbaru
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_favorites WHERE user_id = :uid");
        $countStmt->execute(['uid' => $userId]);
        $totalFav = (int) $countStmt->fetchColumn();

        jsonResponse([
            'success'   => true,
            'added'     => $inserted > 0,
            'message'   => $message,
            'total'     => $totalFav,
        ]);
    } catch (PDOException $e) {
        error_log('[Favorites add] ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Gagal menambahkan favorit.'], 500);
    }
}

// ── POST: remove ───────────────────────────────────────────────────────────
if ($method === 'POST' && $action === 'remove') {
    $userId = requireAuth();
    $destId = validateDestId($_POST['dest_id'] ?? null);

    try {
        $stmt = $pdo->prepare("
            DELETE FROM user_favorites
            WHERE user_id = :uid AND destination_id = :did
        ");
        $stmt->execute(['uid' => $userId, 'did' => $destId]);

        $deleted = $stmt->rowCount();

        // Ambil total favorit terbaru
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_favorites WHERE user_id = :uid");
        $countStmt->execute(['uid' => $userId]);
        $totalFav = (int) $countStmt->fetchColumn();

        jsonResponse([
            'success' => true,
            'removed' => $deleted > 0,
            'message' => $deleted > 0 ? 'Destinasi dihapus dari favorit.' : 'Destinasi tidak ditemukan di favorit.',
            'total'   => $totalFav,
        ]);
    } catch (PDOException $e) {
        error_log('[Favorites remove] ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Gagal menghapus favorit.'], 500);
    }
}

// ── POST: clear ────────────────────────────────────────────────────────────
if ($method === 'POST' && $action === 'clear') {
    $userId = requireAuth();

    try {
        $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);

        jsonResponse([
            'success' => true,
            'message' => 'Seluruh favorit berhasil dihapus.',
            'total'   => 0,
        ]);
    } catch (PDOException $e) {
        error_log('[Favorites clear] ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Gagal menghapus semua favorit.'], 500);
    }
}

// ── Fallback: route tidak dikenal ──────────────────────────────────────────
jsonResponse(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
