<?php
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['kode_peminjaman'])) {
    die("Kode peminjaman tidak ditemukan.");
}

$kode_peminjaman = $_GET['kode_peminjaman'];

// Query untuk mengambil detail pengembalian berdasarkan kode_peminjaman
$query_pengembalian = "SELECT p.kode_peminjaman, a.nama AS peminjam, p.tanggal_kembali, p.status
                       FROM peminjaman p
                       JOIN anggota a ON p.id_anggota = a.id_anggota
                       WHERE p.kode_peminjaman = '$kode_peminjaman'";

$result_pengembalian = mysqli_query($conn, $query_pengembalian);
$pengembalian = mysqli_fetch_assoc($result_pengembalian);

// Query untuk mengambil daftar buku yang dikembalikan berdasarkan kode_peminjaman
$query_detail = "SELECT b.judul, b.pengarang, b.penerbit, b.tahun_terbit, k.nama_kategori, dp.jumlah
                 FROM detailpeminjaman dp
                 JOIN buku b ON dp.id_buku = b.id_buku
                 JOIN kategori k ON b.id_kategori = k.id_kategori
                 WHERE dp.id_peminjaman = (SELECT id_peminjaman FROM peminjaman WHERE kode_peminjaman = '$kode_peminjaman')";

$result_detail = mysqli_query($conn, $query_detail);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pengembalian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .receipt {
            width: 400px;
            padding: 10px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .receipt h3 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .receipt p {
            margin: 5px 0;
            font-size: 14px;
        }
        .receipt table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .receipt table th, .receipt table td {
            padding: 5px;
            font-size: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .receipt .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            color: #888;
        }
        .receipt .total {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="receipt">
    <h3>Struk Pengembalian</h3>
    <p><strong>Kode Peminjaman:</strong> <?php echo $pengembalian['kode_peminjaman']; ?></p>
    <p><strong>Peminjam:</strong> <?php echo $pengembalian['peminjam']; ?></p>
    <p><strong>Tanggal Kembali:</strong> <?php echo $pengembalian['tanggal_kembali']; ?></p>
    <p><strong>Status:</strong> <?php echo $pengembalian['status']; ?></p>

    <h4>Buku yang Dikembalikan:</h4>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Kategori</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result_detail) > 0) {
                while ($row = mysqli_fetch_assoc($result_detail)) {
                    echo "<tr>
                            <td>" . $row['judul'] . "</td>
                            <td>" . $row['pengarang'] . "</td>
                            <td>" . $row['penerbit'] . "</td>
                            <td>" . $row['tahun_terbit'] . "</td>
                            <td>" . $row['nama_kategori'] . "</td>
                            <td>" . $row['jumlah'] . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada buku yang dikembalikan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Terima kasih atas kerjasama Anda!</p>
        <p>PerpusManager</p>
        <p>By Kaman Septia </p>
        <p>NIM : 17230312</p>
    </div>
</div>

</body>
</html>
