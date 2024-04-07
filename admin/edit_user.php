<?php
session_start();
require_once('../koneksi.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$userEmail = $_SESSION['user_email']; // Mendapatkan email pengguna dari sesi
$userNama = $_SESSION['user_nama']; // Mendapatkan nama pengguna dari sesi
$userRole = $_SESSION['user_role']; // Mendapatkan peran (role) pengguna dari sesi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $userName = $_POST['user_name'];
    $userEmail = $_POST['user_email'];
    $userRole = $_POST['user_role'];

    // query untuk mengupdate data pengguna di database
    $stmt = $conn->prepare("UPDATE users SET nama = :nama, email = :email, role = :role WHERE id = :id");
    // tampung inputan sblum update ke dtabase
    $success = $stmt->execute([':nama' => $userName, ':email' => $userEmail, ':role' => $userRole, ':id' => $userId]);

    $_SESSION['update_success'] = $success;

    echo json_encode(['success' => $success]); // Keluarkan respon JSON berisi status keberhasilan update
    exit(); // Hentikan eksekusi 
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // query untuk mengambil data pengguna berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Ambil satu baris data pengguna dan simpan dalam bentuk asosiatif array
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
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Start Bootstrap</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <a class="nav-link" href="../index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <a class="nav-link" href="../upload.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-fw fa-folder"></i></div>
                            Upload
                        </a>

                        <?php if ($userRole === 'admin'): ?>
                            <a class="nav-link" href="users.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Admin
                            </a>
                        <?php endif; ?>

                        <a class="nav-link" href="../logout.php">
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
                    <h1 class="mt-4">Edit User</h1>
                    <ol class="breadcrumb mb-4">
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">

                            <form method="POST" id="editForm"  class="row g-3 needs-validation" novalidate>
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <div class="col-md-12">
                                    <label for="user_name" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name"
                                        value="<?= $user['nama'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="user_email" name="user_email"
                                        value="<?= $user['email'] ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select" id="role" name="user_role" required>
                                                <option value="admin" <?php echo ($user['role'] === 'admin' ? 'selected' : ''); ?>>
                                                    Admin</option>
                                                <option value="user" <?php echo ($user['role'] === 'user' ? 'selected' : ''); ?>>
                                                    User</option>
                                            </select>
                                        </div>
        
                                <button type="submit" class="btn btn-secondary">Update</button>
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
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>

    <script>
    document.getElementById('editForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Mencegah form untuk langsung melakukan submit

        // Lakukan proses fetch atau AJAX untuk mengirim data form ke server

        fetch('edit_user.php', {
            method: 'POST',
            body: new FormData(this),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Data berhasil diubah!');
                window.location.href = 'users.php'; // Arahkan ke halaman users.php setelah pesan alert
            } else {
                alert('Data gagal diubah!');
                window.location.href = 'users.php'; // Arahkan ke halaman users.php setelah pesan alert
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
</body>

</html>