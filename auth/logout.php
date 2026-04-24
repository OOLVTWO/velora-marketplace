<?php
/**
 * VELORA — Logout
 * Developer: Felysia
 * Menghapus semua session dan redirect ke landing page.
 */
session_start();

// Hapus semua data session
$_SESSION = [];

// Hapus session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

// Redirect ke landing page dengan pesan
header('Location: ../index.php?msg=logout');
exit;
