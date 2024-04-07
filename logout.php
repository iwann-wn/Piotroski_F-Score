<?php
// Mulai sesi (pastikan ini berada di bagian atas halaman)
session_start();

// Hapus semua data sesi
session_destroy();

// Arahkan pengguna ke halaman login setelah logout
header('Location: login.php');
exit();
?>
