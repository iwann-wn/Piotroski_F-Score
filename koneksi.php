<?php
$host = 'localhost'; // Ganti dengan nama host MySQL Anda
$username = 'root'; // Ganti dengan username MySQL Anda
$password = ''; // Ganti dengan password MySQL Anda
$database = 'saham'; // Ganti dengan nama database Anda

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Atur pengkodean karakter UTF-8 jika diperlukan
    $conn->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    echo "Koneksi ke database gagal: " . $e->getMessage();
}
?>
