<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data dari form
$id_anggota = $_POST['id_anggota'];
$id_user = $_POST['id_user'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_kembali = $_POST['tanggal_kembali'];
$kode_peminjaman = $_POST['kode_peminjaman'];
$buku = $_POST['buku'];
$jumlah = $_POST['jumlah'];

try {
    // Insert data peminjaman
    $query = "INSERT INTO peminjaman (id_anggota, id_user, tanggal_pinjam, tanggal_kembali, kode_peminjaman) 
              VALUES ('$id_anggota', '$id_user', '$tanggal_pinjam', '$tanggal_kembali', '$kode_peminjaman')";
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Gagal menambahkan data peminjaman: " . mysqli_error($conn));
    }
    $id_peminjaman = mysqli_insert_id($conn);  // Ambil ID peminjaman yang baru saja dimasukkan

    // Insert data detail peminjaman
    for ($i = 0; $i < count($buku); $i++) {
        $id_buku = $buku[$i];
        $qty = $jumlah[$i];
        $query_detail = "INSERT INTO detailpeminjaman (id_peminjaman, id_buku, jumlah) 
                         VALUES ('$id_peminjaman', '$id_buku', '$qty')";
        if (!mysqli_query($conn, $query_detail)) {
            throw new Exception("Gagal menambahkan detail peminjaman: " . mysqli_error($conn));
        }

        // Update stok buku
        $query_stok = "UPDATE buku SET stok = stok - $qty WHERE id_buku = '$id_buku'";
        if (!mysqli_query($conn, $query_stok)) {
            throw new Exception("Gagal mengupdate stok buku: " . mysqli_error($conn));
        }
    }

    $_SESSION['success'] = "Data peminjaman berhasil ditambahkan!";
    header('Location: index.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php');
    exit;
}
?>
