<section class="py-5" style="background-color: var(--c-offwhite);" id="reviews-section">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label"><i class="bi bi-chat-square-text me-1"></i> Ulasan Pengguna</span>
      <h2 class="section-title">Rating & Ulasan Wisatawan</h2>
      <p class="section-subtitle">Penilaian pengguna JelajahNusa untuk <?php echo htmlspecialchars($destinasi['nama'], ENT_QUOTES, 'UTF-8'); ?>.</p>
    </div>

    <div class="text-center mb-4">
      <?php if ($reviewSummary['rating'] !== null): ?>
        <div class="display-4 fw-bold text-dark"><?php echo number_format($reviewSummary['rating'], 1); ?></div>
        <div class="text-warning fs-5" aria-label="Rating <?php echo number_format($reviewSummary['rating'], 1); ?> dari 5">
          <?php for ($star = 1; $star <= 5; $star++): ?><i class="bi <?php echo $star <= round($reviewSummary['rating']) ? 'bi-star-fill' : 'bi-star'; ?>"></i><?php endfor; ?>
        </div>
        <div class="text-muted small"><?php echo number_format($reviewSummary['total_ulasan']); ?> ulasan</div>
      <?php else: ?>
        <div class="fw-semibold text-muted">Belum tersedia</div>
        <div class="small text-muted">Belum ada ulasan yang ditampilkan.</div>
      <?php endif; ?>
    </div>

    <?php if (is_array($reviewFlash)): ?>
    <div class="alert alert-<?php echo htmlspecialchars($reviewFlash['type'] ?? 'info', ENT_QUOTES, 'UTF-8'); ?>" role="status">
      <?php echo htmlspecialchars($reviewFlash['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php endif; ?>

    <div class="row g-4 align-items-start mb-5">
      <div class="col-lg-5">
        <div class="bg-white border rounded-3 p-4">
          <?php if (!empty($_SESSION['user']['id'])): ?>
          <h3 class="h5 fw-bold mb-3"><?php echo $myReview ? 'Ubah ulasan Anda' : 'Tulis ulasan'; ?></h3>
          <?php if ($myReview && $myReview['status'] !== 'approved'): ?>
            <p class="small text-muted">Ulasan Anda berstatus <?php echo $myReview['status'] === 'hidden' ? 'disembunyikan' : 'menunggu persetujuan'; ?>. Perubahan baru akan ditinjau kembali.</p>
          <?php endif; ?>
          <form method="post" action="../auth/reviews.php">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="destination_id" value="<?php echo (int)$destinasi['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_review'], ENT_QUOTES, 'UTF-8'); ?>">
            <fieldset class="mb-3">
              <legend class="form-label small fw-semibold">Rating Anda</legend>
              <div class="btn-group" role="group" aria-label="Pilih rating 1 sampai 5 bintang">
                <?php for ($star = 1; $star <= 5; $star++): ?>
                <input class="btn-check" type="radio" name="rating" id="review-rating-<?php echo $star; ?>" value="<?php echo $star; ?>" required <?php echo (int)($myReview['rating'] ?? 0) === $star ? 'checked' : ''; ?>>
                <label class="btn btn-outline-warning" for="review-rating-<?php echo $star; ?>" title="<?php echo $star; ?> bintang"><i class="bi bi-star-fill"></i> <?php echo $star; ?></label>
                <?php endfor; ?>
              </div>
            </fieldset>
            <div class="mb-3">
              <label for="review-text" class="form-label small fw-semibold">Ulasan</label>
              <textarea class="form-control" id="review-text" name="review_text" rows="5" maxlength="3000" required><?php echo htmlspecialchars($myReview['review_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              <div class="form-text">Maksimal 3.000 karakter.</div>
            </div>
            <button type="submit" class="btn btn-forest"><i class="bi bi-send me-1"></i><?php echo $myReview ? 'Simpan perubahan' : 'Kirim ulasan'; ?></button>
          </form>
          <?php if ($myReview): ?>
          <form method="post" action="../auth/reviews.php" class="mt-2" onsubmit="return confirm('Hapus ulasan Anda untuk destinasi ini?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="destination_id" value="<?php echo (int)$destinasi['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_review'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3 me-1"></i>Hapus ulasan</button>
          </form>
          <?php endif; ?>
          <?php else: ?>
          <h3 class="h5 fw-bold mb-2">Punya pengalaman di sini?</h3>
          <p class="text-muted small">Masuk untuk memberikan rating dan ulasan.</p>
          <button type="button" class="btn btn-forest" data-bs-toggle="modal" data-bs-target="#authModal">Masuk untuk memberi ulasan</button>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-7">
        <h3 class="h5 fw-bold mb-3">Ulasan yang ditampilkan</h3>
        <?php if (empty($approvedReviews)): ?>
        <div class="bg-white border rounded-3 p-4 text-center text-muted">Belum tersedia</div>
        <?php else: ?>
        <div class="row g-3">
          <?php foreach ($approvedReviews as $review): ?>
          <div class="col-12">
            <article class="bg-white border rounded-3 p-3">
              <div class="d-flex justify-content-between gap-3">
                <strong><?php echo htmlspecialchars($review['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="text-warning" aria-label="Rating <?php echo (int)$review['rating']; ?> dari 5">
                  <?php for ($star = 1; $star <= 5; $star++): ?><i class="bi <?php echo $star <= (int)$review['rating'] ? 'bi-star-fill' : 'bi-star'; ?>"></i><?php endfor; ?>
                </span>
              </div>
              <p class="small text-muted mb-1"><?php echo nl2br(htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8')); ?></p>
              <time class="text-muted" style="font-size:.75rem" datetime="<?php echo htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(date('d M Y', strtotime($review['created_at'])), ENT_QUOTES, 'UTF-8'); ?></time>
            </article>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
