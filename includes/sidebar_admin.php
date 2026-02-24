<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px; min-height: 100vh;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4">Admin Panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo $base_url; ?>admin/dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['PHP_SELF'], 'admin') !== false) ? 'active' : 'link-dark'; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/buku/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'buku') !== false) ? 'active' : 'link-dark'; ?>">
                Data Buku
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/anggota/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'anggota') !== false) ? 'active' : 'link-dark'; ?>">
                Data Anggota
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/transaksi/pinjam.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pinjam.php') ? 'active' : 'link-dark'; ?>">
                Peminjaman
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/transaksi/kembali.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'kembali.php') ? 'active' : 'link-dark'; ?>">
                Pengembalian
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/transaksi/approval-pinjam.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'approval-pinjam.php') ? 'active' : 'link-dark'; ?>">
                Approval Peminjaman
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/transaksi/approval-kembali.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'approval-kembali.php') ? 'active' : 'link-dark'; ?>">
                Approval Pengembalian
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/transaksi/riwayat.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'active' : 'link-dark'; ?>">
                Riwayat Transaksi
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>admin/laporan.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : 'link-dark'; ?>">
                Laporan
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <strong><?php echo $_SESSION['nama']; ?></strong>
        </a>
        <ul class="dropdown-menu text-small shadow">
            <li><a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">Logout</a></li>
        </ul>
    </div>
</div>