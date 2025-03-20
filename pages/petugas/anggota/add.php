<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil nama pengguna
$id_user = $_SESSION['id_user'];
$username = getUserName($id_user);

// Menampilkan waktu sesuai dengan jam
$waktu = getWaktu();

// Create a new user with the 'anggota' role
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
    $nomor_hp = isset($_POST['nomor_hp']) ? $_POST['nomor_hp'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Menambahkan password
    $tanggal_daftar = date('Y-m-d');

    if (!empty($nama) && !empty($username) && !empty($email) && !empty($alamat) && !empty($nomor_hp) && !empty($password)) {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Step 1: Insert data into the 'users' table with hashed password
        $stmt_user = $conn->prepare("INSERT INTO users (username, email, password, tanggal_daftar, role) VALUES (?, ?, ?, ?, ?)");
        $stmt_user->bind_param("sssss", $username, $email, $hashed_password, $tanggal_daftar, $role);
        $role = 'anggota';  // Set role to 'anggota'
        
        // Execute query for inserting data into users table
        if ($stmt_user->execute()) {
            // Step 2: Get the last inserted id_user from users table
            $id_user = $conn->insert_id;

            // Step 3: Insert data into the 'anggota' table
            $stmt_anggota = $conn->prepare("INSERT INTO anggota (id_user, nama, alamat, nomor_hp) VALUES (?, ?, ?, ?)");
            $stmt_anggota->bind_param("isss", $id_user, $nama, $alamat, $nomor_hp);
            
            // Execute query for inserting data into anggota table
            if ($stmt_anggota->execute()) {
                $_SESSION['success'] = "Data anggota berhasil ditambahkan!";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Gagal menambahkan data anggota: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "Gagal menambahkan data pengguna: " . $conn->error;
        }

        // Close statements after execution
        $stmt_user->close();
        $stmt_anggota->close();
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
        <title>Tambah Anggota</title>
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
                        <h1 class="mt-4">Anggota</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Anggota</li>
                            <li class="breadcrumb-item active">Tambah Anggota</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Tambah Anggota
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="add.php" >
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email"required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat"required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nomor_hp" class="form-label">Nomor HP</label>
                                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" placeholder="Masukkan Nomor HP"required>
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
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/datatables-simple-demo.js"></script>
    </body>
</html>
