<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

//fitur sapa
    // Ambil nama pengguna
    $id_user = $_SESSION['id_user'];
    $username = getUserName($id_user);

    // Menampilkan waktu sesuai dengan jam
    $waktu = getWaktu();


// Ambil semua laporan dari database
$sql = "SELECT * FROM laporan";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Kelola Laporan</title>
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
                        <h1 class="mt-4">Kelola Laporan</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Laporan</li>
                            <li class="breadcrumb-item active">Kelola Laporan</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-table me-1"></i>
                                    DataTable Laporan Per-Bulan
                                </div>
                                <a href="add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah
                                </a>
                            </div>

                            <div class="card-body">

                            <?php include '../includes/notif.php'?>
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Total Peminjaman</th>
                                            <th>Total Pengembalian</th>
                                            <th>Total Denda</th>     
                                            <th>Aksi</th>                                 
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Total Peminjaman</th>
                                            <th>Total Pengembalian</th>
                                            <th>Total Denda</th>     
                                            <th>Aksi</th>    
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $row['periode'] ?></td>
                                                <td><?= $row['total_peminjaman'] ?></td>
                                                <td><?= $row['total_pengembalian'] ?></td>
                                                <td><?= number_format($row['total_denda'], 2) ?></td>
                                                <td>
                                                    <div class='d-flex justify-content-center gap-2'>
                                                        <form method="POST" action="hapus_laporan.php">
                                                            <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                                                            <button type="submit" class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">Hapus</button>
                                                        </form>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
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


