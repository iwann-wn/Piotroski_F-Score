<?php
session_start();
require_once('koneksi.php');

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses form login
    $email = $_POST['email']; // Mengambil nilai email dari form
    $password = $_POST['password']; // Mengambil nilai password dari form

    try {
        // untuk mengambil data user dari tabel 'users' berdasarkan email yang diinputkan
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR); // Membuat parameter untuk email
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Login berhasil, simpan informasi pengguna dalam sesi
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_nama'] = $row['nama'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_role'] = $row['role'];

                // Redirect berdasarkan role
                if ($row['role'] === 'admin') {
                    header('Location: admin/users.php');
                    exit();
                } elseif ($row['role'] === 'user') {
                    header('Location: index.php');
                    exit();
                }
            } else {
                $message = "Password salah. Silakan coba lagi.";
            }
        } else {
            $message = "Email tidak ditemukan. Silakan coba lagi atau daftar jika belum memiliki akun.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
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
    <title>Login - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($message)) : ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" type="email" name="email" required />
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" name="password" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-secondary" type="submit">Login</button>
                                            <a class="small" href="register.php">Butuh akun? Register!</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-secondary text-white mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; iwann 2023</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>
