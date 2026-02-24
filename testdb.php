<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Connection Test ===\n\n";

$host = 'maglev.proxy.rlwy.net';
$user = 'root';
$pass = 'toZSvsOOeZgGtOUxTzVGZGuXQhIipZjd';
$db   = 'railway';
$port = 37954;

echo "Attempting to connect to:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "Database: $db\n\n";

// Try to connect
echo "Connecting...\n";
$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    echo "❌ CONNECTION FAILED!\n";
    echo "Error: " . mysqli_connect_error() . "\n";
    exit(1);
}

echo "✅ CONNECTION SUCCESS!\n\n";

// Try to get tables
echo "Tables in database:\n";
$result = mysqli_query($koneksi, "SHOW TABLES");
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        echo "  - " . $row[0] . "\n";
    }
} else {
    echo "❌ Failed to list tables: " . mysqli_error($koneksi) . "\n";
}

$koneksi->close();
echo "\n✅ Test completed!\n";
?>
