<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$id_buku = $_GET['id'] ?? 0;

// Cek apakah buku sedang dipinjam
$cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE id_buku = $id_buku AND status = 'dipinjam'");
$row = mysqli_fetch_assoc($cek);

if ($row['total'] > 0) {
    $_SESSION['error'] = 'Buku tidak bisa dihapus karena masih ada yang meminjam!';
    redirect('index.php');
}

// Ambil data buku untuk hapus cover
$buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku"));

if ($buku) {
    // Hapus file cover jika ada
    if ($buku['cover_path'] && file_exists('../../assets/images/buku/' . $buku['cover_path'])) {
        unlink('../../assets/images/buku/' . $buku['cover_path']);
    }
    
    // Hapus data dari database
    if (mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku = $id_buku")) {
        logAktivitas($koneksi, 'hapus_buku', 'Menghapus buku: ' . $buku['judul'], $_SESSION['user_id'], null);
        $_SESSION['success'] = 'Buku berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus buku!';
    }
} else {
    $_SESSION['error'] = 'Buku tidak ditemukan!';
}

redirect('index.php');
?>