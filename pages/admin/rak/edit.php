<<?php
// Sambungkan ke database
include '../loggedas.php';

session_start();

// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil nama pengguna untuk fitur sapa
$id_user = $_SESSION['id_user'];
$username = getUserName($id_user);

// Menampilkan waktu
$waktu = getWaktu();

// Ambil ID rak dari URL
$id_rak = $_GET['id_rak'];

// Ambil data rak berdasarkan ID
$sql = "SELECT * FROM rak WHERE id_rak = '$id_rak'";
$result = mysqli_query($conn, $sql);
$rak = mysqli_fetch_assoc($result);

// Jika rak tidak ditemukan, redirect ke halaman daftar rak
if (!$rak) {
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
        <title>Edit Rak Buku</title>
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
                            <li class="breadcrumb-item active">Edit Rak Buku</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Edit Rak Buku
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="update.php?id_rak=<?php echo $rak['id_rak']; ?>">
                                    <div class="mb-3">
                                        <label for="kode_rak" class="form-label">Kode Rak</label>
                                        <input type="text" class="form-control" id="kode_rak" name="kode_rak" value="<?php echo $rak['kode_rak']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nama_rak" class="form-label">Nama Rak</label>
                                        <input type="text" class="form-control" id="nama_rak" name="nama_rak" value="<?php echo $rak['nama_rak']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                                        <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" value="<?php echo $rak['lokasi_rak']; ?>" required>
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
