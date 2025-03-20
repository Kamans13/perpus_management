<?php
include '../loggedas.php';

// Pastikan parameter id_denda ada
if (!isset($_GET['id_denda'])) {
    die("ID denda tidak ditemukan.");
}

$id_denda = $_GET['id_denda'];

// Query untuk mengambil detail denda
$query_denda = "SELECT d.id_denda, d.jumlah_denda, d.tanggal_bayar, d.status_pembayaran,
                       p.kode_peminjaman, a.nama AS peminjam, p.tanggal_pinjam, p.tanggal_kembali, p.status
                FROM denda d
                JOIN peminjaman p ON d.id_peminjaman = p.id_peminjaman
                JOIN anggota a ON p.id_anggota = a.id_anggota
                WHERE d.id_denda = '$id_denda'";

$result_denda = mysqli_query($conn, $query_denda);
$denda = mysqli_fetch_assoc($result_denda);

// Jika denda tidak ditemukan
if (!$denda) {
    die("Detail denda tidak ditemukan.");
}

// Query untuk mengambil daftar buku yang dipinjam
$query_detail = "SELECT b.judul, b.pengarang, b.penerbit, b.tahun_terbit, k.nama_kategori, dp.jumlah
                 FROM detailpeminjaman dp
                 JOIN buku b ON dp.id_buku = b.id_buku
                 JOIN kategori k ON b.id_kategori = k.id_kategori
                 WHERE dp.id_peminjaman = (SELECT id_peminjaman FROM peminjaman WHERE kode_peminjaman = '" . $denda['kode_peminjaman'] . "')";

$result_detail = mysqli_query($conn, $query_detail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Peminjaman</title>
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
        .receipt .denda {
            margin-top: 15px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="receipt">
    <h3>Struk Peminjaman</h3>
    <p><strong>Kode Peminjaman:</strong> <?php echo $denda['kode_peminjaman']; ?></p>
    <p><strong>Peminjam:</strong> <?php echo $denda['peminjam']; ?></p>
    <p><strong>Tanggal Pinjam:</strong> <?php echo $denda['tanggal_pinjam']; ?></p>
    <p><strong>Tanggal Kembali:</strong> <?php echo $denda['tanggal_kembali']; ?></p>
    <p><strong>Status:</strong> <?php echo $denda['status']; ?></p>

    <h4>Buku yang Dipinjam:</h4>
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
                echo "<tr><td colspan='6'>Tidak ada buku dipinjam.</td></tr>";
            }
            ?>
        </tbody>
    </table>


    <div class="denda">
        <p><strong>Denda:</strong></p>
        <p><strong>Jumlah Denda:</strong> Rp <?php echo number_format($denda['jumlah_denda'], 0, ',', '.'); ?></p>
        <p><strong>Status Pembayaran:</strong> <?php echo $denda['status_pembayaran']; ?></p>
        <p><strong>Tanggal Bayar:</strong> <?php echo $denda['tanggal_bayar'] ? $denda['tanggal_bayar'] : 'Belum Dibayar'; ?></p>

        <!-- Form Pembayaran -->
        <?php if ($denda['status_pembayaran'] == 'Belum Dibayar') { ?>
            <form action="proses_bayar.php" method="post">
                <input type="hidden" name="id_denda" value="<?php echo $denda['id_denda']; ?>">
                <button type="submit" class="btn btn-success">Bayar Denda</button>
            </form>
        <?php } else { ?>
            <p>Denda sudah dibayar.</p>
        <?php } ?>
    </div>


    <div class="footer">
        <p>Terima kasih telah meminjam buku di perpustakaan kami!</p>
        <p>PerpusManager</p>
        <p>By Kaman Septia </p>
        <p>NIM : 17230312</p>
    </div>
</div>

</body>
</html>
