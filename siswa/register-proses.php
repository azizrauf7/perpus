<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis_nisn = clean($_POST['nis_nisn']);
    $nama = clean($_POST['nama']);
    $kelas_jurusan = clean($_POST['kelas_jurusan']);
    $jenis_kelamin = clean($_POST['jenis_kelamin']);
    $alamat = clean($_POST['alamat']);
    $nohp = clean($_POST['nohp']);
    $email = clean($_POST['email']);
    $username = clean($_POST['username']);
    $password = hashPassword($_POST['password']);
    
    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM anggota WHERE username = ?";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan!';
        redirect('register.php');
    }
    
    // Cek apakah NIS/NISN sudah ada
    if (!empty($nis_nisn)) {
        $check_nis_sql = "SELECT * FROM anggota WHERE nis_nisn = ?";
        $check_nis_stmt = mysqli_prepare($koneksi, $check_nis_sql);
        mysqli_stmt_bind_param($check_nis_stmt, "s", $nis_nisn);
        mysqli_stmt_execute($check_nis_stmt);
        $check_nis_result = mysqli_stmt_get_result($check_nis_stmt);
        
        if (mysqli_num_rows($check_nis_result) > 0) {
            $_SESSION['error'] = 'NIS/NISN sudah terdaftar!';
            redirect('register.php');
        }
    }
    
    // Insert data
    $sql = "INSERT INTO anggota (nis_nisn, nama, kelas_jurusan, jenis_kelamin, alamat, nohp, email, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", $nis_nisn, $nama, $kelas_jurusan, $jenis_kelamin, $alamat, $nohp, $email, $username, $password);
    
    if (mysqli_stmt_execute($stmt)) {
        // Log aktivitas
        $id_anggota = mysqli_insert_id($koneksi);
        logAktivitas($koneksi, 'register', 'Registrasi anggota baru: ' . $username, null, $id_anggota);
        
        $_SESSION['success'] = 'Registrasi berhasil! Silakan login dengan akun Anda.';
        redirect('../index.php');
    } else {
        $_SESSION['error'] = 'Registrasi gagal! Silakan coba lagi.';
        redirect('register.php');
    }
} else {
    redirect('register.php');
}
?>