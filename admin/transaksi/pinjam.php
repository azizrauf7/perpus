<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Peminjaman Buku';
$base_url = '../../';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = clean($_POST['id_anggota']);
    $id_buku = clean($_POST['id_buku']);
    $id_admin = $_SESSION['user_id'];
    $lama_pinjam = clean($_POST['lama_pinjam']) ?? 7;
    
    $tanggal_pinjam = date('Y-m-d');
    $batas_kembali = date('Y-m-d', strtotime("+$lama_pinjam days"));
    
    // Cek stok buku
    $buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku"));
    if ($buku['stok_tersedia'] < 1) {
        $error = 'Stok buku tidak tersedia!';
    } else {
        // Cek apakah anggota sudah pinjam buku yang sama dan belum dikembalikan
        $cek = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_anggota = $id_anggota AND id_buku = $id_buku AND status = 'dipinjam'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Anggota masih meminjam buku yang sama!';
        } else {
            $sql = "INSERT INTO transaksi (id_anggota, id_buku, id_admin, tanggal_pinjam, batas_kembali, status) 
                    VALUES (?, ?, ?, ?, ?, 'dipinjam')";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "iiiss", $id_anggota, $id_buku, $id_admin, $tanggal_pinjam, $batas_kembali);
            
            if (mysqli_stmt_execute($stmt)) {
                // Stok akan otomatis berkurang karena trigger
                logAktivitas($koneksi, 'peminjaman', 'Peminjaman buku oleh anggota ID: ' . $id_anggota, $_SESSION['user_id'], null);
                $_SESSION['success'] = 'Peminjaman berhasil dicatat!';
                redirect('pinjam.php');
            } else {
                $error = 'Gagal mencatat peminjaman!';
            }
        }
    }
}

// Ambil data anggota aktif
$anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE status_aktif = 1 ORDER BY nama ASC");

// Ambil data buku yang tersedia
$buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok_tersedia > 0 ORDER BY judul ASC");

// Ambil transaksi peminjaman aktif
$transaksi = mysqli_query($koneksi, "
    SELECT t.*, a.nama as nama_anggota, a.nis_nisn, b.judul as judul_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.status = 'dipinjam'
    ORDER BY t.created_at DESC
");
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Peminjaman Buku</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Form Peminjaman -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Form Peminjaman Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pilih Anggota <span class="text-danger">*</span></label>
                            <select name="id_anggota" class="form-select" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php 
                                mysqli_data_seek($anggota, 0);
                                while ($a = mysqli_fetch_assoc($anggota)): 
                                ?>
                                    <option value="<?php echo $a['id_anggota']; ?>">
                                        <?php echo $a['nama'] . ' (' . ($a['nis_nisn'] ?? 'N/A') . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pilih Buku <span class="text-danger">*</span></label>
                            <select name="id_buku" class="form-select" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php 
                                mysqli_data_seek($buku, 0);
                                while ($b = mysqli_fetch_assoc($buku)): 
                                ?>
                                    <option value="<?php echo $b['id_buku']; ?>">
                                        <?php echo $b['judul'] . ' (Stok: ' . $b['stok_tersedia'] . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Lama Pinjam (Hari)</label>
                            <input type="number" name="lama_pinjam" class="form-control" value="7" min="1" max="14">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Pinjam</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Daftar Peminjaman Aktif -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Peminjaman Aktif</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($t = mysqli_fetch_assoc($transaksi)): 
                                $terlambat = strtotime($t['batas_kembali']) < time();
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php echo $t['nama_anggota']; ?><br>
                                    <small class="text-muted"><?php echo $t['nis_nisn'] ?? '-'; ?></small>
                                </td>
                                <td><?php echo $t['judul_buku']; ?></td>
                                <td><?php echo formatTanggal($t['tanggal_pinjam']); ?></td>
                                <td><?php echo formatTanggal($t['batas_kembali']); ?></td>
                                <td>
                                    <?php if ($terlambat): ?>
                                        <span class="badge bg-danger">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Dipinjam</span>
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

<?php include '../../includes/footer.php'; ?>