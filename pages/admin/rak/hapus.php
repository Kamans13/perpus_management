<?php
// Include koneksi database dan session
include '../loggedas.php';
session_start();

// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Memeriksa apakah tombol delete sudah ditekan
if (isset($_POST['delete']) && isset($_POST['id_rak'])) {
    $id_rak = $_POST['id_rak'];

    // Pastikan rak yang akan dihapus ada
    $check_query = "SELECT * FROM rak WHERE id_rak = '$id_rak'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Query untuk menghapus rak
        $delete_query = "DELETE FROM rak WHERE id_rak = '$id_rak'";

        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "Rak berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Rak tidak ditemukan!";
    }
} else {
    $_SESSION['error'] = "ID rak tidak ditemukan!";
}

// Redirect ke halaman daftar rak
header("Location: index.php");
exit;
?>
