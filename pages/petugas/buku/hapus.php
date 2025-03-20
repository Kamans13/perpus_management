<?php
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Pastikan ada parameter id_buku yang dikirim
if (isset($_POST['id_buku'])) {
    $id_buku = $_POST['id_buku'];

    // Query untuk menghapus buku
    $sql = "DELETE FROM buku WHERE id_buku = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_buku);

    // Eksekusi query
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data buku berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus buku: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "ID buku tidak ditemukan.";
}

// Redirect ke halaman daftar buku
header("Location: index.php");
exit();
$conn->close();

?>
