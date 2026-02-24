<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px; min-height: 100vh;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4">Siswa Panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?php echo $base_url; ?>siswa/dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : 'link-dark'; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>siswa/buku.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'buku.php') ? 'active' : 'link-dark'; ?>">
                Katalog Buku
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>siswa/pinjamanku.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pinjamanku.php') ? 'active' : 'link-dark'; ?>">
                Pinjamanku
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>siswa/request-saya.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'request-saya.php') ? 'active' : 'link-dark'; ?>">
                Request Saya
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>siswa/profil.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : 'link-dark'; ?>">
                Profil Saya
            </a>
        </li>
        <li>
            <a href="<?php echo $base_url; ?>siswa/ubah-password.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'ubah-password.php') ? 'active' : 'link-dark'; ?>">
                Ubah Password
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