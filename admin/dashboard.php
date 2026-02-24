<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

// Cek login dan role
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$title = 'Dashboard Admin';
$base_url = '../';
include '../includes/header.php';

// Ambil statistik
$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota WHERE status_aktif = 1"))['total'];
$total_dipinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'dipinjam'"))['total'];
$total_terlambat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'dipinjam' AND batas_kembali < CURDATE()"))['total'];

// Ambil request pending
$total_request_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM request_peminjaman WHERE status = 'pending'"))['total'];
$total_request_kembali = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM request_pengembalian WHERE status = 'pending'"))['total'];

// Ambil transaksi (search + pagination)
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$q = isset($_GET['q']) ? clean($_GET['q']) : '';

$where = '';
if ($q !== '') {
    $where = "WHERE (a.nama LIKE '%" . $q . "%' OR b.judul LIKE '%" . $q . "%' OR t.status LIKE '%" . $q . "%')";
}

$total_count_row = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    $where
"));
$total_items = $total_count_row['total'];
$total_pages = $total_items > 0 ? ceil($total_items / $limit) : 1;

$transaksi_terbaru = mysqli_query($koneksi, "
    SELECT t.*, a.nama as nama_anggota, b.judul as judul_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    $where
    ORDER BY t.created_at DESC
    LIMIT $limit OFFSET $offset
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Dashboard Admin</h2>
        <!-- Chart Statistik -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <canvas id="statsChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Buku</h5>
                        <h2 class="mb-0"><?php echo $total_buku; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Anggota</h5>
                        <h2 class="mb-0"><?php echo $total_anggota; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Sedang Dipinjam</h5>
                        <h2 class="mb-0"><?php echo $total_dipinjam; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Terlambat</h5>
                        <h2 class="mb-0"><?php echo $total_terlambat; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Request Notifications -->
        <?php if ($total_request_pinjam > 0 || $total_request_kembali > 0): ?>
        <div class="row mb-4">
            <?php if ($total_request_pinjam > 0): ?>
            <div class="col-md-6">
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <div>
                        <strong>🔔 Request Peminjaman Pending</strong><br>
                        <small>Ada <?php echo $total_request_pinjam; ?> request peminjaman yang menunggu persetujuan</small>
                    </div>
                    <a href="transaksi/approval-pinjam.php" class="btn btn-warning">
                        Lihat <span class="badge bg-dark"><?php echo $total_request_pinjam; ?></span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($total_request_kembali > 0): ?>
            <div class="col-md-6">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>🔔 Request Pengembalian Pending</strong><br>
                        <small>Ada <?php echo $total_request_kembali; ?> request pengembalian yang menunggu persetujuan</small>
                    </div>
                    <a href="transaksi/approval-kembali.php" class="btn btn-info">
                        Lihat <span class="badge bg-dark"><?php echo $total_request_kembali; ?></span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi</h5>
                    <div>
                        <form class="d-flex" method="GET" style="gap:8px; align-items:center;">
                            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control form-control-sm" placeholder="Cari anggota / buku / status">
                            <button class="btn btn-sm btn-primary" type="submit">Cari</button>
                            <!-- Export moved to Laporan & Riwayat pages -->
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($transaksi_terbaru)): ?>
                            <tr>
                                <td><?php echo formatTanggal($row['tanggal_pinjam']); ?></td>
                                <td><?php echo $row['nama_anggota']; ?></td>
                                <td><?php echo $row['judul_buku']; ?></td>
                                <td><?php echo formatTanggal($row['batas_kembali']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'secondary';
                                    if ($row['status'] == 'dipinjam') {
                                        $badge_class = strtotime($row['batas_kembali']) < time() ? 'danger' : 'warning';
                                    } elseif ($row['status'] == 'dikembalikan') {
                                        $badge_class = 'success';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($row['status']); ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                <li class="page-item <?php echo ($p == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?q=<?php echo urlencode($q); ?>&page=<?php echo $p; ?>"><?php echo $p; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Buku', 'Total Anggota', 'Sedang Dipinjam', 'Terlambat'],
                datasets: [{
                    label: 'Jumlah',
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                    data: [<?php echo (int)$total_buku; ?>, <?php echo (int)$total_anggota; ?>, <?php echo (int)$total_dipinjam; ?>, <?php echo (int)$total_terlambat; ?>]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
</script>
<?php include '../includes/footer.php'; ?>