<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Approval Pengembalian';
$base_url = '../../';
include '../../includes/header.php';

// Proses approve/reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id_request = $_GET['id'];
    $id_admin = $_SESSION['user_id'];
    
    $request = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT rk.*, t.batas_kembali, t.id_buku,
               a.nama as nama_anggota,
               b.judul
        FROM request_pengembalian rk
        JOIN transaksi t ON rk.id_transaksi = t.id_transaksi
        JOIN anggota a ON rk.id_anggota = a.id_anggota
        JOIN buku b ON t.id_buku = b.id_buku
        WHERE rk.id_request = $id_request
    "));
    
    if ($request) {
        if ($action == 'approve') {
            $tanggal_kembali = date('Y-m-d');
            $denda = hitungDenda($tanggal_kembali, $request['batas_kembali']);
            
            // Tambah denda jika buku rusak atau hilang
            $denda_tambahan = 0;
            if ($request['kondisi_buku'] == 'rusak_ringan') {
                $denda_tambahan = 10000;
            } elseif ($request['kondisi_buku'] == 'rusak_berat') {
                $denda_tambahan = 50000;
            } elseif ($request['kondisi_buku'] == 'hilang') {
                $denda_tambahan = 100000;
            }
            
            $total_denda = $denda + $denda_tambahan;
            
            // Update transaksi
            $sql = "UPDATE transaksi 
                    SET tanggal_kembali = ?, denda = ?, status = 'dikembalikan', keterangan = ?
                    WHERE id_transaksi = ?";
            $keterangan = $request['kondisi_buku'] != 'baik' ? 'Kondisi: ' . str_replace('_', ' ', $request['kondisi_buku']) : null;
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "sdsi", $tanggal_kembali, $total_denda, $keterangan, $request['id_transaksi']);
            
            if (mysqli_stmt_execute($stmt)) {
                // Update request
                $update_sql = "UPDATE request_pengembalian 
                               SET status = 'approved', id_admin = ?, tanggal_response = NOW()
                               WHERE id_request = ?";
                $update_stmt = mysqli_prepare($koneksi, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "ii", $id_admin, $id_request);
                mysqli_stmt_execute($update_stmt);
                
                logAktivitas($koneksi, 'approve_pengembalian', 'Approve pengembalian: ' . $request['judul'], $id_admin, null);
                $_SESSION['success'] = 'Request pengembalian disetujui! Denda: Rp ' . number_format($total_denda, 0, ',', '.');
            } else {
                $_SESSION['error'] = 'Gagal memproses pengembalian!';
            }
        } elseif ($action == 'reject') {
            $alasan = $_POST['alasan_reject'] ?? 'Tidak ada alasan';
            
            $sql = "UPDATE request_pengembalian 
                    SET status = 'rejected', id_admin = ?, tanggal_response = NOW(), alasan_reject = ?
                    WHERE id_request = ?";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $id_admin, $alasan, $id_request);
            
            if (mysqli_stmt_execute($stmt)) {
                logAktivitas($koneksi, 'reject_pengembalian', 'Reject pengembalian: ' . $request['judul'], $id_admin, null);
                $_SESSION['success'] = 'Request pengembalian ditolak!';
            } else {
                $_SESSION['error'] = 'Gagal menolak request!';
            }
        }
    }
    
    redirect('approval-kembali.php');
}

// Ambil request pending
$requests = mysqli_query($koneksi, "
    SELECT rk.*, 
           a.nama, a.nis_nisn, a.kelas_jurusan,
           b.judul, b.pengarang, b.isbn, b.cover_path,
           t.tanggal_pinjam, t.batas_kembali
    FROM request_pengembalian rk
    JOIN transaksi t ON rk.id_transaksi = t.id_transaksi
    JOIN anggota a ON rk.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE rk.status = 'pending'
    ORDER BY rk.tanggal_request ASC
");

$total_pending = mysqli_num_rows($requests);
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Approval Pengembalian</h2>
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
                        <?php while ($r = mysqli_fetch_assoc($requests)): 
                            $terlambat = strtotime($r['batas_kembali']) < time();
                            $denda_keterlambatan = $terlambat ? hitungDenda(date('Y-m-d'), $r['batas_kembali']) : 0;
                            
                            $denda_kondisi = 0;
                            if ($r['kondisi_buku'] == 'rusak_ringan') $denda_kondisi = 10000;
                            elseif ($r['kondisi_buku'] == 'rusak_berat') $denda_kondisi = 50000;
                            elseif ($r['kondisi_buku'] == 'hilang') $denda_kondisi = 100000;
                            
                            $total_denda = $denda_keterlambatan + $denda_kondisi;
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-<?php echo $terlambat ? 'danger' : 'warning'; ?>">
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
                                            <p class="mb-1"><small>NIS: <?php echo $r['nis_nisn'] ?? '-'; ?></small></p>
                                            <p class="mb-1"><strong>Dipinjam:</strong> <?php echo formatTanggal($r['tanggal_pinjam']); ?></p>
                                            <p class="mb-1"><strong>Batas:</strong> 
                                                <span class="<?php echo $terlambat ? 'text-danger fw-bold' : ''; ?>">
                                                    <?php echo formatTanggal($r['batas_kembali']); ?>
                                                </span>
                                            </p>
                                            <p class="mb-2"><strong>Kondisi Buku:</strong> 
                                                <span class="badge bg-<?php 
                                                    echo $r['kondisi_buku'] == 'baik' ? 'success' : 
                                                        ($r['kondisi_buku'] == 'rusak_ringan' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $r['kondisi_buku'])); ?>
                                                </span>
                                            </p>
                                            
                                            <?php if ($r['keterangan']): ?>
                                                <p class="mb-2"><small><strong>Keterangan:</strong> <?php echo $r['keterangan']; ?></small></p>
                                            <?php endif; ?>
                                            
                                            <?php if ($total_denda > 0): ?>
                                                <div class="alert alert-danger p-2 mb-2">
                                                    <small>
                                                        <strong>Total Denda: Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></strong><br>
                                                        <?php if ($denda_keterlambatan > 0): ?>
                                                            • Keterlambatan: Rp <?php echo number_format($denda_keterlambatan, 0, ',', '.'); ?><br>
                                                        <?php endif; ?>
                                                        <?php if ($denda_kondisi > 0): ?>
                                                            • Kondisi buku: Rp <?php echo number_format($denda_kondisi, 0, ',', '.'); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex gap-2 mt-2">
                                                <a href="?action=approve&id=<?php echo $r['id_request']; ?>" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('Setujui pengembalian ini?\nDenda: Rp <?php echo number_format($total_denda, 0, ',', '.'); ?>')">
                                                    ✓ Setujui
                                                </a>
                                                
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
                                        <h5 class="modal-title">Tolak Request Pengembalian</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="?action=reject&id=<?php echo $r['id_request']; ?>" method="POST">
                                        <div class="modal-body">
                                            <p>Anda akan menolak request dari <strong><?php echo $r['nama']; ?></strong></p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan:</label>
                                                <textarea name="alasan_reject" class="form-control" rows="3" required 
                                                          placeholder="Contoh: Buku harus dikembalikan langsung ke perpustakaan"></textarea>
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
                        <h5>Tidak ada request pengembalian pending</h5>
                        <p>Semua request sudah diproses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>