<?php
require 'vendor/autoload.php'; // Memuat library PhpSpreadsheet
require 'koneksi.php';

use PhpOffice\PhpSpreadsheet\IOFactory; // Menggunakan IOFactory dari PhpSpreadsheet

session_start();

// Memeriksa apakah sudah login, jika belum arahkan ke  login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Mengambil informasi pengguna dari sesi
$userEmail = $_SESSION['user_email'];
$userNama = $_SESSION['user_nama'];
$userRole = $_SESSION['user_role'];

// Memeriksa jika tombol upload ditekan
if (isset($_POST['upload'])) {
    try {
        // Upload file Excel jika tidak ada error
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($_FILES['file']['name']);
            
            // Memeriksa tipe file yang diizinkan (hanya excel yg diizinkan)
            if (in_array($fileInfo['extension'], ['xlsx', 'xls'])) {
                
                // Mengambil nama file sementara
                $inputFileName = $_FILES['file']['tmp_name'];

                
                // Membaca data dari file Excel menggunakan PhpSpreadsheet
                $reader = IOFactory::createReaderForFile($inputFileName);
                $reader->setReadDataOnly(true);
                $reader->setLoadSheetsOnly(['Sheet1']); // Mengatur sheet yang akan dibaca
                $spreadsheet = $reader->load($inputFileName);

                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();

                // Hapus baris pertama (header)
                if (!empty($data)) {
                    array_shift($data);
                }
                
                // Ambil email pengguna dari sesi
                if (isset($_SESSION['user_email'])) {
                    $userEmail = $_SESSION['user_email'];
                } else {
                    // Jika tidak ada, arahkan kembali ke halaman login
                    header('Location: login.php');
                    exit();
                }

                // Loop data dari file Excel dan masukkan ke database
                foreach ($data as $row) {
                    // Mengambil nilai dari setiap kolom excel secara horizontal
                    $kode_saham = $row[0];
                    $tahun = $row[1];
                    $net_income = $row[2];
                    $operating_cashflow = $row[3];
                    $return_on_asset_previous = $row[4];
                    $return_on_asset_now = $row[5];
                    $long_term_debt_to_asset_previous = $row[6];
                    $long_term_debt_to_asset_now = $row[7];
                    $current_ratio_previous = $row[8];
                    $current_ratio_now = $row[9];
                    $outstanding_shares_previous = $row[10];
                    $outstanding_shares_now = $row[11];
                    $gross_margin_previous = $row[12];
                    $gross_margin_now = $row[13];
                    $asset_turnover_previous = $row[14];
                    $asset_turnover_now = $row[15];

                    // Hitung skor-skor sesuai dengan aturan penilaian
                    $net_income_score = ($net_income > 0) ? 1 : 0;
                    $operating_cashflow_score = ($operating_cashflow > 0) ? 1 : 0;
                    $return_on_asset_score = ($return_on_asset_previous < $return_on_asset_now) ? 1 : 0;
                    $quality_of_earning_score = ($operating_cashflow < $net_income) ? 0 : 1;
                    
                    // Hitung skor long term debt to asset
                    if ($long_term_debt_to_asset_now < $long_term_debt_to_asset_previous) {
                        $long_term_debt_score = 1;
                    } elseif ($long_term_debt_to_asset_now === $long_term_debt_to_asset_previous) {
                        $long_term_debt_score = 1;
                    } else {
                        $long_term_debt_score = 0;
                    }

                    $current_ratio_score = ($current_ratio_previous < $current_ratio_now) ? 1 : 0;

                    // Hitung skor Outstanding Shares
                    if ($outstanding_shares_now < $outstanding_shares_previous) {
                        $outstanding_shares_score = 1;
                    } elseif ($outstanding_shares_now === $outstanding_shares_previous) {
                        $outstanding_shares_score = 1;
                    } else {
                        $outstanding_shares_score = 0;
                    }

                    $gross_margin_score = ($gross_margin_previous < $gross_margin_now) ? 1 : 0;
                    $asset_turnover_score = ($asset_turnover_previous < $asset_turnover_now) ? 1 : 0;

                    // Mengisi nilai quality_of_earning dengan data net_income, operating_cashflow, dan skor
                    $quality_of_earning = $net_income . ' / ' . $operating_cashflow . ' / ' . $quality_of_earning_score;

                    // Menyimpan data ke database menggunakan prepared statement
                    $sql = "INSERT INTO stock (kode_saham, tahun, net_income, operating_cashflow, return_on_asset_previous, return_on_asset_now, quality_of_earning, long_term_debt_to_asset_previous, long_term_debt_to_asset_now, current_ratio_previous, current_ratio_now, outstanding_shares_previous, outstanding_shares_now, gross_margin_previous, gross_margin_now, asset_turnover_previous, asset_turnover_now, skor, email) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        $kode_saham,
                        $tahun,
                        $net_income,
                        $operating_cashflow,
                        $return_on_asset_previous,
                        $return_on_asset_now,
                        $quality_of_earning, // Diisi dengan data net_income, operating_cashflow, dan skor
                        $long_term_debt_to_asset_previous,
                        $long_term_debt_to_asset_now,
                        $current_ratio_previous,
                        $current_ratio_now,
                        $outstanding_shares_previous,
                        $outstanding_shares_now,
                        $gross_margin_previous,
                        $gross_margin_now,
                        $asset_turnover_previous,
                        $asset_turnover_now,
                        $net_income_score + $operating_cashflow_score + $return_on_asset_score + $quality_of_earning_score + $long_term_debt_score + $current_ratio_score + $outstanding_shares_score + $gross_margin_score + $asset_turnover_score,
                        $userEmail // Masukkan email pengguna ke kolom "email"
                    ]);
                }

                // Tutup koneksi ke database
                $conn = null;

                // Arahkan ke halaman index.php jika upload berhasil
                header('Location: index.php');
                exit();
            } else {
                $errorMessage = "Tipe file tidak valid. Harap upload file Excel (.xlsx atau .xls).";
            }
        } else {
            $errorMessage = "Silahkan Pilih File Terlebih Dahulu.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
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
                    <h1 class="mt-4 mb-4">Halaman Upload </h1>

                    <div class="card mb-4">
                        <div class="card-body">

                            <?php if (!empty($errorMessage)): ?>
                                <!-- Tampilkan pesan kesalahan di atas form -->
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $errorMessage; ?>
                                </div>
                            <?php endif; ?>

                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <div>
                                        <label for="formFileLg" class="form-label">Pilih Data Saham</label>
                                        <input class="form-control form-control-lg" id="formFileLg" type="file"
                                            name="file" accept=".xlsx, .xls">

                                        <button type="submit" class="btn btn-secondary mt-3"
                                            name="upload">Upload</button>
                                    </div>
                            </form>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>

</body>
</html>