<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}


//fitur sapa
    // Ambil nama pengguna
    $id_user = $_SESSION['id_user'];
    $username = getUserName($id_user);

    // Menampilkan waktu sesuai dengan jam
    $waktu = getWaktu();

// Create a new user with 'Users' or 'admin' role
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $role = isset($_POST['role']) ? $_POST['role'] : ''; // role should be either 'Users' or 'admin'
    $tanggal_daftar = date('Y-m-d');

    // Validate inputs
    if (!empty($username) && !empty($email) && !empty($password) && !empty($role)) {
        // Ensure role is valid
        if (in_array($role, ['Users', 'admin'])) {
            // Insert data into the 'users' table
            $sql = "INSERT INTO users (username, password, email, role, tanggal_daftar)
                    VALUES ('$username', '$password', '$email', '$role', '$tanggal_daftar')";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = "Data $role berhasil ditambahkan!";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Gagal menambahkan $role: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "Role tidak valid! Hanya 'Users' atau 'admin' yang diizinkan.";
        }
    } else {
        $_SESSION['error'] = "Semua kolom harus diisi!";
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
        <title>Daftar Users</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

    <?php include '../includes/topnav.php' ?>

        <div id="layoutSidenav">

            <?php include '../includes/sidebar.php'?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Users</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Users</li>
                            <li class="breadcrumb-item active">Tambah Users</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Tambah Users
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="add.php">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username:</label>
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan Username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan Email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan Password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role:</label>
                                        <select class="form-select" name="role" id="role" required>
                                            <option value="" disabled selected>Pilih Role</option>
                                            <option value="petugas">Petugas</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="index.php" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>

                            </div>
                            
                        </div>
                    </div>
                </main>
                
                <?php include '../includes/footer.php'?>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/datatables-simple-demo.js"></script>
    </body>
</html>
