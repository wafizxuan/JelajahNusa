<?php
/**
 * JelajahNusa - Konfigurasi Koneksi Database MySQL (PDO)
 * Lokasi: config/database.php
 */

// Konfigurasi Kredensial Database Laragon
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'jelajahnusa_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Mendapatkan instance koneksi PDO tunggal
 *
 * @return PDO
 */
function getDbConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Catat detail kesalahan teknis ke error log tanpa membocorkannya ke publik
            error_log('[Database Error] ' . $e->getMessage());

            // Tampilkan pesan ramah pengguna yang aman
            http_response_code(500);
            die('Layanan sistem database saat ini sedang tidak dapat diakses. Silakan coba beberapa saat lagi.');
        }
    }

    return $pdo;
}

// Inisialisasi variabel $pdo agar dapat langsung digunakan oleh skrip PHP
$pdo = getDbConnection();
