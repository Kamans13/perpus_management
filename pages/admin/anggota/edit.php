<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id_user'])) {
    $_SESSION['error'] = "ID Anggota tidak ditemukan.";
    header("Location: index.php");
    exit();
}

// Ambil nama pengguna
$id_user = $_SESSION['id_user'];
$username = getUserName($id_user);

// Menampilkan waktu sesuai dengan jam
$waktu = getWaktu();

// Ambil id_user dari URL
$id_user = $_GET['id_user'];

// Ambil data anggota berdasarkan ID dengan gabungan tabel users dan anggota
$sql = "SELECT u.id_user, u.username, u.email, a.nama, a.alamat, a.nomor_hp
        FROM users u
        JOIN anggota a ON u.id_user = a.id_user
        WHERE u.id_user = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Cek jika data anggota ditemukan
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Anggota tidak ditemukan.";
    header("Location: index.php");
    exit();
}

// Ambil data anggota
$row = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Edit Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
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
                            <li class="breadcrumb-item active">Edit Anggota</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Edit Anggota
                                </div>
                            </div>

                            <div class="card-body">
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger">
                                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                    </div>
                                <?php endif; ?>
                                <form method="POST" action="update.php">
                                    <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $row['nama']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?= $row['username']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= $row['email']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="alamat" name="alamat" required><?= $row['alamat']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nomor_hp" class="form-label">Nomor HP</label>
                                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="<?= $row['nomor_hp']; ?>" required>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        <a href="index.php" class="btn btn-danger btn-sm">Kembali</a>
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
