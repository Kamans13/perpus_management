<?php
include '../loggedas.php';
session_start();

// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Pastikan ID kategori disertakan dalam URL
if (isset($_GET['id_kategori'])) {
    $id_kategori = $_GET['id_kategori'];

    // Ambil data kategori dari database
    $sql = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori'";
    $result = mysqli_query($conn, $sql);
    $kategori = mysqli_fetch_assoc($result);

    if (!$kategori) {
        $_SESSION['error'] = "Kategori tidak ditemukan!";
        header("Location: index.php");
        exit;
    }
}

// Memeriksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = $_POST['nama_kategori'];

    // Query untuk memperbarui kategori
    $sql = "UPDATE kategori SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Kategori berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    header("Location: index.php");
    exit;
}

// Memanggil form edit
include 'edit.php';
?>
