<?php
include '../../loggedas.php';

session_start();

// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['id_peminjaman']) && isset($_POST['jumlah_denda'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $jumlah_denda = $_POST['jumlah_denda'];

    // Update status pembayaran denda menjadi 'Dibayar'
    $update_denda = "UPDATE denda SET status_pembayaran = 'Dibayar' WHERE id_peminjaman = '$id_peminjaman' AND jumlah_denda = '$jumlah_denda'";
    $result = mysqli_query($conn, $update_denda);

    if ($result) {
        // Set notifikasi sukses
        $_SESSION['success'] = "Status pembayaran denda berhasil diperbarui.";
        header("Location: index.php");
        exit;
    } else {
        // Set notifikasi gagal
        $_SESSION['error'] = "Gagal memperbarui status pembayaran denda. Silakan coba lagi.";
        header("Location: index.php");
        exit;
    }
}
?>
