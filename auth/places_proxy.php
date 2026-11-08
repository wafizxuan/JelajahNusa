<?php
/**
 * JelajahNusa - Google Places API Proxy (Server-Side)
 * Lokasi: auth/places_proxy.php
 *
 * Semua request ke Google Places API dilakukan dari server PHP ini.
 * API key TIDAK pernah dikirim ke browser.
 *
 * Routes (GET):
 *   ?action=search&q=QUERY          → Text Search (cari tempat)
 *   ?action=details&place_id=ID     → Place Details (ambil rating + ulasan)
 *
 * Routes (POST):
 *   action=save_place_id            → simpan Place ID ke destinasi (admin only)
 *
 * Kebijakan caching Google:
 *   - Data rating/ulasan: TIDAK di-cache (diambil langsung saat diminta)
 *   - Place ID: BOLEH disimpan permanen di database
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../config/places_api.php';
require_once __DIR__ . '/../config/database.php';

// ─── Helper output ─────────────────────────────────────────────────────────
function jsonOut(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ─── Cek apakah admin (untuk save_place_id) ─────────────────────────────────
function requireAdmin(): void
{
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        jsonOut(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
}

// ─── Cek API tersedia ────────────────────────────────────────────────────────
if (!defined('GOOGLE_PLACES_ENABLED') || !GOOGLE_PLACES_ENABLED) {
    // Untuk save_place_id, tetap izinkan meski API disabled
    $m = $_SERVER['REQUEST_METHOD'];
    $a = $m === 'POST' ? ($_POST['action'] ?? '') : ($_GET['action'] ?? '');
    if ($a !== 'save_place_id') {
        jsonOut([
            'success'  => false,
            'disabled' => true,
            'message'  => 'Google Places API belum dikonfigurasi. Isi GOOGLE_PLACES_API_KEY di config/places_api.php dan set GOOGLE_PLACES_ENABLED = true.',
        ], 503);
    }
}

$apiKey = GOOGLE_PLACES_API_KEY;

if ($apiKey === 'YOUR_GOOGLE_PLACES_API_KEY_HERE' || empty($apiKey)) {
    $m = $_SERVER['REQUEST_METHOD'];
    $a = $m === 'POST' ? ($_POST['action'] ?? '') : ($_GET['action'] ?? '');
    if ($a !== 'save_place_id') {
        jsonOut(['success' => false, 'disabled' => true, 'message' => 'API key belum diisi.'], 503);
    }
}

// ─── HTTP helper — curl ke Google API ────────────────────────────────────────
function googleRequest(string $url, array $headers = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => GOOGLE_PLACES_TIMEOUT,
        CURLOPT_HTTPHEADER     => array_merge(['Accept: application/json'], $headers),
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err    = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => $err, 'status' => 0, 'body' => null];
    }
    $data = json_decode($body, true);
    return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $data, 'error' => null];
}

// ─── Router ──────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$action = match ($method) {
    'GET'  => trim($_GET['action']  ?? ''),
    'POST' => trim($_POST['action'] ?? ''),
    default => ''
};

// ══════════════════════════════════════════════════════════════════════════════
// GET: search — Text Search (New) untuk mencari tempat
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'GET' && $action === 'search') {
    requireAdmin();

    $query = trim($_GET['q'] ?? '');
    if (empty($query) || mb_strlen($query) < 3) {
        jsonOut(['success' => false, 'message' => 'Query pencarian minimal 3 karakter.'], 422);
    }
    if (mb_strlen($query) > 200) {
        jsonOut(['success' => false, 'message' => 'Query terlalu panjang.'], 422);
    }

    // Text Search (New) — POST dengan body JSON
    $url     = GOOGLE_PLACES_BASE_URL . '/places:searchText';
    $payload = json_encode([
        'textQuery'     => $query,
        'languageCode'  => 'id',
        'regionCode'    => 'ID',
        'maxResultCount' => 5,
    ]);

    $headers = [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . $apiKey,
        // FieldMask: hanya minta field yang diperlukan untuk pencarian
        'X-Goog-FieldMask: places.id,places.displayName,places.formattedAddress,places.rating,places.userRatingCount,places.googleMapsUri',
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => GOOGLE_PLACES_TIMEOUT,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        error_log('[Places search curl] ' . $curlErr);
        jsonOut(['success' => false, 'message' => 'Gagal menghubungi Google API. Periksa koneksi server.'], 502);
    }

    $data = json_decode($body, true);

    if ($status !== 200 || isset($data['error'])) {
        $msg = $data['error']['message'] ?? 'Google API mengembalikan error.';
        error_log('[Places search API] status=' . $status . ' msg=' . $msg);
        jsonOut(['success' => false, 'message' => $msg, 'status' => $status], 502);
    }

    $places = $data['places'] ?? [];
    if (empty($places)) {
        jsonOut(['success' => true, 'places' => [], 'message' => 'Tidak ada tempat yang ditemukan untuk query ini.']);
    }

    // Mapping hasil ke format ringkas
    $results = array_map(fn($p) => [
        'place_id'      => $p['id']                      ?? '',
        'name'          => $p['displayName']['text']      ?? '',
        'address'       => $p['formattedAddress']         ?? '',
        'rating'        => $p['rating']                   ?? null,
        'user_ratings'  => $p['userRatingCount']          ?? null,
        'maps_url'      => $p['googleMapsUri']             ?? null,
    ], $places);

    jsonOut(['success' => true, 'places' => $results]);
}

// ══════════════════════════════════════════════════════════════════════════════
// GET: details — Place Details (New) ambil rating + ulasan
// Catatan kebijakan Google: Data ini tidak boleh di-cache.
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'GET' && $action === 'details') {
    $placeId = trim($_GET['place_id'] ?? '');
    if (empty($placeId)) {
        jsonOut(['success' => false, 'message' => 'place_id wajib disertakan.'], 422);
    }

    // Sanitasi: Place ID hanya boleh berisi alfanumerik, tanda minus, dan underscore
    if (!preg_match('/^[A-Za-z0-9_\-]+$/', $placeId)) {
        jsonOut(['success' => false, 'message' => 'Format place_id tidak valid.'], 422);
    }

    $url = GOOGLE_PLACES_BASE_URL . '/places/' . urlencode($placeId);

    $headers = [
        'X-Goog-Api-Key: ' . $apiKey,
        // FieldMask: hanya minta field yang diperlukan — hemat kuota
        'X-Goog-FieldMask: id,displayName,rating,userRatingCount,reviews,googleMapsUri,websiteUri',
        'Accept-Language: id',
    ];

    $res = googleRequest($url . '?languageCode=id', $headers);

    if (!$res['ok']) {
        $msg = $res['error'] ?? ($res['body']['error']['message'] ?? 'Gagal mengambil data dari Google API.');
        error_log('[Places details] place_id=' . $placeId . ' error=' . $msg);
        jsonOut(['success' => false, 'message' => $msg], 502);
    }

    $p = $res['body'];
    if (!$p || isset($p['error'])) {
        jsonOut(['success' => false, 'message' => $p['error']['message'] ?? 'Data tidak ditemukan.'], 404);
    }

    // Proses ulasan — max 5 sesuai yang dikembalikan API
    $reviews = [];
    foreach ($p['reviews'] ?? [] as $rev) {
        $reviews[] = [
            'author_name'        => $rev['authorAttribution']['displayName']  ?? 'Anonim',
            'author_photo_url'   => $rev['authorAttribution']['photoUri']      ?? null,
            'author_uri'         => $rev['authorAttribution']['uri']           ?? null,
            'rating'             => $rev['rating']                              ?? null,
            'text'               => $rev['text']['text']                        ?? '',
            'relative_time'      => $rev['relativePublishTimeDescription']      ?? '',
            'publish_time'       => $rev['publishTime']                         ?? null,
            'original_language'  => $rev['text']['languageCode']                ?? null,
        ];
    }

    jsonOut([
        'success'      => true,
        'place_id'     => $p['id']                  ?? $placeId,
        'name'         => $p['displayName']['text']  ?? '',
        'rating'       => $p['rating']               ?? null,
        'user_ratings' => $p['userRatingCount']       ?? null,
        'maps_url'     => $p['googleMapsUri']         ?? null,
        'website_url'  => $p['websiteUri']            ?? null,
        'reviews'      => $reviews,
        'attribution'  => 'Data rating dan ulasan bersumber dari Google Maps.',
    ]);
}

// ══════════════════════════════════════════════════════════════════════════════
// POST: save_place_id — simpan Place ID ke destinasi (admin only)
// Place ID boleh disimpan sesuai kebijakan Google.
// ══════════════════════════════════════════════════════════════════════════════
if ($method === 'POST' && $action === 'save_place_id') {
    requireAdmin();

    $destId  = filter_var($_POST['dest_id']  ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $placeId = trim($_POST['place_id'] ?? '');

    if (!$destId) {
        jsonOut(['success' => false, 'message' => 'ID destinasi tidak valid.'], 422);
    }

    // Izinkan placeId kosong (untuk menghapus asosiasi)
    if ($placeId !== '' && !preg_match('/^[A-Za-z0-9_\-]+$/', $placeId)) {
        jsonOut(['success' => false, 'message' => 'Format place_id tidak valid.'], 422);
    }

    try {
        $stmt = $pdo->prepare("UPDATE destinations SET google_place_id = :pid WHERE id = :id");
        $stmt->execute([
            'pid' => $placeId ?: null,
            'id'  => $destId,
        ]);

        if ($stmt->rowCount() === 0) {
            jsonOut(['success' => false, 'message' => 'Destinasi tidak ditemukan.'], 404);
        }

        jsonOut([
            'success'  => true,
            'message'  => $placeId
                ? 'Place ID berhasil disimpan ke destinasi.'
                : 'Place ID berhasil dihapus dari destinasi.',
            'place_id' => $placeId ?: null,
        ]);
    } catch (PDOException $e) {
        error_log('[Places save_place_id] ' . $e->getMessage());
        jsonOut(['success' => false, 'message' => 'Gagal menyimpan ke database.'], 500);
    }
}

// ─── Fallback ─────────────────────────────────────────────────────────────────
jsonOut(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
