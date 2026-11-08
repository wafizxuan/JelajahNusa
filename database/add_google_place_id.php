<?php
/**
 * JelajahNusa - Migrasi: Tambah kolom google_place_id ke tabel destinations
 * Jalankan sekali via CLI: php database/add_google_place_id.php
 */

$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? 'cli', ['127.0.0.1', '::1', 'cli']);
if (!$isLocalhost && PHP_SAPI !== 'cli') {
    http_response_code(403);
    die("Akses ditolak.\n");
}

require_once __DIR__ . '/../config/database.php';

try {
    // Cek apakah kolom sudah ada
    $check = $pdo->query("SHOW COLUMNS FROM destinations LIKE 'google_place_id'")->fetch();

    if ($check) {
        echo "✅ Kolom 'google_place_id' sudah ada. Tidak perlu migrasi.\n";
    } else {
        // Tambahkan kolom google_place_id
        // NULL diizinkan: destinasi lama belum punya Place ID
        $pdo->exec("
            ALTER TABLE destinations
            ADD COLUMN google_place_id VARCHAR(255) NULL DEFAULT NULL
            COMMENT 'Google Maps Place ID — disimpan sesuai kebijakan Google Places API'
            AFTER total_ulasan
        ");
        echo "✅ Kolom 'google_place_id' (VARCHAR 255, NULL) berhasil ditambahkan.\n";
    }

    // Verifikasi
    $result = $pdo->query("SHOW COLUMNS FROM destinations LIKE 'google_place_id'")->fetch();
    echo "   Type: " . ($result['Type'] ?? '-') . "\n";
    echo "   Null: " . ($result['Null'] ?? '-') . "\n";
    echo "   Default: " . ($result['Default'] ?? 'NULL') . "\n";

} catch (PDOException $e) {
    echo "❌ Gagal: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Migrasi selesai.\n";
