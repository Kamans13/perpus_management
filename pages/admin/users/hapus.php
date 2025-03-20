<?php
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Pastikan ada parameter id_user yang dikirim
if (isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    // Query untuk menghapus petugas
    $sql = "DELETE FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);

    // Eksekusi query
    if ($stmt->execute()) {
        $_SESSION['success'] = "Data petugas berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus petugas: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "ID petugas tidak ditemukan.";
}

// Redirect ke halaman daftar petugas
header("Location: index.php");
exit();
$conn->close();

?>
