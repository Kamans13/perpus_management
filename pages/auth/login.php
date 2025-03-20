<?php
session_start();
include __DIR__ . '/../../includes/koneksi.php';

// Proses login ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query untuk memeriksa username
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $query);

        // Jika username ditemukan
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);

            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $data['password'])) {
                // Mengambil data anggota jika role adalah anggota
                if ($data['role'] == 'anggota') {
                    // Query untuk mendapatkan data anggota
                    $query_anggota = "SELECT * FROM anggota WHERE id_user = ".$data['id_user'];
                    $result_anggota = mysqli_query($conn, $query_anggota);
                    if ($result_anggota && mysqli_num_rows($result_anggota) > 0) {
                        $data_anggota = mysqli_fetch_assoc($result_anggota);
                        $_SESSION['nama_anggota'] = $data_anggota['nama']; // Nama anggota
                        $_SESSION['id_anggota'] = $data_anggota['id_anggota']; // ID anggota
                    }
                }

                $_SESSION['username'] = $data['username'];
                $_SESSION['role'] = $data['role'];
                $_SESSION['id_user'] = $data['id_user']; // Menambahkan id_user ke sesi
                $_SESSION['welcome_message'] = "Selamat datang, " . $data['username'] . "!"; // Pesan selamat datang

                // Redirect berdasarkan role user
                if ($data['role'] == 'admin') {
                    header("Location: ../admin/index.php");
                    exit();
                } elseif ($data['role'] == 'petugas') {
                    header("Location: ../petugas/index.php");
                    exit();
                } elseif ($data['role'] == 'anggota') {
                    header("Location: ../anggota/index.php");
                    exit();
                }
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    } else {
        $error = "Harap isi username dan password!";
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
        <link href="../../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
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
                                        <!-- Menampilkan Pesan Error Jika Ada -->
                                        <?php if (isset($error)) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?php echo $error; ?>
                                            </div>
                                        <?php } ?>
                                        <!-- Form Login -->
                                        <form method="POST" action="">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputUsername" type="text" name="username" placeholder="Username Anda" required />
                                                <label for="inputUsername">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password Anda" required />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="indevelopment.php">Forgot Password?</a>
                                                <button type="submit" class="btn btn-primary">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="indevelopment.php">Need an account? Sign up!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Kaman Septia Project SPADA 2025</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
    </body>
</html>
