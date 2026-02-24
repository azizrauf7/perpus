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

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = mysqli_connect($host, $user, $pass, $db, $port);
    
    // Set charset to utf8mb4
    mysqli_set_charset($koneksi, "utf8mb4");
    
} catch (mysqli_sql_exception $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    error_log("Host: $host, Port: $port, User: $user, DB: $db");
    
    // For debugging
    die("Database connection failed. Check server logs for details.\n");
}
?>
