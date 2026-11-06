<?php
/**
 * JelajahNusa - Backend Logout Pengguna
 * Lokasi: auth/logout.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan array session
$_SESSION = [];

// 2. Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Hancurkan session di server
session_destroy();

// 4. Tangani respons AJAX vs Redirect biasa
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Anda berhasil keluar dari sistem.']);
    exit;
}

// Redirect aman ke halaman sebelumnya atau beranda
$redirect = $_GET['redirect'] ?? '../index.php';

// Pastikan redirect hanya menuju rute lokal (mencegah open redirect)
$cleanRedirect = (strpos($redirect, 'http://') === 0 || strpos($redirect, 'https://') === 0 || strpos($redirect, '//') === 0)
    ? '../index.php'
    : $redirect;

header('Location: ' . $cleanRedirect);
exit;
