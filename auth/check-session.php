<?php
/**
 * JelajahNusa - Cek Status Sesi Pengguna
 * Lokasi: auth/check-session.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
    echo json_encode([
        'logged_in' => true,
        'user'      => [
            'id'    => (int)$_SESSION['user']['id'],
            'name'  => htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'),
            'role'  => $_SESSION['user']['role'] ?? 'user'
        ]
    ]);
} else {
    echo json_encode([
        'logged_in' => false,
        'user'      => null
    ]);
}
