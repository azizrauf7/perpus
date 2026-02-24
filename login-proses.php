<?php
session_start();
require_once 'koneksi.php';
require_once 'includes/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $role = clean($_POST['role']);
    
    if ($role == 'admin') {
        // Login sebagai admin
        $sql = "SELECT * FROM admin WHERE username = ? AND is_active = 1";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (verifyPassword($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id_admin'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama'] = $row['nama_lengkap'];
                $_SESSION['role'] = 'admin';
                
                // Log aktivitas
                logAktivitas($koneksi, 'login', 'Admin login: ' . $username, $row['id_admin'], null);
                
                redirect('admin/dashboard.php');
            } else {
                $_SESSION['error'] = 'Password salah!';
                redirect('index.php');
            }
        } else {
            $_SESSION['error'] = 'Username tidak ditemukan!';
            redirect('index.php');
        }
        
    } elseif ($role == 'siswa') {
        // Login sebagai siswa
        $sql = "SELECT * FROM anggota WHERE username = ? AND status_aktif = 1";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (verifyPassword($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id_anggota'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['role'] = 'siswa';
                
                // Log aktivitas
                logAktivitas($koneksi, 'login', 'Siswa login: ' . $username, null, $row['id_anggota']);
                
                redirect('siswa/dashboard.php');
            } else {
                $_SESSION['error'] = 'Password salah!';
                redirect('index.php');
            }
        } else {
            $_SESSION['error'] = 'Username tidak ditemukan atau akun tidak aktif!';
            redirect('index.php');
        }
    } else {
        $_SESSION['error'] = 'Pilih role terlebih dahulu!';
        redirect('index.php');
    }
} else {
    redirect('index.php');
}
?>