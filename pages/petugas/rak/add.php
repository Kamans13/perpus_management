<<?php
// Sambungkan ke database
include '../loggedas.php';

session_start();

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil nama pengguna untuk fitur sapa
$id_user = $_SESSION['id_user'];
$username = getUserName($id_user);

// Menampilkan waktu
$waktu = getWaktu();

// Memeriksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_rak = $_POST['kode_rak'];
    $nama_rak = $_POST['nama_rak'];
    $lokasi_rak = $_POST['lokasi_rak'];

    // Periksa apakah kode_rak sudah ada
    $check_query = "SELECT * FROM rak WHERE kode_rak = '$kode_rak'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Jika kode_rak sudah ada
        $_SESSION['error'] = "Kode rak sudah ada. Silakan gunakan kode lain.";
    } else {
        // Jika kode_rak belum ada, lakukan insert
        $sql = "INSERT INTO rak (kode_rak, nama_rak, lokasi_rak) 
                VALUES ('$kode_rak', '$nama_rak', '$lokasi_rak')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Rak berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }

    header("Location: index.php");
    exit;
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
        <title>Tambah Rak Buku</title>
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
                        <h1 class="mt-4">Rak Buku</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Buku</li>
                            <li class="breadcrumb-item active">Tambah Rak Buku</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Tambah Rak Buku
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="add.php">
                                    <div class="mb-3">
                                        <label for="kode_rak" class="form-label">Kode Rak</label>
                                        <input type="text" class="form-control" id="kode_rak" name="kode_rak" placeholder="Masukkan kode rak, ex: A1 (1 huruf 1 angka)" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nama_rak" class="form-label">Nama Rak</label>
                                        <input type="text" class="form-control" id="nama_rak" name="nama_rak" placeholder="Masukkan nama rak, ex: Rak Fiksi" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                                        <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" placeholder="Masukkan lokasi rak, ex: Lantai 1 - Tengah" required>
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
