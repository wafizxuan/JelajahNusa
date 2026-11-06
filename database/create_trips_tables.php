<?php
/**
 * JelajahNusa - Migrasi: Buat Tabel trips dan trip_items
 * Lokasi: database/create_trips_tables.php
 * Jalankan sekali via CLI: php database/create_trips_tables.php
 *
 * SQL yang dieksekusi:
 * -------------------------------------------------
 * CREATE TABLE trips (
 *   id          INT             NOT NULL AUTO_INCREMENT,
 *   user_id     INT             NOT NULL,
 *   name        VARCHAR(150)    NOT NULL,
 *   destination VARCHAR(200)    NOT NULL,
 *   start_date  DATE            NOT NULL,
 *   end_date    DATE            NOT NULL,
 *   category    VARCHAR(80)     NOT NULL DEFAULT 'Pasangan & Keluarga',
 *   notes       TEXT            NULL,
 *   days        SMALLINT        NOT NULL DEFAULT 1,
 *   created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *   PRIMARY KEY (id),
 *   KEY idx_trips_user (user_id),
 *   CONSTRAINT fk_trips_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
 * )
 *
 * CREATE TABLE trip_items (
 *   id          INT             NOT NULL AUTO_INCREMENT,
 *   trip_id     INT             NOT NULL,
 *   user_id     INT             NOT NULL,
 *   day_number  SMALLINT        NOT NULL DEFAULT 1,
 *   category    VARCHAR(60)     NOT NULL DEFAULT 'Destinasi',
 *   name        VARCHAR(200)    NOT NULL,
 *   location    VARCHAR(200)    NULL,
 *   time_visit  VARCHAR(20)     NULL,
 *   photo_url   VARCHAR(500)    NULL,
 *   note        TEXT            NULL,
 *   sort_order  SMALLINT        NOT NULL DEFAULT 0,
 *   created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   PRIMARY KEY (id),
 *   KEY idx_items_trip (trip_id),
 *   KEY idx_items_user (user_id),
 *   CONSTRAINT fk_items_trip FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
 *   CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
 * )
 */

$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? 'cli', ['127.0.0.1', '::1', 'cli']);
if (!$isLocalhost && PHP_SAPI !== 'cli') {
    http_response_code(403);
    die("Akses ditolak.\n");
}

require_once __DIR__ . '/../config/database.php';

$statements = [];

// ─── Tabel trips ───────────────────────────────────────────────────────────
$statements[] = "
CREATE TABLE IF NOT EXISTS trips (
    id          INT             NOT NULL AUTO_INCREMENT,
    user_id     INT             NOT NULL,
    name        VARCHAR(150)    NOT NULL,
    destination VARCHAR(200)    NOT NULL,
    start_date  DATE            NOT NULL,
    end_date    DATE            NOT NULL,
    category    VARCHAR(80)     NOT NULL DEFAULT 'Pasangan & Keluarga',
    notes       TEXT            NULL,
    days        SMALLINT        NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trips_user (user_id),
    CONSTRAINT fk_trips_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// ─── Tabel trip_items ──────────────────────────────────────────────────────
$statements[] = "
CREATE TABLE IF NOT EXISTS trip_items (
    id          INT             NOT NULL AUTO_INCREMENT,
    trip_id     INT             NOT NULL,
    user_id     INT             NOT NULL,
    day_number  SMALLINT        NOT NULL DEFAULT 1,
    category    VARCHAR(60)     NOT NULL DEFAULT 'Destinasi',
    name        VARCHAR(200)    NOT NULL,
    location    VARCHAR(200)    NULL,
    time_visit  VARCHAR(20)     NULL,
    photo_url   VARCHAR(500)    NULL,
    note        TEXT            NULL,
    sort_order  SMALLINT        NOT NULL DEFAULT 0,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_items_trip (trip_id),
    KEY idx_items_user (user_id),
    CONSTRAINT fk_items_trip
        FOREIGN KEY (trip_id) REFERENCES trips(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_items_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// ─── Eksekusi ──────────────────────────────────────────────────────────────
$pass = 0;
$fail = 0;
$tableNames = ['trips', 'trip_items'];

foreach ($statements as $i => $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ Tabel '{$tableNames[$i]}' berhasil dibuat (atau sudah ada).\n";
        $pass++;
    } catch (PDOException $e) {
        echo "❌ Gagal membuat tabel '{$tableNames[$i]}': " . $e->getMessage() . "\n";
        $fail++;
    }
}

// ─── Verifikasi kolom ──────────────────────────────────────────────────────
echo "\n=== Verifikasi Struktur ===\n";
foreach ($tableNames as $tbl) {
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM {$tbl}")->fetchAll();
        echo "  {$tbl}: " . implode(', ', array_column($cols, 'Field')) . "\n";
    } catch (PDOException $e) {
        echo "  {$tbl}: TIDAK DITEMUKAN\n";
    }
}

echo "\n--- Selesai: {$pass} berhasil, {$fail} gagal ---\n";
