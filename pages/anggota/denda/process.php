<?php
include '../../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_POST['id_peminjaman']) && isset($_POST['jumlah_denda'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $jumlah_denda = $_POST['jumlah_denda'];

    // Update status pembayaran denda menjadi 'Dibayar'
    $update_denda = "UPDATE denda SET status_pembayaran = 'Dibayar' WHERE id_peminjaman = '$id_peminjaman' AND jumlah_denda = '$jumlah_denda'";
    $result = mysqli_query($conn, $update_denda);

    if ($result) {
        // Redirect ke halaman denda.php dengan status sukses
        header("Location: index.php?kode_peminjaman=$id_peminjaman&status=success");
    } else {
        // Redirect ke halaman denda.php dengan status gagal
        header("Location: index.php?kode_peminjaman=$id_peminjaman&status=failed");
    }
}
?>
