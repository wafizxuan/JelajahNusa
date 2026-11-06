<?php
/**
 * JelajahNusa - Seeder Migrasi Data Destinasi (Tahap 3C)
 * Lokasi: database/seed_destinations.php
 * 
 * Memindahkan 6 data destinasi dari:
 * 1. data/destinasi.php ($all_destinasi)
 * 2. index.php ($destinasi_wisata)
 * ke tabel MySQL `destinations` secara aman menggunakan transaksi PDO.
 */

// 1. Muat koneksi database aman (tanpa hardcode kredensial)
require_once __DIR__ . '/../config/database.php';

// 2. Muat data master detail
require_once __DIR__ . '/../data/destinasi.php';

if (!isset($all_destinasi) || !is_array($all_destinasi) || count($all_destinasi) !== 6) {
    die("[FATAL] Data master \$all_destinasi tidak valid atau tidak berjumlah 6 destinasi.\n");
}

// 3. Ekstrak data kartu homepage langsung dari index.php
$indexFile = __DIR__ . '/../index.php';
if (!file_exists($indexFile)) {
    die("[FATAL] File index.php tidak ditemukan.\n");
}

$indexContent = file_get_contents($indexFile);
if (!preg_match('/\$destinasi_wisata\s*=\s*\[(.*?)\];\s*\/\/\s*={10,}/s', $indexContent, $matches)) {
    die("[FATAL] Gagal mengekstrak array \$destinasi_wisata dari index.php.\n");
}

eval('$destinasi_wisata = [' . $matches[1] . '];');

if (!isset($destinasi_wisata) || !is_array($destinasi_wisata) || count($destinasi_wisata) !== 6) {
    die("[FATAL] Data kartu \$destinasi_wisata tidak valid atau tidak berjumlah 6 item.\n");
}

// Indeks kartu homepage berdasarkan ID
$cardByDestId = [];
foreach ($destinasi_wisata as $card) {
    $cardByDestId[$card['id']] = $card;
}

echo "=== MEMULAI PROSES SEEDING DATA DESTINASI ===\n";

try {
    // 4. Periksa apakah tabel destinations sudah berisi data
    $checkCount = (int)$pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    if ($checkCount > 0) {
        die("[BATAL] Tabel 'destinations' sudah memiliki $checkCount baris data. Migrasi dibatalkan untuk mencegah penimpaan data.\n");
    }

    // 5. Periksa integritas tabel users
    $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "[INFO] Tabel 'users' terverifikasi aman ($userCount baris data).\n";

    // 6. Mulai Transaksi PDO
    $pdo->beginTransaction();
    echo "[INFO] Transaksi database dimulai...\n";

    $sql = "INSERT INTO destinations (
        id, slug,
        nama, provinsi, wilayah, kategori, rating, total_ulasan, hero_image, tagline, deskripsi, about_heading,
        card_nama, card_lokasi, card_kategori, card_kategori_id, card_rating, card_image, card_deskripsi, card_tags, card_highlights,
        daya_tarik, waktu_terbaik, popular_places, recommended_stays, local_culinary, things_to_do, travel_info
    ) VALUES (
        :id, :slug,
        :nama, :provinsi, :wilayah, :kategori, :rating, :total_ulasan, :hero_image, :tagline, :deskripsi, :about_heading,
        :card_nama, :card_lokasi, :card_kategori, :card_kategori_id, :card_rating, :card_image, :card_deskripsi, :card_tags, :card_highlights,
        :daya_tarik, :waktu_terbaik, :popular_places, :recommended_stays, :local_culinary, :things_to_do, :travel_info
    )";

    $stmt = $pdo->prepare($sql);
    $jsonFlags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    for ($id = 1; $id <= 6; $id++) {
        if (!isset($all_destinasi[$id])) {
            throw new Exception("Destinasi ID $id tidak ditemukan di \$all_destinasi");
        }
        if (!isset($cardByDestId[$id])) {
            throw new Exception("Destinasi ID $id tidak ditemukan di \$destinasi_wisata");
        }

        $master = $all_destinasi[$id];
        $card   = $cardByDestId[$id];

        // Encode kolom-kolom JSON secara ketat
        $cardTagsJson          = json_encode($card['tags'], $jsonFlags);
        $dayaTarikJson         = json_encode($master['daya_tarik'], $jsonFlags);
        $waktuTerbaikJson      = json_encode($master['waktu_terbaik'], $jsonFlags);
        $popularPlacesJson     = json_encode($master['popular_places'], $jsonFlags);
        $recommendedStaysJson  = json_encode($master['recommended_stays'], $jsonFlags);
        $localCulinaryJson     = json_encode($master['local_culinary'], $jsonFlags);
        $thingsToDoJson        = json_encode($master['things_to_do'], $jsonFlags);
        $travelInfoJson        = json_encode($master['travel_info'], $jsonFlags);

        $params = [
            // Identitas
            ':id'                  => $id,
            ':slug'                => $master['slug'],

            // Master Detail
            ':nama'                => $master['nama'],
            ':provinsi'            => $master['provinsi'],
            ':wilayah'             => $master['wilayah'],
            ':kategori'            => $master['kategori'],
            ':rating'              => $master['rating'],
            ':total_ulasan'        => $master['total_ulasan'],
            ':hero_image'          => $master['hero_image'],
            ':tagline'             => $master['tagline'],
            ':deskripsi'           => $master['deskripsi'],
            ':about_heading'       => $master['about_heading'],

            // Card Homepage
            ':card_nama'           => $card['nama'],
            ':card_lokasi'         => $card['lokasi'],
            ':card_kategori'       => $card['kategori'],
            ':card_kategori_id'    => $card['kategori_id'],
            ':card_rating'         => $card['rating'],
            ':card_image'          => $card['foto'],
            ':card_deskripsi'      => $card['deskripsi'],
            ':card_tags'           => $cardTagsJson,
            ':card_highlights'     => $card['highlights'],

            // Nested Rich JSON
            ':daya_tarik'          => $dayaTarikJson,
            ':waktu_terbaik'       => $waktuTerbaikJson,
            ':popular_places'      => $popularPlacesJson,
            ':recommended_stays'   => $recommendedStaysJson,
            ':local_culinary'      => $localCulinaryJson,
            ':things_to_do'        => $thingsToDoJson,
            ':travel_info'         => $travelInfoJson,
        ];

        $stmt->execute($params);
        echo "  [OK] Destinasi #$id ({$master['nama']}) berhasil disisipkan.\n";
    }

    // 7. Commit transaksi jika semua baris sukses
    $pdo->commit();
    echo "[BERHASIL] Transaksi berhasil di-commit! Enam destinasi berhasil dimigrasikan ke MySQL.\n";

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
        echo "[ROLLBACK] Transaksi dibatalkan karena terjadi kesalahan.\n";
    }
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
