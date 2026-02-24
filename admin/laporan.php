<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$title = 'Laporan';
$base_url = '../';
include '../includes/header.php';

// Default filter bulan ini
$bulan = $_GET['bulan'] ?? date('Y-m');

// Statistik Bulan Ini
$stats = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(CASE WHEN status = 'dipinjam' THEN 1 ELSE 0 END) as masih_dipinjam,
        SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) as sudah_dikembalikan,
        SUM(denda) as total_denda
    FROM transaksi
    WHERE DATE_FORMAT(tanggal_pinjam, '%Y-%m') = '$bulan'
"));

// Buku Terpopuler
$buku_populer = mysqli_query($koneksi, "
    SELECT b.judul, b.pengarang, COUNT(t.id_transaksi) as jumlah_pinjam
    FROM transaksi t
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE DATE_FORMAT(t.tanggal_pinjam, '%Y-%m') = '$bulan'
    GROUP BY t.id_buku
    ORDER BY jumlah_pinjam DESC
    LIMIT 10
");

// Anggota Teraktif
$anggota_aktif = mysqli_query($koneksi, "
    SELECT a.nama, a.nis_nisn, COUNT(t.id_transaksi) as jumlah_pinjam
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    WHERE DATE_FORMAT(t.tanggal_pinjam, '%Y-%m') = '$bulan'
    GROUP BY t.id_anggota
    ORDER BY jumlah_pinjam DESC
    LIMIT 10
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Laporan Perpustakaan</h2>
        
        <!-- Filter Bulan -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <form method="GET">
                            <label class="form-label">Pilih Bulan</label>
                            <input type="month" name="bulan" class="form-control" value="<?php echo $bulan; ?>">
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary">Tampilkan</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        <form method="GET" action="export-laporan.php">
                            <label class="form-label">Export Laporan Per Hari</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <div class="mt-2">
                                <button type="submit" class="btn btn-outline-dark">Export CSV (Harian)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Transaksi</h6>
                        <h3 class="mb-0"><?php echo $stats['total_transaksi']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Masih Dipinjam</h6>
                        <h3 class="mb-0"><?php echo $stats['masih_dipinjam']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Sudah Dikembalikan</h6>
                        <h3 class="mb-0"><?php echo $stats['sudah_dikembalikan']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Total Denda</h6>
                        <h3 class="mb-0">Rp <?php echo number_format($stats['total_denda'], 0, ',', '.'); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Buku Terpopuler -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Buku Terpopuler</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Buku</th>
                                        <th>Pengarang</th>
                                        <th class="text-center">Pinjaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($bp = mysqli_fetch_assoc($buku_populer)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $bp['judul']; ?></td>
                                        <td><?php echo $bp['pengarang']; ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?php echo $bp['jumlah_pinjam']; ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Anggota Teraktif -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Anggota Teraktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>NIS/NISN</th>
                                        <th class="text-center">Pinjaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($aa = mysqli_fetch_assoc($anggota_aktif)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $aa['nama']; ?></td>
                                        <td><?php echo $aa['nis_nisn'] ?? '-'; ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo $aa['jumlah_pinjam']; ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>