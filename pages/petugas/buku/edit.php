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


// Mendapatkan ID buku dari URL
$id_buku = $_GET['id_buku'] ?? null;

if ($id_buku) {
    // Ambil data buku berdasarkan ID
    $sql = "SELECT * FROM buku WHERE id_buku = $id_buku";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $buku = $result->fetch_assoc();
    } else {
        echo "Buku tidak ditemukan.";
        exit;
    }
} else {
    echo "ID buku tidak tersedia.";
    exit;
}

// Query untuk mengambil data rak dan kategori
$sql_rak = "SELECT * FROM rak";
$result_rak = $conn->query($sql_rak);

$sql_kategori = "SELECT * FROM kategori";
$result_kategori = $conn->query($sql_kategori);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Edit Buku</title>
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
                        <h1 class="mt-4">Buku</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Buku</li>
                            <li class="breadcrumb-item active">Edit Buku</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Edit Buku
                                </div>
                            </div>

                            <div class="card-body">
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger">
                                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                        </div>
                                <?php endif; ?>
                                <form method="POST" action="update.php" >
                                    <div class="mb-3">
                                    <input type="hidden" name="id_buku" value="<?= $buku['id_buku']; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="judul" class="form-label">Judul Buku</label>
                                        <input type="text" class="form-control" id="judul" name="judul" value="<?php echo $buku['judul']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pengarang" class="form-label">Nama Pengarang</label>
                                        <input type="text" class="form-control" id="pengarang" name="pengarang" value="<?php echo $buku['pengarang']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="penerbit" class="form-label">Nama Penerbit</label>
                                        <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo $buku['penerbit']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                        <input type="text" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?php echo $buku['tahun_terbit']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok Buku</label>
                                        <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $buku['stok']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="id_kategori" class="form-label">Pilih Kategori</label>
                                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php
                                                // Menampilkan kategori dari database
                                                while ($kategori = $result_kategori->fetch_assoc()) {
                                                    $selected = ($kategori['id_kategori'] == $buku['id_kategori']) ? 'selected' : '';
                                                    echo "<option value='" . $kategori['id_kategori'] . "' $selected>" . $kategori['nama_kategori'] . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>      
                                    <div class="mb-3">
                                        <label for="id_rak">Rak Buku:</label><br>
                                        <select class="form-select" name="id_rak" id="id_rak" required>
                                            <option value="">Pilih Rak</option>
                                                <?php
                                                  // Ambil data rak dari database
                                                    $result = $conn->query("SELECT id_rak, nama_rak FROM rak");
                                                    while ($row = $result->fetch_assoc()) {
                                                        $selected = $row['id_rak'] == $buku['id_rak'] ? 'selected' : '';
                                                        echo "<option value='{$row['id_rak']}' $selected>{$row['nama_rak']}</option>";
                                                    }
                                                ?>
                                            
                                        </select>
                                    </div>      
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-primary">Update</button>
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
