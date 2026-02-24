<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Approval Peminjaman';
$base_url = '../../';
include '../../includes/header.php';

// Proses approve/reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id_request = $_GET['id'];
    $id_admin = $_SESSION['user_id'];
    
    $request = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT rp.*, b.stok_tersedia, a.nama as nama_anggota
        FROM request_peminjaman rp
        JOIN buku b ON rp.id_buku = b.id_buku
        JOIN anggota a ON rp.id_anggota = a.id_anggota
        WHERE rp.id_request = $id_request
    "));
    
    if ($request) {
        if ($action == 'approve') {
            // Cek stok
            if ($request['stok_tersedia'] < 1) {
                $_SESSION['error'] = 'Stok buku tidak tersedia!';
            } else {
                // Buat transaksi
                $tanggal_pinjam = date('Y-m-d');
                $batas_kembali = date('Y-m-d', strtotime("+{$request['lama_pinjam']} days"));
                
                $sql = "INSERT INTO transaksi (id_anggota, id_buku, id_admin, tanggal_pinjam, batas_kembali, status) 
                        VALUES (?, ?, ?, ?, ?, 'dipinjam')";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "iiiss", $request['id_anggota'], $request['id_buku'], $id_admin, $tanggal_pinjam, $batas_kembali);
                
                if (mysqli_stmt_execute($stmt)) {
                    $id_transaksi = mysqli_insert_id($koneksi);
                    
                    // Update request
                    $update_sql = "UPDATE request_peminjaman 
                                   SET status = 'approved', id_admin = ?, tanggal_response = NOW(), id_transaksi = ?
                                   WHERE id_request = ?";
                    $update_stmt = mysqli_prepare($koneksi, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "iii", $id_admin, $id_transaksi, $id_request);
                    mysqli_stmt_execute($update_stmt);
                    
                    logAktivitas($koneksi, 'approve_peminjaman', 'Approve request peminjaman: ' . $request['nama_anggota'], $id_admin, null);
                    $_SESSION['success'] = 'Request peminjaman disetujui dan transaksi berhasil dibuat!';
                } else {
                    $_SESSION['error'] = 'Gagal membuat transaksi!';
                }
            }
        } elseif ($action == 'reject') {
            $alasan = $_POST['alasan_reject'] ?? 'Tidak ada alasan';
            
            $sql = "UPDATE request_peminjaman 
                    SET status = 'rejected', id_admin = ?, tanggal_response = NOW(), alasan_reject = ?
                    WHERE id_request = ?";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $id_admin, $alasan, $id_request);
            
            if (mysqli_stmt_execute($stmt)) {
                logAktivitas($koneksi, 'reject_peminjaman', 'Reject request peminjaman: ' . $request['nama_anggota'], $id_admin, null);
                $_SESSION['success'] = 'Request peminjaman ditolak!';
            } else {
                $_SESSION['error'] = 'Gagal menolak request!';
            }
        }
    }
    
    redirect('approval-pinjam.php');
}

// Ambil request pending
$requests = mysqli_query($koneksi, "
    SELECT rp.*, 
           a.nama, a.nis_nisn, a.kelas_jurusan,
           b.judul, b.pengarang, b.isbn, b.stok_tersedia, b.cover_path
    FROM request_peminjaman rp
    JOIN anggota a ON rp.id_anggota = a.id_anggota
    JOIN buku b ON rp.id_buku = b.id_buku
    WHERE rp.status = 'pending'
    ORDER BY rp.tanggal_request ASC
");

$total_pending = mysqli_num_rows($requests);
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Approval Peminjaman</h2>
            <span class="badge bg-warning fs-6"><?php echo $total_pending; ?> Request Pending</span>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <?php if ($total_pending > 0): ?>
                    <div class="row">
                        <?php while ($r = mysqli_fetch_assoc($requests)): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <?php if ($r['cover_path']): ?>
                                                <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $r['cover_path']; ?>" 
                                                     class="img-fluid" alt="Cover">
                                            <?php else: ?>
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="height: 120px;">No Cover</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-9">
                                            <h6 class="card-title"><?php echo $r['judul']; ?></h6>
                                            <p class="mb-1"><small class="text-muted"><?php echo $r['pengarang']; ?></small></p>
                                            
                                            <hr>
                                            
                                            <p class="mb-1"><strong>Peminjam:</strong> <?php echo $r['nama']; ?></p>
                                            <p class="mb-1"><small>NIS: <?php echo $r['nis_nisn'] ?? '-'; ?> | Kelas: <?php echo $r['kelas_jurusan'] ?? '-'; ?></small></p>
                                            <p class="mb-1"><strong>Lama Pinjam:</strong> <?php echo $r['lama_pinjam']; ?> hari</p>
                                            <p class="mb-1"><strong>Request:</strong> <?php echo date('d/m/Y H:i', strtotime($r['tanggal_request'])); ?></p>
                                            
                                            <?php if ($r['keterangan']): ?>
                                                <p class="mb-2"><small><strong>Keterangan:</strong> <?php echo $r['keterangan']; ?></small></p>
                                            <?php endif; ?>
                                            
                                            <p class="mb-2">
                                                <span class="badge bg-<?php echo $r['stok_tersedia'] > 0 ? 'success' : 'danger'; ?>">
                                                    Stok: <?php echo $r['stok_tersedia']; ?>
                                                </span>
                                            </p>
                                            
                                            <div class="d-flex gap-2 mt-2">
                                                <?php if ($r['stok_tersedia'] > 0): ?>
                                                    <a href="?action=approve&id=<?php echo $r['id_request']; ?>" 
                                                       class="btn btn-success btn-sm"
                                                       onclick="return confirm('Setujui request peminjaman ini?')">
                                                        ✓ Setujui
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?php echo $r['id_request']; ?>">
                                                    ✗ Tolak
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Reject -->
                        <div class="modal fade" id="rejectModal<?php echo $r['id_request']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tolak Request Peminjaman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="?action=reject&id=<?php echo $r['id_request']; ?>" method="POST">
                                        <div class="modal-body">
                                            <p>Anda akan menolak request dari <strong><?php echo $r['nama']; ?></strong></p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan:</label>
                                                <textarea name="alasan_reject" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak Request</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <h5>Tidak ada request peminjaman pending</h5>
                        <p>Semua request sudah diproses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>