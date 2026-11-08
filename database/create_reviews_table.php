<?php
/**
 * Membuat tabel ulasan internal pengguna.
 * Jalankan sekali dengan: php database/create_reviews_table.php
 */

$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? 'cli', ['127.0.0.1', '::1', 'cli'], true);
if (!$isLocalhost && PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Akses ditolak.\n");
}

require_once __DIR__ . '/../config/database.php';

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS destination_reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    destination_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review_text TEXT NOT NULL,
    status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_destination_review_user (destination_id, user_id),
    KEY idx_destination_review_visible (destination_id, status, created_at),
    CONSTRAINT chk_destination_review_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT fk_destination_review_destination
        FOREIGN KEY (destination_id) REFERENCES destinations (id) ON DELETE CASCADE,
    CONSTRAINT fk_destination_review_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

try {
    $pdo->exec($sql);
    echo "Tabel destination_reviews siap digunakan.\n";
} catch (PDOException $e) {
    error_log('[Reviews Migration Error] ' . $e->getMessage());
    fwrite(STDERR, "Gagal membuat tabel ulasan. Periksa log PHP untuk detail teknis.\n");
    exit(1);
}
