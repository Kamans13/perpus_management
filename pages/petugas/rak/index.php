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

// Query SQL untuk mendapatkan data buku, kategori, kode rak, dan lokasi rak
$sql = "
   SELECT * FROM rak
";

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
        <title>Rak Buku</title>
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
                            <li class="breadcrumb-item active">Rak Buku</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-table me-1"></i>
                                    DataTable Buku
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
                                            <th>Kode Rak</th>
                                            <th>Nama Rak</th>
                                            <th>Lokasi Rak</th>  
                                            <th>Aksi</th>                                         
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Rak</th>
                                            <th>Nama Rak</th>
                                            <th>Lokasi Rak</th>  
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php                        
                                     $no = 1; // Inisialisasi nomor urut

                                     if ($result->num_rows > 0) {
                                         while ($row = $result->fetch_assoc()) {
                                             echo "<tr>
                                                 <td>{$no}</td>
                                                 <td>" . $row['kode_rak'] . "</td>
                                                 <td>" . $row['nama_rak'] . "</td>
                                                 <td>" . $row['lokasi_rak'] . "</td>
                                                 <td>
                                                     <div class='d-flex justify-content-center gap-2'>
                                                        <a href='edit.php?id_rak={$row['id_rak']}' class='btn btn-warning btn-sm'><i class='fas fa-pencil-square'></i> Edit </a>
                                                        <form method='POST' action='hapus.php' style='display:inline;'>
                                                            <input type='hidden' name='id_rak' value='{$row['id_rak']}'>
                                                            <button type='submit' name='delete' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus Buku ini?');\"><i class='fas fa-trash-alt'></i> Delete </button>
                                                        </form>
                                                    </div>
                                                </td>
                                             </tr>";
                                             $no++; // Increment nomor urut
                                         }
                                     } else {
                                         echo "<tr><td colspan='6'>Tidak ada Buku.</td></tr>";
                                     }
                             
                                     $conn->close();
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
