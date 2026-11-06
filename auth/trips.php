<?php
/**
 * JelajahNusa - API Endpoint: Kelola Rencana Perjalanan (Trips)
 * Lokasi: auth/trips.php
 *
 * Semua operasi tulis mensyaratkan session login yang valid.
 * user_id selalu dari $_SESSION['user']['id'] — tidak pernah dari input browser.
 * Setiap query memfilter WHERE user_id = :uid untuk isolasi data antar pengguna.
 *
 * Routes (GET):
 *   ?action=list                → daftar semua trip milik user yang login
 *   ?action=get&trip_id=N       → detail 1 trip + semua item-nya
 *
 * Routes (POST):
 *   action=create               → buat trip baru
 *   action=delete_trip          → hapus trip (beserta semua item-nya via CASCADE)
 *   action=add_item             → tambah item ke trip
 *   action=delete_item          → hapus 1 item dari trip
 *   action=update_item_order    → perbarui sort_order item (opsional future use)
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

function requireAuth(): int
{
    if (!isset($_SESSION['user']['id']) || !is_int($_SESSION['user']['id'])) {
        jsonOut([
            'success' => false,
            'auth'    => false,
            'message' => 'Silakan login terlebih dahulu untuk mengelola rencana perjalanan.',
        ], 401);
    }
    return (int) $_SESSION['user']['id'];
}

function vInt(mixed $raw, int $min = 1): int
{
    $v = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min]]);
    if ($v === false) {
        jsonOut(['success' => false, 'message' => 'Parameter ID tidak valid.'], 422);
    }
    return (int) $v;
}

function vStr(mixed $raw, int $max = 200, bool $required = true): string
{
    $s = trim((string)($raw ?? ''));
    if ($required && $s === '') {
        jsonOut(['success' => false, 'message' => 'Field wajib tidak boleh kosong.'], 422);
    }
    return mb_substr($s, 0, $max);
}

function vDate(mixed $raw): string
{
    $s = trim((string)($raw ?? ''));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) || !strtotime($s)) {
        jsonOut(['success' => false, 'message' => "Format tanggal tidak valid: {$s}"], 422);
    }
    return $s;
}

/**
 * Validasi bahwa tanggal tidak lebih awal dari hari ini (waktu Asia/Jakarta).
 * Hari ini sendiri tetap diizinkan.
 */
function vDateNotPast(string $dateStr): void
{
    // Gunakan timezone Asia/Jakarta agar konsisten dengan waktu lokal Indonesia
    $tz      = new DateTimeZone('Asia/Jakarta');
    $today   = new DateTime('today', $tz);           // 00:00:00 hari ini
    $checked = new DateTime($dateStr . 'T00:00:00', $tz);

    if ($checked < $today) {
        jsonOut([
            'success' => false,
            'message' => 'Tanggal tidak boleh sebelum hari ini. Pilih tanggal mulai dari hari ini.',
        ], 422);
    }
}

// ─── Pastikan trip milik user yang sedang login ───────────────────────────────
function ownsTrip(PDO $pdo, int $tripId, int $userId): void
{
    $s = $pdo->prepare("SELECT id FROM trips WHERE id = :tid AND user_id = :uid LIMIT 1");
    $s->execute(['tid' => $tripId, 'uid' => $userId]);
    if (!$s->fetch()) {
        jsonOut(['success' => false, 'message' => 'Trip tidak ditemukan atau bukan milik Anda.'], 403);
    }
}

// ─── Router ──────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$action = match ($method) {
    'GET'  => trim($_GET['action']  ?? ''),
    'POST' => trim($_POST['action'] ?? ''),
    default => ''
};

// ══════════════════════════════════════════════════════════════════════════════
// GET: list — semua trip milik user
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'GET' && $action === 'list') {
    $uid = requireAuth();
    try {
        $stmt = $pdo->prepare("
            SELECT
                t.id, t.name, t.destination,
                t.start_date, t.end_date,
                t.category, t.notes, t.days,
                t.created_at,
                COUNT(ti.id) AS item_count
            FROM trips t
            LEFT JOIN trip_items ti ON ti.trip_id = t.id
            WHERE t.user_id = :uid
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute(['uid' => $uid]);
        $trips = $stmt->fetchAll();
        jsonOut(['success' => true, 'trips' => $trips]);
    } catch (PDOException $e) {
        error_log('[trips list] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal mengambil data trip.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// GET: get — detail 1 trip + item-nya
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'GET' && $action === 'get') {
    $uid    = requireAuth();
    $tripId = vInt($_GET['trip_id'] ?? null);
    ownsTrip($pdo, $tripId, $uid);

    try {
        $tStmt = $pdo->prepare("SELECT * FROM trips WHERE id = :tid LIMIT 1");
        $tStmt->execute(['tid' => $tripId]);
        $trip = $tStmt->fetch();

        $iStmt = $pdo->prepare("
            SELECT id, day_number, category, name, location,
                   time_visit, photo_url, note, sort_order, created_at
            FROM trip_items
            WHERE trip_id = :tid
            ORDER BY day_number ASC, sort_order ASC, id ASC
        ");
        $iStmt->execute(['tid' => $tripId]);
        $items = $iStmt->fetchAll();

        $trip['items'] = $items;
        jsonOut(['success' => true, 'trip' => $trip]);
    } catch (PDOException $e) {
        error_log('[trips get] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal mengambil detail trip.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: create — buat trip baru (+ opsional item pertama)
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && $action === 'create') {
    $uid  = requireAuth();
    $name = vStr($_POST['name'] ?? '', 150);
    $dest = vStr($_POST['destination'] ?? '', 200);
    $sd   = vDate($_POST['start_date'] ?? '');
    $ed   = vDate($_POST['end_date']   ?? '');
    $cat  = vStr($_POST['category']    ?? 'Pasangan & Keluarga', 80, false) ?: 'Pasangan & Keluarga';
    $notes = vStr($_POST['notes']      ?? '', 2000, false);

    // Validasi tanggal tidak lampau (berlaku untuk keduanya)
    vDateNotPast($sd);
    vDateNotPast($ed);

    if (strtotime($ed) < strtotime($sd)) {
        jsonOut(['success' => false, 'message' => 'Tanggal pulang tidak boleh sebelum tanggal berangkat.'], 422);
    }

    $days = (int) round((strtotime($ed) - strtotime($sd)) / 86400) + 1;

    // Nama max 150 char
    if (mb_strlen($name) > 150) {
        jsonOut(['success' => false, 'message' => 'Nama perjalanan maksimal 150 karakter.'], 422);
    }

    try {
        $ins = $pdo->prepare("
            INSERT INTO trips (user_id, name, destination, start_date, end_date, category, notes, days)
            VALUES (:uid, :name, :dest, :sd, :ed, :cat, :notes, :days)
        ");
        $ins->execute([
            'uid'   => $uid,
            'name'  => $name,
            'dest'  => $dest,
            'sd'    => $sd,
            'ed'    => $ed,
            'cat'   => $cat,
            'notes' => $notes,
            'days'  => $days,
        ]);
        $newTripId = (int) $pdo->lastInsertId();

        // Opsional: langsung tambahkan item pertama jika disertakan
        $itemAdded = false;
        $itemName  = vStr($_POST['item_name'] ?? '', 200, false);
        if ($itemName !== '') {
            $iDay  = max(1, (int)($_POST['item_day'] ?? 1));
            $iCat  = vStr($_POST['item_category'] ?? 'Destinasi', 60, false) ?: 'Destinasi';
            $iLoc  = vStr($_POST['item_location'] ?? '', 200, false);
            $iTime = vStr($_POST['item_time']     ?? '', 20, false);
            $iPhoto = vStr($_POST['item_photo']   ?? '', 500, false);
            $iNote  = vStr($_POST['item_note']    ?? '', 1000, false);

            $pdo->prepare("
                INSERT INTO trip_items
                    (trip_id, user_id, day_number, category, name, location, time_visit, photo_url, note)
                VALUES (:tid, :uid, :day, :cat, :name, :loc, :time, :photo, :note)
            ")->execute([
                'tid'   => $newTripId,
                'uid'   => $uid,
                'day'   => $iDay,
                'cat'   => $iCat,
                'name'  => $itemName,
                'loc'   => $iLoc,
                'time'  => $iTime,
                'photo' => $iPhoto,
                'note'  => $iNote,
            ]);
            $itemAdded = true;
        }

        jsonOut([
            'success'    => true,
            'message'    => 'Rencana perjalanan berhasil dibuat.',
            'trip_id'    => $newTripId,
            'days'       => $days,
            'item_added' => $itemAdded,
        ]);
    } catch (PDOException $e) {
        error_log('[trips create] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal membuat rencana perjalanan.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: delete_trip — hapus trip beserta semua item (via CASCADE)
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && $action === 'delete_trip') {
    $uid    = requireAuth();
    $tripId = vInt($_POST['trip_id'] ?? null);
    ownsTrip($pdo, $tripId, $uid);

    try {
        $pdo->prepare("DELETE FROM trips WHERE id = :tid AND user_id = :uid")
            ->execute(['tid' => $tripId, 'uid' => $uid]);
        jsonOut(['success' => true, 'message' => 'Rencana perjalanan berhasil dihapus.']);
    } catch (PDOException $e) {
        error_log('[trips delete_trip] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal menghapus rencana perjalanan.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: add_item — tambah item ke trip yang sudah ada
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && $action === 'add_item') {
    $uid    = requireAuth();
    $tripId = vInt($_POST['trip_id'] ?? null);
    ownsTrip($pdo, $tripId, $uid);

    // Validasi day_number tidak melebihi durasi trip
    try {
        $tripRow = $pdo->prepare("SELECT days FROM trips WHERE id = :tid LIMIT 1");
        $tripRow->execute(['tid' => $tripId]);
        $tripData = $tripRow->fetch();
    } catch (PDOException $e) {
        jsonOut(['success' => false, 'message' => 'Trip tidak ditemukan.'], 404);
    }

    $dayNum = (int)($_POST['day_number'] ?? 1);
    if ($dayNum < 1 || $dayNum > (int)$tripData['days']) {
        jsonOut(['success' => false, 'message' => "Hari ke-{$dayNum} tidak valid untuk trip ini (durasi: {$tripData['days']} hari)."], 422);
    }

    $name  = vStr($_POST['name']     ?? '', 200);
    $cat   = vStr($_POST['category'] ?? 'Destinasi', 60, false) ?: 'Destinasi';
    $loc   = vStr($_POST['location'] ?? '', 200, false);
    $time  = vStr($_POST['time_visit'] ?? '', 20, false);
    $photo = vStr($_POST['photo_url']  ?? '', 500, false);
    $note  = vStr($_POST['note']       ?? '', 1000, false);

    if (mb_strlen($name) > 200) {
        jsonOut(['success' => false, 'message' => 'Nama item maksimal 200 karakter.'], 422);
    }

    try {
        // sort_order = max sort_order pada hari yg sama + 1
        $sortStmt = $pdo->prepare("
            SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_order
            FROM trip_items
            WHERE trip_id = :tid AND day_number = :day
        ");
        $sortStmt->execute(['tid' => $tripId, 'day' => $dayNum]);
        $nextOrder = (int)$sortStmt->fetchColumn();

        $ins = $pdo->prepare("
            INSERT INTO trip_items
                (trip_id, user_id, day_number, category, name, location, time_visit, photo_url, note, sort_order)
            VALUES (:tid, :uid, :day, :cat, :name, :loc, :time, :photo, :note, :order)
        ");
        $ins->execute([
            'tid'   => $tripId,
            'uid'   => $uid,
            'day'   => $dayNum,
            'cat'   => $cat,
            'name'  => $name,
            'loc'   => $loc,
            'time'  => $time,
            'photo' => $photo,
            'note'  => $note,
            'order' => $nextOrder,
        ]);
        $newItemId = (int) $pdo->lastInsertId();

        jsonOut([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke rencana perjalanan.',
            'item_id' => $newItemId,
        ]);
    } catch (PDOException $e) {
        error_log('[trips add_item] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal menambahkan item.'], 500);
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: delete_item — hapus 1 item dari trip
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && $action === 'delete_item') {
    $uid    = requireAuth();
    $itemId = vInt($_POST['item_id'] ?? null);

    try {
        // Pastikan item milik user yang sedang login
        $check = $pdo->prepare("SELECT id FROM trip_items WHERE id = :iid AND user_id = :uid LIMIT 1");
        $check->execute(['iid' => $itemId, 'uid' => $uid]);
        if (!$check->fetch()) {
            jsonOut(['success' => false, 'message' => 'Item tidak ditemukan atau bukan milik Anda.'], 403);
        }

        $pdo->prepare("DELETE FROM trip_items WHERE id = :iid AND user_id = :uid")
            ->execute(['iid' => $itemId, 'uid' => $uid]);
        jsonOut(['success' => true, 'message' => 'Item berhasil dihapus.']);
    } catch (PDOException $e) {
        error_log('[trips delete_item] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal menghapus item.'], 500);
    }
}

// ─── Fallback ─────────────────────────────────────────────────────────────────
jsonOut(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
