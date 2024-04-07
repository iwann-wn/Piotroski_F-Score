<?php

session_start();

// Periksa apakah pengguna sudah login atau tidak
if (!isset($_SESSION['user_id'])) {
    // Jika tidak, arahkan kembali ke halaman login atau halaman lain yang sesuai
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'saham';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];

    $sql = "DELETE FROM stock WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: index.php');
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
