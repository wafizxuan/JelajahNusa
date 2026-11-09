<?php
/**
 * Endpoint tambah, edit, dan hapus ulasan destinasi.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode request tidak diizinkan.');
}

$destinationId = filter_var($_POST['destination_id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);
$redirectUrl = '../pages/detail-destinasi.php?id=' . (int)($destinationId ?: 0);

if (empty($_SESSION['user']['id'])) {
    $redirectTarget = 'pages/detail-destinasi.php?id=' . (int)($destinationId ?: 0);
    header('Location: ../index.php?auth=login&redirect=' . rawurlencode($redirectTarget));
    exit;
}

$userId = filter_var($_SESSION['user']['id'], FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);
$token = $_POST['csrf_token'] ?? '';
if (!$destinationId || !$userId || !is_string($token)
    || empty($_SESSION['csrf_review'])
    || !hash_equals($_SESSION['csrf_review'], $token)) {
    $_SESSION['review_flash'] = ['type' => 'danger', 'message' => 'Permintaan tidak valid atau sesi keamanan telah berakhir.'];
    header('Location: ' . $redirectUrl);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $destinationStmt = $pdo->prepare('SELECT id FROM destinations WHERE id = :id LIMIT 1');
    $destinationStmt->execute(['id' => $destinationId]);
    if (!$destinationStmt->fetchColumn()) {
        http_response_code(404);
        exit('Destinasi tidak ditemukan.');
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 5]
        ]);
        $reviewText = trim((string)($_POST['review_text'] ?? ''));
        $textLength = function_exists('mb_strlen') ? mb_strlen($reviewText, 'UTF-8') : strlen($reviewText);

        if ($rating === false || $rating === null || $reviewText === '' || $textLength > 3000) {
            $_SESSION['review_flash'] = ['type' => 'danger', 'message' => 'Pilih penilaian 1–5 dan isi ulasan maksimal 3.000 karakter.'];
        } else {
            $stmt = $pdo->prepare(" 
                INSERT INTO destination_reviews (destination_id, user_id, rating, review_text, status)
                VALUES (:destination_id, :user_id, :rating, :review_text, 'approved')
                ON DUPLICATE KEY UPDATE
                    rating = :updated_rating,
                    review_text = :updated_text,
                    status = IF(status = 'hidden', 'hidden', 'approved')
            ");
            $stmt->execute([
                'destination_id' => $destinationId,
                'user_id' => $userId,
                'rating' => $rating,
                'review_text' => $reviewText,
                'updated_rating' => $rating,
                'updated_text' => $reviewText,
            ]);
            $_SESSION['review_flash'] = ['type' => 'success', 'message' => 'Ulasan berhasil dipublikasikan.'];
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM destination_reviews WHERE destination_id = :destination_id AND user_id = :user_id');
        $stmt->execute(['destination_id' => $destinationId, 'user_id' => $userId]);
        $_SESSION['review_flash'] = [
            'type' => $stmt->rowCount() > 0 ? 'success' : 'warning',
            'message' => $stmt->rowCount() > 0 ? 'Ulasan Anda berhasil dihapus.' : 'Ulasan tidak ditemukan.'
        ];
    } else {
        $_SESSION['review_flash'] = ['type' => 'danger', 'message' => 'Aksi ulasan tidak dikenal.'];
    }
} catch (PDOException $e) {
    error_log('[Review Submission Error] ' . $e->getMessage());
    $_SESSION['review_flash'] = ['type' => 'danger', 'message' => 'Ulasan belum dapat diproses. Silakan coba kembali.'];
}

header('Location: ' . $redirectUrl);
exit;
