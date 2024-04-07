<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userEmail = $_SESSION['user_email']; // Mendapatkan email pengguna dari sesi
$userNama = $_SESSION['user_nama']; // Mendapatkan nama pengguna dari sesi
$userRole = $_SESSION['user_role']; // Mendapatkan peran (role) pengguna dari sesi

// cek parameter 'kode_saham' telah diterima melalui GET, jika tidak, arahkan kembali ke index
if (!isset($_GET['kode_saham'])) {
    header('Location: index.php');
    exit();
}

try {
    $kode_saham = $_GET['kode_saham'];

    // Modifikasi query SQL berdasarkan peran pengguna
    if ($userRole === 'admin') { // jka peran 'admin', query tidak mempertimbangkan email user
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
            LEFT JOIN klasifikasi AS klasifikasi3 ON stock.skor > 7 AND klasifikasi3.id = 3
            WHERE stock.kode_saham = :kode_saham";
    } else { // Jika bukan 'admin', query hanya akan mengambil data yang terkait dengan email user
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
            LEFT JOIN klasifikasi AS klasifikasi3 ON stock.skor > 7 AND klasifikasi3.id = 3
            WHERE stock.kode_saham = :kode_saham AND stock.email = :user_email";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':kode_saham', $kode_saham, PDO::PARAM_STR);
    
    // Bind parameter user_email jika bukan admin
    // jika bukan admin , data yg ditampilkan hanya user yg login
    if ($userRole !== 'admin') {
        $stmt->bindParam(':user_email', $userEmail, PDO::PARAM_STR);
    }
    
    $stmt->execute();

    $stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;

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
    <title>Tables - SB Admin</title>
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
                    <h1 class="mt-4"> Data Semua Saham -
                        <?php echo isset($kode_saham) ? $kode_saham : ''; ?>
                    </h1>

                    <ol class="breadcrumb mb-4">
                    </ol>

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
                                        <th>Net Income</th>
                                        <th>Operating Cashflow</th>
                                        <th>Return on Asset Previous</th>
                                        <th>Return on Asset Now</th>
                                        <th>Quality of Earning</th>
                                        <th>Long Term Debt to Asset Previous</th>
                                        <th>Long Term Debt to Asset Now</th>
                                        <th>Current Ratio Previous</th>
                                        <th>Current Ratio Now</th>
                                        <th>Outstanding Shares Previous</th>
                                        <th>Outstanding Shares Now</th>
                                        <th>Gross Margin Previous</th>
                                        <th>Gross Margin Now</th>
                                        <th>Asset Turnover Previous</th>
                                        <th>Asset Turnover Now</th>
                                        <th>Skor</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($stockData)) {
                                        $count = 1;
                                        foreach ($stockData as $row) {
                                            echo "<tr>";
                                            echo "<td>" . $count++ . "</td>";
                                            echo "<td>" . $row['kode_saham'] . "</td>";
                                            echo "<td>" . $row['tahun'] . "</td>";
                                            echo "<td>" . $row['net_income'] . "</td>";
                                            echo "<td>" . $row['operating_cashflow'] . "</td>";
                                            echo "<td>" . $row['return_on_asset_previous'] . "</td>";
                                            echo "<td>" . $row['return_on_asset_now'] . "</td>";
                                            // Menampilkan nilai terakhir dari kolom 'quality_of_earning'
                                            echo "<td>" . substr($row['quality_of_earning'], -1) . "</td>";
                                            echo "<td>" . $row['long_term_debt_to_asset_previous'] . "</td>";
                                            echo "<td>" . $row['long_term_debt_to_asset_now'] . "</td>";
                                            echo "<td>" . $row['current_ratio_previous'] . "</td>";
                                            echo "<td>" . $row['current_ratio_now'] . "</td>";
                                            echo "<td>" . $row['outstanding_shares_previous'] . "</td>";
                                            echo "<td>" . $row['outstanding_shares_now'] . "</td>";
                                            echo "<td>" . $row['gross_margin_previous'] . "</td>";
                                            echo "<td>" . $row['gross_margin_now'] . "</td>";
                                            echo "<td>" . $row['asset_turnover_previous'] . "</td>";
                                            echo "<td>" . $row['asset_turnover_now'] . "</td>";
                                            echo "<td>" . $row['skor'] . "</td>";
                                            echo "<td>" . $row['kategori'] . "</td>";
                                            echo "<td>" . $row['keterangan'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>Data not found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>
