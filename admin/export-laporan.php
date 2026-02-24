<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$date = isset($_GET['date']) ? clean($_GET['date']) : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    die('Tanggal tidak valid');
}

$sql = "
    SELECT t.*, a.nama as nama_anggota, a.nis_nisn, a.kelas_jurusan, a.nohp as anggota_nohp,
           b.judul as judul_buku, b.pengarang, b.isbn, b.lokasi_rak,
           ad.nama_lengkap as nama_admin, ad.email as admin_email
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    LEFT JOIN admin ad ON t.id_admin = ad.id_admin
    WHERE DATE(t.tanggal_pinjam) = '$date' OR DATE(t.tanggal_kembali) = '$date'
    ORDER BY t.tanggal_pinjam ASC
";

$res = mysqli_query($koneksi, $sql);
if (!$res) {
    error_log('export-laporan.php SQL error: ' . mysqli_error($koneksi));
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Query failed. Check server error log.';
    exit();
}

// Output as Excel-compatible HTML table (.xls)
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_' . $date . '.xls');
echo "\xEF\xBB\xBF"; // UTF-8 BOM

echo "<table border='1'>";
echo "<thead><tr>";
echo "<th>ID Transaksi</th><th>Tanggal Pinjam</th><th>Batas Kembali</th><th>Tanggal Kembali</th><th>Status</th><th>Denda</th>";
echo "<th>ID Anggota</th><th>Nama Anggota</th><th>NIS/NISN</th><th>Kelas</th><th>No HP</th>";
echo "<th>ID Buku</th><th>Judul Buku</th><th>Pengarang</th><th>ISBN</th><th>Lokasi Rak</th>";
echo "<th>ID Admin</th><th>Nama Admin</th><th>Admin Email</th>";
echo "</tr></thead><tbody>";

while ($r = mysqli_fetch_assoc($res)) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($r['id_transaksi']) . '</td>';
    echo '<td>' . htmlspecialchars($r['tanggal_pinjam']) . '</td>';
    echo '<td>' . htmlspecialchars($r['batas_kembali']) . '</td>';
    echo '<td>' . htmlspecialchars($r['tanggal_kembali']) . '</td>';
    echo '<td>' . htmlspecialchars($r['status']) . '</td>';
    echo '<td>' . htmlspecialchars($r['denda']) . '</td>';

    echo '<td>' . htmlspecialchars($r['id_anggota']) . '</td>';
    echo '<td>' . htmlspecialchars($r['nama_anggota']) . '</td>';
    echo '<td>' . htmlspecialchars($r['nis_nisn']) . '</td>';
    echo '<td>' . htmlspecialchars($r['kelas_jurusan']) . '</td>';
    echo '<td>' . htmlspecialchars($r['anggota_nohp']) . '</td>';

    echo '<td>' . htmlspecialchars($r['id_buku']) . '</td>';
    echo '<td>' . htmlspecialchars($r['judul_buku']) . '</td>';
    echo '<td>' . htmlspecialchars($r['pengarang']) . '</td>';
    echo '<td>' . htmlspecialchars($r['isbn']) . '</td>';
    echo '<td>' . htmlspecialchars($r['lokasi_rak']) . '</td>';

    echo '<td>' . htmlspecialchars($r['id_admin']) . '</td>';
    echo '<td>' . htmlspecialchars($r['nama_admin']) . '</td>';
    echo '<td>' . htmlspecialchars($r['admin_email']) . '</td>';

    echo '</tr>';
}

echo '</tbody></table>';
exit();

?>
