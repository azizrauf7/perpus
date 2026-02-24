<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

// Auth
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$q = isset($_GET['q']) ? clean($_GET['q']) : '';

$where = '';
if ($q !== '') {
    $where = "WHERE (a.nama LIKE '%" . $q . "%' OR b.judul LIKE '%" . $q . "%' OR t.status LIKE '%" . $q . "%')";
}

$sql = "
    SELECT t.*, a.nama as nama_anggota, b.judul as judul_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    $where
    ORDER BY t.created_at DESC
";

$res = mysqli_query($koneksi, $sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=transaksi_export_' . date('Ymd') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Tanggal Pinjam', 'Anggota', 'Buku', 'Batas Kembali', 'Status']);

while ($row = mysqli_fetch_assoc($res)) {
    fputcsv($output, [
        formatTanggal($row['tanggal_pinjam']),
        $row['nama_anggota'],
        $row['judul_buku'],
        formatTanggal($row['batas_kembali']),
        $row['status']
    ]);
}

fclose($output);
exit();

?>
