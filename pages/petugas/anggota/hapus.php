<?php
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Pastikan ada parameter id_user yang dikirim
if (isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    // Mulai transaksi untuk memastikan data dihapus di kedua tabel
    $conn->begin_transaction();

    try {
        // Query untuk menghapus data anggota dari tabel anggota
        $sql_anggota = "DELETE FROM anggota WHERE id_user = ?";
        $stmt_anggota = $conn->prepare($sql_anggota);
        $stmt_anggota->bind_param("i", $id_user);
        $stmt_anggota->execute();

        // Query untuk menghapus data anggota dari tabel users
        $sql_users = "DELETE FROM users WHERE id_user = ?";
        $stmt_users = $conn->prepare($sql_users);
        $stmt_users->bind_param("i", $id_user);
        $stmt_users->execute();

        // Commit transaksi jika kedua query berhasil
        $conn->commit();

        $_SESSION['success'] = "Data anggota berhasil dihapus!";
    } catch (Exception $e) {
        // Rollback transaksi jika ada kesalahan
        $conn->rollback();
        $_SESSION['error'] = "Gagal menghapus anggota: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID Anggota tidak ditemukan.";
}

// Redirect ke halaman daftar anggota
header("Location: index.php");
exit();
$conn->close();
?>
