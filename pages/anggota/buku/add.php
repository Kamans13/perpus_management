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

// Memeriksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $penerbit = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok = $_POST['stok'];
    $id_kategori = $_POST['id_kategori'];
    $id_rak = $_POST['id_rak'];

    // Query untuk menyimpan data buku ke dalam tabel
    $sql = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok, id_kategori, id_rak) 
            VALUES ('$judul', '$pengarang', '$penerbit', '$tahun_terbit','$stok', '$id_kategori', '$id_rak')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Buku berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
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
        <title>Tambah Buku</title>
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
                        <h1 class="mt-4">Buku</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Buku</li>
                            <li class="breadcrumb-item active">Tambah Buku</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Tambah Buku
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="add.php" >
                                    <div class="mb-3">
                                        <label for="judul" class="form-label">Judul Buku</label>
                                        <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul Buku" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pengarang" class="form-label">Nama Pengarang</label>
                                        <input type="text" class="form-control" id="pengarang" name="pengarang" placeholder="Masukkan nama pengarang" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="penerbit" class="form-label">Nama Penerbit</label>
                                        <input type="text" class="form-control" id="penerbit" name="penerbit" placeholder="Masukkan nama penerbit" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" placeholder="Masukkan tahun terbit" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok Buku</label>
                                        <input type="text" class="form-control" id="stok" name="stok" placeholder="Masukkan jumlah stok buku" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="id_kategori" class="form-label">Pilih Kategori</label>
                                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php
                                            // Mengambil data kategori dari database
                                            $kategori_query = "SELECT * FROM kategori";
                                            $kategori_result = mysqli_query($conn, $kategori_query);
                                            while ($kategori = mysqli_fetch_assoc($kategori_result)) {
                                                echo "<option value='" . $kategori['id_kategori'] . "'>" . $kategori['nama_kategori'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>      
                                    <div class="mb-3">
                                        <label for="id_rak">Rak Buku:</label><br>
                                        <select class="form-select" id="id_rak" name="id_rak" required>
                                            <option value="">Pilih Rak Buku</option>
                                            <?php
                                            // Mengambil data rak dari database
                                            $rak_query = "SELECT * FROM rak";
                                            $rak_result = mysqli_query($conn, $rak_query);
                                            while ($rak = mysqli_fetch_assoc($rak_result)) {
                                                echo "<option value='" . $rak['id_rak'] . "'>" . $rak['kode_rak'] . " - " . $rak['nama_rak'] ."-". $rak['lokasi_rak'] . "</option>";
                                            }
                                            ?>
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
