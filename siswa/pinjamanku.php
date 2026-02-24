<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Pinjamanku';
$base_url = '../';
include '../includes/header.php';

$id_anggota = $_SESSION['user_id'];

// Ambil riwayat peminjaman
$transaksi = mysqli_query($koneksi, "
    SELECT t.*, b.judul, b.pengarang, b.cover_path, b.isbn
    FROM transaksi t
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.id_anggota = $id_anggota
    ORDER BY t.created_at DESC
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Riwayat Peminjaman Saya</h2>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Tgl Kembali</th>
                                <th>Denda</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($t = mysqli_fetch_assoc($transaksi)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <?php if ($t['cover_path']): ?>
                                                <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $t['cover_path']; ?>" 
                                                     alt="Cover" style="width: 40px; height: 55px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 55px; font-size: 8px;">No Cover</div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo $t['judul']; ?></strong><br>
                                            <small class="text-muted"><?php echo $t['pengarang']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo formatTanggal($t['tanggal_pinjam']); ?></td>
                                <td>
                                    <?php 
                                    $terlambat = $t['status'] == 'dipinjam' && strtotime($t['batas_kembali']) < time();
                                    ?>
                                    <span class="<?php echo $terlambat ? 'text-danger fw-bold' : ''; ?>">
                                        <?php echo formatTanggal($t['batas_kembali']); ?>
                                    </span>
                                </td>
                                <td><?php echo $t['tanggal_kembali'] ? formatTanggal($t['tanggal_kembali']) : '-'; ?></td>
                                <td>
                                    <?php if ($t['denda'] > 0): ?>
                                        <span class="text-danger fw-bold">Rp <?php echo number_format($t['denda'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Rp 0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = 'secondary';
                                    if ($t['status'] == 'dipinjam') {
                                        $badge_class = $terlambat ? 'danger' : 'warning';
                                    } elseif ($t['status'] == 'dikembalikan') {
                                        $badge_class = 'success';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>">
                                        <?php echo $terlambat && $t['status'] == 'dipinjam' ? 'Terlambat' : ucfirst($t['status']); ?>
                                    </span>
                                    <?php if ($t['status'] == 'dipinjam'): ?>
                                        <br>
                                        <a href="request-kembali.php?id=<?php echo $t['id_transaksi']; ?>" 
                                           class="btn btn-sm btn-success mt-1">
                                            Ajukan Pengembalian
                                        </a>
                                    <?php endif; ?>
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

<?php include '../includes/footer.php'; ?>