<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

// Cek login dan role
if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Dashboard Siswa';
$base_url = '../';
include '../includes/header.php';

// Ambil data siswa yang login
$id_anggota = $_SESSION['user_id'];

// Ambil statistik personal siswa
$total_sedang_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM transaksi 
    WHERE id_anggota = '$id_anggota' AND status = 'dipinjam'
"))['total'];

$total_sudah_kembali = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM transaksi 
    WHERE id_anggota = '$id_anggota' AND status = 'dikembalikan'
"))['total'];

$total_terlambat = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM transaksi 
    WHERE id_anggota = '$id_anggota' AND status = 'dipinjam' AND batas_kembali < CURDATE()
"))['total'];

// Ambil total denda
$total_denda = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COALESCE(SUM(denda), 0) as total FROM transaksi 
    WHERE id_anggota = '$id_anggota'
"))['total'];

// Ambil buku yang sedang dipinjam oleh siswa
$buku_sedang_pinjam = mysqli_query($koneksi, "
    SELECT t.*, b.judul as judul_buku, b.pengarang as pengarang_buku
    FROM transaksi t
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.id_anggota = '$id_anggota' AND t.status = 'dipinjam'
    ORDER BY t.batas_kembali ASC
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Dashboard Siswa</h2>
        
        <!-- Statistics Cards - Personal -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Sedang Dipinjam</h5>
                        <h2 class="mb-0"><?php echo $total_sedang_pinjam; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Sudah Dikembalikan</h5>
                        <h2 class="mb-0"><?php echo $total_sudah_kembali; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Terlambat</h5>
                        <h2 class="mb-0"><?php echo $total_terlambat; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Denda</h5>
                        <h2 class="mb-0">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Buku Sedang Dipinjam -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Buku Sedang Saya Pinjam</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($buku_sedang_pinjam) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($buku_sedang_pinjam)): ?>
                            <tr>
                                <td><?php echo $row['judul_buku']; ?></td>
                                <td><?php echo $row['pengarang_buku']; ?></td>
                                <td><?php echo formatTanggal($row['tanggal_pinjam']); ?></td>
                                <td><?php echo formatTanggal($row['batas_kembali']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'warning';
                                    $status_text = 'Dipinjam';
                                    if (strtotime($row['batas_kembali']) < time()) {
                                        $badge_class = 'danger';
                                        $status_text = 'TERLAMBAT';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Anda tidak memiliki peminjaman buku saat ini.</p>
                <a href="buku.php" class="btn btn-primary btn-sm">Lihat Katalog Buku</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>