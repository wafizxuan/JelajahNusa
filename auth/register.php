<?php
/**
 * JelajahNusa - Backend Registrasi Pengguna
 * Lokasi: auth/register.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Ambil input data dari $_POST atau JSON payload
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$terms    = isset($_POST['terms']);

// Validasi Nama
if (empty($name)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nama lengkap wajib diisi.']);
    exit;
}
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nama lengkap harus antara 2 hingga 100 karakter.']);
    exit;
}

// Validasi Email
if (empty($email)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Alamat email wajib diisi.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Format alamat email tidak valid.']);
    exit;
}

// Validasi Password
if (empty($password)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Kata sandi wajib diisi.']);
    exit;
}
if (strlen($password) < 8) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Kata sandi minimal terdiri dari 8 karakter.']);
    exit;
}

try {
    // 1. Periksa apakah email sudah terdaftar
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $checkStmt->execute(['email' => $email]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.'
        ]);
        exit;
    }

    // 2. Hash password dengan algoritma aman (Bcrypt / Argon2 default PHP)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan pengguna baru ke tabel users
    $insertStmt = $pdo->prepare('
        INSERT INTO users (name, email, password, role)
        VALUES (:name, :email, :password, :role)
    ');
    $insertStmt->execute([
        'name'     => $name,
        'email'    => $email,
        'password' => $hashedPassword,
        'role'     => 'user'
    ]);

    $newUserId = (int)$pdo->lastInsertId();

    // 4. Regenerasi ID session untuk mencegah session fixation
    session_regenerate_id(true);

    // 5. Simpan data user ke session (tanpa menyimpan password)
    $_SESSION['user'] = [
        'id'    => $newUserId,
        'name'  => $name,
        'email' => $email,
        'role'  => 'user'
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil! Selamat datang di JelajahNusa, ' . $name . '.',
        'user'    => [
            'id'    => $newUserId,
            'name'  => $name,
            'email' => $email
        ]
    ]);
} catch (PDOException $e) {
    error_log('[Auth Register Error] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem saat memproses pendaftaran. Silakan coba beberapa saat lagi.'
    ]);
}
