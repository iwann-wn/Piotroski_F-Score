<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once('../koneksi.php');

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    try {
        $sql = "DELETE FROM users WHERE id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Hapus berhasil
            $_SESSION['delete_success'] = "Data Berhasil Dihapus";
        } else {
            // Hapus gagal
            $_SESSION['delete_error'] = "Data Gagal Dihapus";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_error'] = "Error: " . $e->getMessage();
    }
}

header('Location: users.php');
exit();
?>
