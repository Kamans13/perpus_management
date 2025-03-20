<?php
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
    exit;
}

// Periksa apakah kode_peminjaman dikirim

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_peminjaman'])) {
    $id_peminjaman = $_POST['id_peminjaman'];

    // Query untuk mengupdate status
    $query_update = "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_peminjaman = '$id_peminjaman'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success'] = "Buku berhasil dikembalikan!";
    } else {
        $_SESSION['error'] = "Gagal mengembalikan buku: " . mysqli_error($conn);
    }

    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = "ID peminjaman tidak ditemukan.";
    header("Location: index.php");
    exit;
}

mysqli_close($conn);
?>
