<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

//fitur sapa
    // Ambil nama pengguna
    $id_user = $_SESSION['id_user'];
    $username = getUserName($id_user);

    // Menampilkan waktu sesuai dengan jam
    $waktu = getWaktu();


// Query untuk mengambil data peminjaman
$query_peminjaman = "SELECT p.id_peminjaman,
                            p.kode_peminjaman, 
                            a.nama AS peminjam, 
                            p.tanggal_pinjam, 
                            p.tanggal_kembali, 
                            p.status
                     FROM peminjaman p
                     JOIN anggota a ON p.id_anggota = a.id_anggota
                      WHERE  p.status= 'dipinjam'";

$result_peminjaman = mysqli_query($conn, $query_peminjaman);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Peminjaman</title>
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
                        <h1 class="mt-4">Daftar Peminjaman</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Peminjaman</li>
                            <li class="breadcrumb-item active">Daftar Peminjaman</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-table me-1"></i>
                                    DataTable Peminjaman
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
                                            <th>Kode Peminjaman</th>
                                            <th>Peminjam</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Batas Pengembalian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Peminjaman</th>
                                            <th>Peminjam</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Batas Pengembalian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php 
                                        $no = 1; // Inisialisasi nomor urut
                                        if (mysqli_num_rows($result_peminjaman) > 0) {
                                            while ($row = mysqli_fetch_assoc($result_peminjaman)) {
                                                $kode_peminjaman = $row['kode_peminjaman'];
                                                $id_peminjaman = $row['id_peminjaman']; // Pastikan ini ada di hasil query
                                                echo "<tr>
                                                        <td>{$no}</td>
                                                        <td>" . $row['kode_peminjaman'] . "</td>
                                                        <td>" . $row['peminjam'] . "</td>
                                                        <td>" . $row['tanggal_pinjam'] . "</td>
                                                        <td>" . $row['tanggal_kembali'] . "</td>
                                                        <td>" . $row['status'] . "</td>
                                                        <td>
                                                            <div class='d-flex justify-content-center gap-2'>
                                                                <form method='POST' action='kembalikan.php' style='display:inline;'>
                                                                    <input type='hidden' name='id_peminjaman' value='{$row['id_peminjaman']}'>
                                                                    <button type='submit'  class='btn btn-warning btn-sm' onclick=\"return confirm('Yakin ingin mengembalikkan peminjaman ". $row['kode_peminjaman'] ." ini?');\">
                                                                    <i class='fas fa-sync'></i> Return
                                                                    </button>
                                                                </form>
                                                               
                                                                <a href='detail_peminjaman.php?kode_peminjaman={$kode_peminjaman}'class='btn btn-primary btn-sm'><i class='far fa-eye'></i> Detail </a>
                                                            </div>    
                                                        </td>
                                                    </tr>";
                                                $no++; // Increment nomor urut
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>Tidak ada peminjaman.</td></tr>";
                                        }
                                        ?>
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
