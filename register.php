<?php
require 'koneksi.php'; // Sertakan file koneksi ke database
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = ''; // Variabel untuk menyimpan pesan yang akan ditampilkan kepada user

if (isset($_POST['register'])) { 
    // Jika form registrasi disubmit, proses data
    $nama = $_POST['nama']; // Mengambil nilai nama dari form
    $email = $_POST['email']; // Mengambil nilai email dari form
    $password = $_POST['password']; // Mengambil nilai password dari form

    // Periksa apakah alamat email sudah terdaftar
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $message = "Alamat email sudah terdaftar.";
    } else {
        // Jika email belum terdaftar, lakukan registrasi
        // Enkripsi password sebelum disimpan ke database untuk keamanan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert pengguna baru ke database
        $sql = "INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registrasi berhasil. Silakan <a href='login.php'>login</a>.";
        } else {
            $message = "Terjadi kesalahan saat melakukan registrasi.";
        }
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
    <title>Register - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Register</h3>
                                </div>
                                <div class="card-body">
                                    <?php if ($message !== '') : ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="text" type="text" name="nama" required />
                                            <label for="text">Nama</label>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputEmail" type="email" name="email" required />
                                                    <label for="inputEmail">Email</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputPassword" type="password" name="password" required />
                                                    <label for="inputPassword">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid"><button class="btn btn-secondary btn-block" type="submit" name="register">Create Account</button></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="login.php">Sudah Punya akun? Login</a></div>
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
