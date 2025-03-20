<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
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

// Insert data peminjaman
$query = "INSERT INTO peminjaman (id_anggota, id_user, tanggal_pinjam, tanggal_kembali, kode_peminjaman) 
          VALUES ('$id_anggota', '$id_user', '$tanggal_pinjam', '$tanggal_kembali', '$kode_peminjaman')";
mysqli_query($conn, $query);
$id_peminjaman = mysqli_insert_id($conn);  // Ambil ID peminjaman yang baru saja dimasukkan

// Insert data detail peminjaman
for ($i = 0; $i < count($buku); $i++) {
    $id_buku = $buku[$i];
    $qty = $jumlah[$i];
    $query_detail = "INSERT INTO detailpeminjaman (id_peminjaman, id_buku, jumlah) 
                     VALUES ('$id_peminjaman', '$id_buku', '$qty')";
    mysqli_query($conn, $query_detail);

    // Update stok buku
    $query_stok = "UPDATE buku SET stok = stok - $qty WHERE id_buku = '$id_buku'";
    mysqli_query($conn, $query_stok);
}

header('Location: index.php'); // Redirect ke halaman utama setelah sukses
?>
