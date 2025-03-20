<?php 
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


//pengembalian
$kode_peminjaman = '';
$peminjaman = null;
$detailBuku = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil kode peminjaman dari form
    $kode_peminjaman = $_POST['kode_peminjaman'];

    // Ambil data peminjaman berdasarkan kode_peminjaman
    $query = "
        SELECT peminjaman.*, anggota.nama
        FROM peminjaman
        JOIN anggota ON peminjaman.id_anggota = anggota.id_anggota
        WHERE peminjaman.kode_peminjaman = '$kode_peminjaman' AND peminjaman.status != 'Dikembalikan'
    ";
    $result = mysqli_query($conn, $query);
    $peminjaman = mysqli_fetch_assoc($result);

    // Ambil detail buku yang dipinjam (judul dan jumlah buku)
    if ($peminjaman) {
        $queryDetail = "
            SELECT buku.judul, detailpeminjaman.jumlah
            FROM detailpeminjaman
            JOIN buku ON detailpeminjaman.id_buku = buku.id_buku
            WHERE detailpeminjaman.id_peminjaman = {$peminjaman['id_peminjaman']}
        ";
        $resultDetail = mysqli_query($conn, $queryDetail);
        while ($row = mysqli_fetch_assoc($resultDetail)) {
            $detailBuku[] = $row;
        }
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
        <title>Tambah Kategori Buku</title>
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
                        <h1 class="mt-4">Pengembalian Buku</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Buku</li>
                            <li class="breadcrumb-item active">Pengembalian Buku</li>   
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-wpforms me-1"></i>
                                    Form Pengembalian Buku
                                </div>
                            </div>

                            <!-- body content -->
                            <div class="card-body">
                                <form method="post" action="">
                                    <label for="kode_peminjaman">Kode Peminjaman:</label>
                                    <input type="text" id="kode_peminjaman" name="kode_peminjaman" value="<?= htmlspecialchars($kode_peminjaman) ?>" required>
                                    <button type="submit">Cari</button>
                                </form>

                                <?php if ($peminjaman): ?>
                                    <h3>Detail Peminjaman</h3>
                                    <p><strong>Nama Anggota:</strong> <?= htmlspecialchars($peminjaman['nama']) ?></p>
                                    <p><strong>Status:</strong> <?= htmlspecialchars($peminjaman['status']) ?></p>

                                    <table id="datatablesSimple">
                                    <h3>Detail Buku yang Dipinjam</h3>
                                        <thead>
                                            <tr>
                                                <th>Judul Buku</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detailBuku as $buku): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($buku['judul']) ?></td>
                                                    <td><?= htmlspecialchars($buku['jumlah']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <form action="process.php" method="post">
                                        <input type="hidden" name="kode_peminjaman" value="<?= htmlspecialchars($peminjaman['kode_peminjaman']) ?>">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-success" type="submit">Kembalikan Buku</button>
                                            <a href="index.php" class="btn btn-danger">Kembali</a>
                                        </div>
                                    </form>

                                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                                    <p><strong>Peminjaman tidak ditemukan atau sudah dikembalikan.</strong></p>
                                <?php endif; ?>
                                
                                <?php if (isset($_GET['status'])): ?>
                                    <?php if ($_GET['status'] == 'success'): ?>
                                        <p style="color: green;">Pengembalian buku berhasil!</p>
                                    <?php elseif ($_GET['status'] == 'failed'): ?>
                                        <p style="color: red;">Pengembalian buku gagal, coba lagi.</p>
                                    <?php endif; ?>
                                <?php endif; ?>

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
