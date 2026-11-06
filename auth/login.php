<?php
/**
 * JelajahNusa - Backend Login Pengguna
 * Lokasi: auth/login.php
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

// Ambil input email dan password
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi kelengkapan input
if (empty($email) || empty($password)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Alamat email dan kata sandi wajib diisi.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Format alamat email tidak valid.']);
    exit;
}

try {
    // 1. Ambil data akun berdasarkan email menggunakan prepared statement
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // 2. Verifikasi keberadaan akun dan kecocokan hash password
    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.'
        ]);
        exit;
    }

    // 3. Regenerasi ID session untuk mencegah session fixation
    session_regenerate_id(true);

    // 4. Simpan identitas pengguna dalam session (tanpa menyimpan hash password)
    $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'] ?? 'user'
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Masuk berhasil! Selamat datang kembali, ' . $user['name'] . '.',
        'user'    => [
            'id'    => (int)$user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'] ?? 'user'
        ]
    ]);
} catch (PDOException $e) {
    error_log('[Auth Login Error] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem saat memproses login. Silakan coba beberapa saat lagi.'
    ]);
}
