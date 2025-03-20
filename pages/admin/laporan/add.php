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

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Tambah Laporan</title>
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
                        <h1 class="mt-4">Laporan</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Laporan</li>
                            <li class="breadcrumb-item active">Tambah Laporan</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Tambah Laporan
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="proses_laporan.php">
                                    <div class="mb-3">
                                        <label class="form-label" for="bulan">Bulan:</label>
                                        <select class="form-control" name="bulan" id="bulan">
                                            <?php 
                                            // Loop untuk menampilkan daftar bulan
                                            for ($i = 1; $i <= 12; $i++) { 
                                                $bulan = str_pad($i, 2, "0", STR_PAD_LEFT);
                                                $nama_bulan = date("F", mktime(0, 0, 0, $i, 1));
                                            ?>
                                                <option value="<?= $bulan ?>"><?= $nama_bulan ?></option>
                                            <?php } ?>
                                        </select>

                                        <label class="form-label" for="tahun">Tahun:</label>
                                        <select class="form-control" name="tahun" id="tahun">
                                            <?php 
                                            // Loop untuk menampilkan daftar tahun dari 2025 ke bawah
                                            $tahun_awal = 2025; // Tahun mulai
                                            for ($i = $tahun_awal; $i >= ($tahun_awal - 5); $i--) { 
                                            ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php } ?>
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
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/datatables-simple-demo.js"></script>
    </body>
</html>
