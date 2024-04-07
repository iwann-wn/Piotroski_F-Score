<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userEmail = $_SESSION['user_email']; // Mendapatkan email pengguna dari sesi
$userNama = $_SESSION['user_nama']; // Mendapatkan nama pengguna dari sesi
$userRole = $_SESSION['user_role']; // Mendapatkan peran (role) pengguna dari sesi

$is_admin = false;
if ($userRole === 'admin') { // Jika 'admin', set is_admin menjadi true
    $is_admin = true;
}

try {
    
    // buat query SQL untuk mendapatkan data dari tabel 'stock' &  
    //melakukan left join dengan tabel 'klasifikasi' berdasarkan kondisi skor
    $sql = "SELECT stock.*, 
        CASE 
            WHEN stock.skor <= 3 THEN klasifikasi1.kategori
            WHEN stock.skor <= 7 THEN klasifikasi2.kategori
            ELSE klasifikasi3.kategori
        END AS kategori,
        CASE 
            WHEN stock.skor <= 3 THEN klasifikasi1.keterangan
            WHEN stock.skor <= 7 THEN klasifikasi2.keterangan
            ELSE klasifikasi3.keterangan
        END AS keterangan
        FROM stock
        LEFT JOIN klasifikasi AS klasifikasi1 ON stock.skor <= 3 AND klasifikasi1.id = 1
        LEFT JOIN klasifikasi AS klasifikasi2 ON stock.skor > 3 AND stock.skor <= 7 AND klasifikasi2.id = 2
        LEFT JOIN klasifikasi AS klasifikasi3 ON stock.skor > 7 AND klasifikasi3.id = 3 ";

    // Jika bukan admin, hanya data yang terkait dengan email user yang akan diambil
    if (!$is_admin) {
        $sql .= "WHERE stock.email = :user_email ";
    }

    $sql .= "ORDER BY stock.id DESC";

    $stmt = $conn->prepare($sql);

    if (!$is_admin) {
        $stmt->bindParam(':user_email', $userEmail, PDO::PARAM_STR);
    }

    $stmt->execute();

    $count = 1;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - SB Admin</title>
    <!-- Link CSS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 fw-bold" href="index.php">Analisis Saham</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <!-- Navbar-->

    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <a class="nav-link" href="upload.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-folder"></i></div>
                            Upload
                        </a>

                        <?php if ($userRole === 'admin'): ?>
                            <a class="nav-link" href="admin/users.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Admin
                            </a>
                        <?php endif; ?>

                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user fa-fw"></i></div>
                            Logout
                        </a>

                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as : </div>
                    <p class="fw-bold">
                        <?php echo $userNama; ?>
                    </p>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard </h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            DataTable Saham
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Saham</th>
                                        <th>Tahun</th>
                                        <th>Skor</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Email User</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr>
                                            <td>
                                                <?= $count++ ?>
                                            </td>
                                            <td>
                                                <a href='all.php?kode_saham=<?= $row['kode_saham'] ?>'>
                                                    <?= $row['kode_saham'] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= $row['tahun'] ?>
                                            </td>
                                            <td>
                                                <?= $row['skor'] ?>
                                            </td>
                                            <td>
                                                <?= $row['kategori'] ?>
                                            </td>
                                            <td>
                                                <?= $row['keterangan'] ?>
                                            </td>
                                            <td>
                                                <?= $row['email'] ?>
                                            </td>
                                            <td>
                                                <span style='white-space: nowrap;'>
                                                    <a href='edit_data.php?id=<?= $row['id'] ?>'
                                                        class='btn btn-primary'>Edit</a>
                                                    <a href='hapus_saham.php?id=<?= $row['id'] ?>'
                                                        onclick='return confirmDelete()' class='btn btn-danger'>Hapus</a>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Other content -->
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted"> </div>
                        <div> Copyright &copy; iwann 2023 </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Link JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>

<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus data saham ini?");
    }
</script>
</body>

</html>