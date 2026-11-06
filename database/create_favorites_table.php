<?php
/**
 * JelajahNusa - Migrasi: Buat Tabel user_favorites
 * Jalankan sekali via browser atau CLI untuk membuat tabel.
 * Lokasi: database/create_favorites_table.php
 */

// Keamanan: hanya jalankan dari CLI atau localhost
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? 'cli', ['127.0.0.1', '::1', 'cli']);
if (!$isLocalhost && PHP_SAPI !== 'cli') {
    http_response_code(403);
    die('Akses ditolak. Skrip ini hanya dapat dijalankan dari localhost atau CLI.');
}

require_once __DIR__ . '/../config/database.php';

$sql = "
CREATE TABLE IF NOT EXISTS user_favorites (
    id            INT             NOT NULL AUTO_INCREMENT,
    user_id       INT             NOT NULL,
    destination_id INT            NOT NULL,
    created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_dest (user_id, destination_id),
    KEY idx_user_id (user_id),
    KEY idx_destination_id (destination_id),
    CONSTRAINT fk_uf_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_uf_dest
        FOREIGN KEY (destination_id) REFERENCES destinations(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $pdo->exec($sql);
    echo "✅ Tabel 'user_favorites' berhasil dibuat (atau sudah ada).\n";
    echo "   Kolom: id, user_id, destination_id, created_at\n";
    echo "   Constraint: UNIQUE(user_id, destination_id), FK ke users dan destinations\n";
} catch (PDOException $e) {
    error_log('[Migration Error] ' . $e->getMessage());
    echo "❌ Gagal membuat tabel: " . $e->getMessage() . "\n";
}
