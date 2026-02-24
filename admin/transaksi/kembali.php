<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Pengembalian Buku';
$base_url = '../../';
include '../../includes/header.php';

// Proses pengembalian
if (isset($_GET['kembalikan'])) {
    $id_transaksi = $_GET['kembalikan'];
    $tanggal_kembali = date('Y-m-d');
    
    // Ambil data transaksi
    $trans = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = $id_transaksi"));
    
    // Hitung denda jika terlambat
    $denda = hitungDenda($tanggal_kembali, $trans['batas_kembali']);
    
    // Update status transaksi
    $sql = "UPDATE transaksi SET tanggal_kembali = ?, denda = ?, status = 'dikembalikan' WHERE id_transaksi = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sdi", $tanggal_kembali, $denda, $id_transaksi);
    
    if (mysqli_stmt_execute($stmt)) {
        // Stok akan otomatis bertambah karena trigger
        logAktivitas($koneksi, 'pengembalian', 'Pengembalian buku ID transaksi: ' . $id_transaksi, $_SESSION['user_id'], null);
        
        if ($denda > 0) {
            $_SESSION['success'] = 'Buku berhasil dikembalikan! Denda keterlambatan: Rp ' . number_format($denda, 0, ',', '.');
        } else {
            $_SESSION['success'] = 'Buku berhasil dikembalikan!';
        }
        redirect('kembali.php');
    } else {
        $error = 'Gagal memproses pengembalian!';
    }
}

// Ambil transaksi yang belum dikembalikan
$transaksi = mysqli_query($koneksi, "
    SELECT t.*, a.nama as nama_anggota, a.nis_nisn, b.judul as judul_buku, b.isbn
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.status = 'dipinjam'
    ORDER BY t.batas_kembali ASC
");
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Pengembalian Buku</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Buku yang Dipinjam</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>ISBN</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($t = mysqli_fetch_assoc($transaksi)): 
                                $terlambat = strtotime($t['batas_kembali']) < time();
                                $hari_terlambat = 0;
                                $denda_preview = 0;
                                
                                if ($terlambat) {
                                    $date1 = new DateTime($t['batas_kembali']);
                                    $date2 = new DateTime();
                                    $diff = $date1->diff($date2);
                                    $hari_terlambat = $diff->days;
                                    $denda_preview = $hari_terlambat * 1000;
                                }
                            ?>
                            <tr class="<?php echo $terlambat ? 'table-danger' : ''; ?>">
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo $t['nama_anggota']; ?></strong><br>
                                    <small class="text-muted"><?php echo $t['nis_nisn'] ?? '-'; ?></small>
                                </td>
                                <td><?php echo $t['judul_buku']; ?></td>
                                <td><?php echo $t['isbn']; ?></td>
                                <td><?php echo formatTanggal($t['tanggal_pinjam']); ?></td>
                                <td>
                                    <?php echo formatTanggal($t['batas_kembali']); ?>
                                    <?php if ($terlambat): ?>
                                        <br><span class="badge bg-danger"><?php echo $hari_terlambat; ?> hari terlambat</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($denda_preview > 0): ?>
                                        <strong class="text-danger">Rp <?php echo number_format($denda_preview, 0, ',', '.'); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">Rp 0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?kembalikan=<?php echo $t['id_transaksi']; ?>" 
                                       class="btn btn-sm btn-success"
                                       onclick="return confirm('Proses pengembalian buku ini?')">
                                        Kembalikan
                                    </a>
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

<?php include '../../includes/footer.php'; ?>