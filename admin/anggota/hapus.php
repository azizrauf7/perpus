<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$id_anggota = $_GET['id'] ?? 0;

// Cek apakah anggota masih ada pinjaman aktif
$cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE id_anggota = $id_anggota AND status = 'dipinjam'");
$row = mysqli_fetch_assoc($cek);

if ($row['total'] > 0) {
    $_SESSION['error'] = 'Anggota tidak bisa dihapus karena masih memiliki pinjaman aktif!';
    redirect('index.php');
}

$anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id_anggota"));

if ($anggota) {
    if (mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota = $id_anggota")) {
        logAktivitas($koneksi, 'hapus_anggota', 'Menghapus anggota: ' . $anggota['nama'], $_SESSION['user_id'], null);
        $_SESSION['success'] = 'Anggota berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus anggota!';
    }
} else {
    $_SESSION['error'] = 'Anggota tidak ditemukan!';
}

redirect('index.php');
?>