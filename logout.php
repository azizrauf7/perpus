<?php
session_start();
require_once 'koneksi.php';
require_once 'includes/fungsi.php';

// Log aktivitas sebelum logout
if (isset($_SESSION['user_id'])) {
    if (isAdmin()) {
        logAktivitas($koneksi, 'logout', 'Admin logout: ' . $_SESSION['username'], $_SESSION['user_id'], null);
    } else {
        logAktivitas($koneksi, 'logout', 'Siswa logout: ' . $_SESSION['username'], null, $_SESSION['user_id']);
    }
}

// Destroy session
session_destroy();
redirect('index.php');
?>