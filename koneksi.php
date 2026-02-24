<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$host = 'maglev.proxy.rlwy.net';
$user = 'root';
$pass = 'toZSvsOOeZgGtOUxTzVGZGuXQhIipZjd';
$db   = 'railway';
$port = 37954;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>