<?php 
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
    exit;
}

// Fitur sapa
$id_user = $_SESSION['id_user'];
$username = getUserName($id_user);
$waktu = getWaktu();

// Query untuk mendapatkan data denda, anggota, dan petugas
$sql = "SELECT 
            d.id_denda, 
            d.jumlah_denda, 
            d.tanggal_bayar, 
            d.status_pembayaran, 
            a.nama AS nama_anggota, 
            a.alamat AS alamat_anggota, 
            a.nomor_hp AS nomor_hp_anggota,
            p.kode_peminjaman AS kode_pmj,
            u.username AS username_petugas
        FROM 
            denda d
        JOIN 
            peminjaman p ON d.id_peminjaman = p.id_peminjaman
        JOIN 
            anggota a ON p.id_anggota = a.id_anggota
        JOIN 
            users u ON p.id_user = u.id_user
        ORDER BY 
            d.id_denda DESC";

// Menjalankan query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman dan Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<?php include '../includes/topnav.php' ?>

<div id="layoutSidenav">
    <?php include '../includes/sidebar.php' ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Daftar Denda</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Denda</li>
                        <li class="breadcrumb-item active">Daftar Denda</li>
                    </ol>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-table me-1"></i>
                            Daftar Denda
                        </div>
                        <a href="add.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Refresh
                        </a>
                    </div>

                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Peminjaman</th>
                                    <th>Nama</th>
                                    <th>HP</th>
                                    <th>Jumlah</th>
                                    <th>Status Pembayaran</th>
                                    <th>Tanggal Bayar</th>                                    
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Peminjaman</th>
                                    <th>Nama </th>
                                    <th>HP </th>
                                    <th>Jumlah</th>
                                    <th>Status Pembayaran</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            $no = 1; // Inisialisasi nomor urut

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Format jumlah denda
                                    $jumlah_denda = number_format($row["jumlah_denda"], 0, ',', '.');

                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>" . $row["kode_pmj"] . "</td>                                            
                                            <td>" . $row["nama_anggota"] . "</td>
                                            <td>" . $row["nomor_hp_anggota"] . "</td>
                                            <td>" . $jumlah_denda ."</td>
                                            <td>" . $row["status_pembayaran"] . "</td>   
                                            <td>" . ($row["tanggal_bayar"] ? $row["tanggal_bayar"] : '-') . "</td>
                                            <td>" . $row["username_petugas"] . "</td>
                                            <td>
                                                <div class='d-flex justify-content-center gap-2'>
                                                    <a href='detail.php?id_denda=" . $row['id_denda'] . "' class='btn btn-info btn-sm'>
                                                        <i class='fas fa-eye'></i> Detail
                                                    </a>
                                                </div>
                                            </td>    
                                    </tr>";
                                    $no++; // Increment nomor urut
                                }
                            } else {
                                echo "<tr><td colspan='9'>Tidak ada denda yang belum dibayar.</td></tr>";
                            }

                            $conn->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../includes/footer.php' ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../../../js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="../../../js/datatables-simple-demo.js"></script>
</body>
</html>
