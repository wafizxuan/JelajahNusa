<?php
/**
 * JelajahNusa - Migrasi: Kolom rating/card_rating/total_ulasan menjadi nullable
 * Tujuan: memisahkan "belum ada rating" dari "rating = 5.0 (palsu)"
 *
 * Setelah migrasi:
 *   - rating IS NULL      → belum terhubung Google atau API belum aktif
 *   - rating = angka      → nilai nyata dari Google Places API
 *
 * Data lama dari database (nilai default statis 5.0 / 0) di-set ke NULL
 * karena nilainya bukan dari Google — tidak boleh ditampilkan seolah rating asli.
 */

$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? 'cli', ['127.0.0.1', '::1', 'cli']);
if (!$isLocalhost && PHP_SAPI !== 'cli') {
    http_response_code(403); die("Akses ditolak.\n");
}

require_once __DIR__ . '/../config/database.php';

try {
    $pdo->beginTransaction();

    // 1. Ubah tipe kolom menjadi nullable
    // Catatan: ALTER TABLE adalah DDL yang auto-commit di MySQL.
    // Transaction di sini hanya untuk DML (UPDATE) yang mengikutinya.
    $pdo->exec("
        ALTER TABLE destinations
        MODIFY COLUMN rating       DECIMAL(2,1) NULL DEFAULT NULL,
        MODIFY COLUMN card_rating  DECIMAL(2,1) NULL DEFAULT NULL,
        MODIFY COLUMN total_ulasan INT          NULL DEFAULT NULL
    ");
    echo "✅ Kolom rating, card_rating, total_ulasan diubah menjadi NULL DEFAULT NULL.\n";

    // Commit setelah DDL (DDL sudah auto-commit, ini hanya menutup transaksi DML jika ada)
    try { $pdo->commit(); } catch (Throwable $_) {}

    // 2. Set semua nilai ke NULL karena nilainya statis bukan dari Google
    //    (semua baris saat ini punya google_place_id = NULL, artinya belum ada data Google)
    $affected = $pdo->exec("
        UPDATE destinations
        SET rating = NULL, card_rating = NULL, total_ulasan = NULL
        WHERE google_place_id IS NULL
    ");
    echo "✅ {$affected} destinasi di-set NULL (belum ada Google Place ID).\n";

    // 3. Verifikasi
    echo "\n=== Verifikasi kolom ===\n";
    foreach ($pdo->query("SHOW COLUMNS FROM destinations WHERE Field IN ('rating','card_rating','total_ulasan')")->fetchAll() as $c) {
        echo "  {$c['Field']}: Type={$c['Type']} Null={$c['Null']} Default=" . ($c['Default'] ?? 'NULL') . "\n";
    }

    $nullCount = (int)$pdo->query("SELECT COUNT(*) FROM destinations WHERE rating IS NULL")->fetchColumn();
    echo "\n{$nullCount} destinasi dengan rating = NULL (belum ada data Google).\n";
    echo "\n✅ Migrasi selesai.\n";

} catch (PDOException $e) {
    echo "❌ Gagal: " . $e->getMessage() . "\n";
    exit(1);
}
