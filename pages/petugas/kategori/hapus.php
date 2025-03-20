<?php
include '../loggedas.php';
session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Memeriksa apakah tombol delete sudah ditekan
if (isset($_POST['delete']) && isset($_POST['id_kategori'])) {
    $id_kategori = $_POST['id_kategori'];

    // Pastikan kategori yang akan dihapus ada
    $check_query = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Query untuk menghapus kategori
        $delete_query = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";

        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Kategori tidak ditemukan!";
    }
} else {
    $_SESSION['error'] = "ID kategori tidak ditemukan!";
}

// Redirect ke halaman daftar kategori
header("Location: index.php");
exit;
?>
