<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Riwayat Transaksi';
$base_url = '../../';
include '../../includes/header.php';

// Filter
$filter_status = $_GET['status'] ?? 'semua';
$filter_bulan = $_GET['bulan'] ?? '';

$where = "WHERE 1=1";
if ($filter_status != 'semua') {
    $where .= " AND t.status = '$filter_status'";
}
if (!empty($filter_bulan)) {
    $where .= " AND DATE_FORMAT(t.tanggal_pinjam, '%Y-%m') = '$filter_bulan'";
}

$transaksi = mysqli_query($koneksi, "
    SELECT t.*, 
           a.nama as nama_anggota, a.nis_nisn,
           b.judul as judul_buku, b.isbn,
           ad.nama_lengkap as nama_admin
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    JOIN admin ad ON t.id_admin = ad.id_admin
    $where
    ORDER BY t.created_at DESC
");
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Riwayat Transaksi</h2>
        
        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="semua" <?php echo $filter_status == 'semua' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="dipinjam" <?php echo $filter_status == 'dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                            <option value="dikembalikan" <?php echo $filter_status == 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                            <option value="terlambat" <?php echo $filter_status == 'terlambat' ? 'selected' : ''; ?>>Terlambat</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="<?php echo $filter_bulan; ?>">
                    </div>
                    <div class="col-md-4 d-grid">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" id="exportForm" action="export-riwayat.php">
                    <div class="mb-3 d-flex justify-content-end">
                        <button type="submit" id="exportBtn" class="btn btn-sm btn-outline-dark" disabled>Export Selected (Excel)</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;"><input type="checkbox" id="select_all"></th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Anggota</th>
                                    <th>Buku</th>
                                    <th>Batas Kembali</th>
                                    <th>Tgl Kembali</th>
                                    <th>Denda</th>
                                    <th>Status</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_denda = 0;
                                mysqli_data_seek($transaksi, 0);
                                while ($t = mysqli_fetch_assoc($transaksi)): 
                                    $total_denda += $t['denda'];
                                ?>
                                <tr>
                                    <td><input type="checkbox" class="select_item" name="ids[]" value="<?php echo $t['id_transaksi']; ?>"></td>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo formatTanggal($t['tanggal_pinjam']); ?></td>
                                    <td>
                                        <?php echo $t['nama_anggota']; ?><br>
                                        <small class="text-muted"><?php echo $t['nis_nisn'] ?? '-'; ?></small>
                                    </td>
                                    <td>
                                        <?php echo $t['judul_buku']; ?><br>
                                        <small class="text-muted"><?php echo $t['isbn']; ?></small>
                                    </td>
                                    <td><?php echo formatTanggal($t['batas_kembali']); ?></td>
                                    <td>
                                        <?php echo $t['tanggal_kembali'] ? formatTanggal($t['tanggal_kembali']) : '-'; ?>
                                    </td>
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
                                            $badge_class = strtotime($t['batas_kembali']) < time() ? 'danger' : 'warning';
                                        } elseif ($t['status'] == 'dikembalikan') {
                                            $badge_class = 'success';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($t['status']); ?></span>
                                    </td>
                                    <td><small><?php echo $t['nama_admin']; ?></small></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Denda:</th>
                                    <th colspan="3">
                                        <span class="text-danger fw-bold">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectAll = document.getElementById('select_all');
    const exportBtn = document.getElementById('exportBtn');
    function updateExportButton() {
        const any = document.querySelectorAll('.select_item:checked').length > 0;
        if (exportBtn) exportBtn.disabled = !any;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function(e){
            const checked = e.target.checked;
            document.querySelectorAll('.select_item').forEach(cb => cb.checked = checked);
            updateExportButton();
        });
    }

    document.querySelectorAll('.select_item').forEach(cb => {
        cb.addEventListener('change', updateExportButton);
    });
});
</script>

<?php include '../../includes/footer.php'; ?>