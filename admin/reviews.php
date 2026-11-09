<?php
/**
 * Moderasi ulasan pengguna: sembunyikan atau hapus ulasan.
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Token keamanan CSRF tidak valid.';
        header('Location: reviews.php');
        exit;
    }

    $reviewId = filter_var($_POST['review_id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $action = $_POST['action'] ?? '';
    if (!$reviewId || !in_array($action, ['hide', 'delete'], true)) {
        $_SESSION['flash_error'] = 'Aksi moderasi tidak valid.';
    } else {
        try {
            if ($action === 'hide') {
                $stmt = $pdo->prepare("UPDATE destination_reviews SET status = 'hidden' WHERE id = :id");
                $stmt->execute(['id' => $reviewId]);
                $_SESSION['flash_success'] = 'Ulasan berhasil disembunyikan.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM destination_reviews WHERE id = :id');
                $stmt->execute(['id' => $reviewId]);
                $_SESSION['flash_success'] = 'Ulasan berhasil dihapus.';
            }
        } catch (PDOException $e) {
            error_log('[Admin Review Moderation Error] ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Ulasan belum dapat diproses.';
        }
    }

    header('Location: reviews.php');
    exit;
}

$reviews = [];
try {
    $stmt = $pdo->query("SELECT r.id, r.rating, r.review_text, r.status, r.created_at, u.name AS user_name, d.card_nama AS destination_name FROM destination_reviews r JOIN users u ON u.id = r.user_id JOIN destinations d ON d.id = r.destination_id ORDER BY r.created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('[Admin Reviews Query Error] ' . $e->getMessage());
}

$csrfToken = generateCsrfToken();
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
$adminUser = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="content-language" content="id">
  <title>Moderasi Ulasan | Administrator JelajahNusa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f7f7f2; color: #29352d; }
    .review-admin-main { max-width: 1080px; margin: 0 auto; padding: 28px 18px; }
    .review-admin-nav { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 28px; }
    .review-admin-item { background: #fff; border: 1px solid rgba(52,91,73,.14); border-radius: 6px; padding: 18px; }
    .review-admin-item + .review-admin-item { margin-top: 12px; }
  </style>
</head>
<body>
  <main class="review-admin-main">
    <nav class="review-admin-nav" aria-label="Navigasi admin">
      <a class="btn btn-outline-success btn-sm" href="index.php"><i class="bi bi-grid me-1"></i>Dasbor</a>
      <a class="btn btn-outline-success btn-sm" href="destinasi.php">Destinasi</a>
      <a class="btn btn-outline-success btn-sm" href="users.php">Pengguna</a>
      <a class="btn btn-success btn-sm" href="reviews.php">Ulasan</a>
      <span class="ms-auto small text-muted"><?php echo htmlspecialchars($adminUser['name'], ENT_QUOTES, 'UTF-8'); ?></span>
    </nav>

    <header class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
      <div>
        <p class="text-success small text-uppercase fw-bold mb-1">Konten Pengguna</p>
        <h1 class="h3 fw-bold mb-1">Moderasi Ulasan</h1>
        <p class="text-muted mb-0">Ulasan langsung tampil secara publik. Sembunyikan atau hapus ulasan yang tidak sesuai.</p>
      </div>
      <span class="badge text-bg-light border"><?php echo count($reviews); ?> ulasan</span>
    </header>

    <?php if ($flashSuccess): ?><div class="alert alert-success" role="status"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    <?php if ($flashError): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

    <?php if (!$reviews): ?>
      <div class="review-admin-item text-center text-muted py-5">Belum ada ulasan untuk dimoderasi.</div>
    <?php else: ?>
      <?php foreach ($reviews as $review): ?>
      <article class="review-admin-item">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <h2 class="h6 fw-bold mb-1"><?php echo htmlspecialchars($review['destination_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="small text-muted">
              <?php echo htmlspecialchars($review['user_name'], ENT_QUOTES, 'UTF-8'); ?>
              <span class="mx-1">·</span>
              <?php echo htmlspecialchars(formatTanggalIndonesia($review['created_at']), ENT_QUOTES, 'UTF-8'); ?>
            </div>
          </div>
          <span class="badge <?php echo $review['status'] === 'hidden' ? 'text-bg-secondary' : 'text-bg-success'; ?>">
            <?php echo $review['status'] === 'hidden' ? 'Disembunyikan' : 'Publik'; ?>
          </span>
        </div>
        <div class="text-warning my-2" aria-label="Penilaian <?php echo (int)$review['rating']; ?> dari 5">
          <?php for ($star = 1; $star <= 5; $star++): ?><i class="bi <?php echo $star <= (int)$review['rating'] ? 'bi-star-fill' : 'bi-star'; ?>"></i><?php endfor; ?>
          <span class="text-dark small ms-1"><?php echo (int)$review['rating']; ?>/5</span>
        </div>
        <p class="mb-3 text-break"><?php echo nl2br(htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8')); ?></p>
        <div class="d-flex gap-2">
          <?php if ($review['status'] !== 'hidden'): ?>
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="review_id" value="<?php echo (int)$review['id']; ?>">
            <input type="hidden" name="action" value="hide">
            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye-slash me-1"></i>Sembunyikan</button>
          </form>
          <?php endif; ?>
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="review_id" value="<?php echo (int)$review['id']; ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus ulasan ini secara permanen?');"><i class="bi bi-trash3 me-1"></i>Hapus</button>
          </form>
        </div>
      </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>
</body>
</html>
