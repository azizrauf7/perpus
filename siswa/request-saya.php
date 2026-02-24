<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Request Saya';
$base_url = '../';
include '../includes/header.php';

$id_anggota = $_SESSION['user_id'];

// Ambil request peminjaman
$request_pinjam = mysqli_query($koneksi, "
    SELECT rp.*, b.judul, b.pengarang, b.cover_path, b.isbn,
           a.nama_lengkap as nama_admin
    FROM request_peminjaman rp
    JOIN buku b ON rp.id_buku = b.id_buku
    LEFT JOIN admin a ON rp.id_admin = a.id_admin
    WHERE rp.id_anggota = $id_anggota
    ORDER BY rp.tanggal_request DESC
");

// Ambil request pengembalian
$request_kembali = mysqli_query($koneksi, "
    SELECT rk.*, t.tanggal_pinjam, t.batas_kembali,
           b.judul, b.pengarang, b.isbn,
           a.nama_lengkap as nama_admin
    FROM request_pengembalian rk
    JOIN transaksi t ON rk.id_transaksi = t.id_transaksi
    JOIN buku b ON t.id_buku = b.id_buku
    LEFT JOIN admin a ON rk.id_admin = a.id_admin
    WHERE rk.id_anggota = $id_anggota
    ORDER BY rk.tanggal_request DESC
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Request Saya</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Request Peminjaman -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📥 Request Peminjaman</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($request_pinjam) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Buku</th>
                                    <th>Lama Pinjam</th>
                                    <th>Status</th>
                                    <th>Respon Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rp = mysqli_fetch_assoc($request_pinjam)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($rp['tanggal_request'])); ?></td>
                                    <td>
                                        <strong><?php echo $rp['judul']; ?></strong><br>
                                        <small class="text-muted"><?php echo $rp['pengarang']; ?></small>
                                    </td>
                                    <td><?php echo $rp['lama_pinjam']; ?> hari</td>
                                    <td>
                                        <?php
                                        $badge_class = 'warning';
                                        $text = 'Pending';
                                        if ($rp['status'] == 'approved') {
                                            $badge_class = 'success';
                                            $text = 'Disetujui';
                                        } elseif ($rp['status'] == 'rejected') {
                                            $badge_class = 'danger';
                                            $text = 'Ditolak';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $text; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($rp['status'] != 'pending'): ?>
                                            <small>
                                                Admin: <?php echo $rp['nama_admin']; ?><br>
                                                Tanggal: <?php echo date('d/m/Y H:i', strtotime($rp['tanggal_response'])); ?>
                                                <?php if ($rp['status'] == 'rejected' && $rp['alasan_reject']): ?>
                                                    <br><strong>Alasan:</strong> <?php echo $rp['alasan_reject']; ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Menunggu...</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Belum ada request peminjaman</p>
                        <a href="buku.php" class="btn btn-primary">Lihat Katalog Buku</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Request Pengembalian -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">📤 Request Pengembalian</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($request_kembali) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Request</th>
                                    <th>Buku</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                    <th>Respon Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($request_kembali, 0);
                                while ($rk = mysqli_fetch_assoc($request_kembali)): 
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($rk['tanggal_request'])); ?></td>
                                    <td>
                                        <strong><?php echo $rk['judul']; ?></strong><br>
                                        <small class="text-muted"><?php echo $rk['pengarang']; ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $kondisi_badge = 'success';
                                        if ($rk['kondisi_buku'] == 'rusak_ringan') $kondisi_badge = 'warning';
                                        if ($rk['kondisi_buku'] == 'rusak_berat') $kondisi_badge = 'danger';
                                        if ($rk['kondisi_buku'] == 'hilang') $kondisi_badge = 'dark';
                                        ?>
                                        <span class="badge bg-<?php echo $kondisi_badge; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $rk['kondisi_buku'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = 'warning';
                                        $text = 'Pending';
                                        if ($rk['status'] == 'approved') {
                                            $badge_class = 'success';
                                            $text = 'Disetujui';
                                        } elseif ($rk['status'] == 'rejected') {
                                            $badge_class = 'danger';
                                            $text = 'Ditolak';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $text; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($rk['status'] != 'pending'): ?>
                                            <small>
                                                Admin: <?php echo $rk['nama_admin']; ?><br>
                                                Tanggal: <?php echo date('d/m/Y H:i', strtotime($rk['tanggal_response'])); ?>
                                                <?php if ($rk['status'] == 'rejected' && $rk['alasan_reject']): ?>
                                                    <br><strong>Alasan:</strong> <?php echo $rk['alasan_reject']; ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Menunggu...</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <p>Belum ada request pengembalian</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>