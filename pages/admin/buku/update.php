<?php
// Menghubungkan ke database
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}


//update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = $_POST['id_buku'] ?? '';
    $judul = $_POST['judul'] ?? '';
    $pengarang = $_POST['pengarang'] ?? '';
    $penerbit = $_POST['penerbit'] ?? '';
    $tahun_terbit = $_POST['tahun_terbit'] ?? '';
    $id_kategori = $_POST['id_kategori'] ?? '';
    $stok = $_POST['stok'] ?? '';
    $id_rak = $_POST['id_rak'] ?? '';

    // Validasi data
    if (empty($id_buku) || empty($judul) || empty($pengarang) || empty($penerbit) || 
        empty($tahun_terbit) || empty($id_kategori) || empty($stok) || empty($id_rak)) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: edit.php?id=$id_buku");
        exit;
    }

    // Query update
    $stmt = $conn->prepare("UPDATE buku SET 
        judul = ?, pengarang = ?, penerbit = ?, tahun_terbit = ?, 
        id_kategori = ?, stok = ?, id_rak = ? WHERE id_buku = ?");
    $stmt->bind_param(
        "ssssiisi",
        $judul, $pengarang, $penerbit, $tahun_terbit, $id_kategori, $stok, $id_rak, $id_buku
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data buku berhasil diupdate.";
    } else {
        $_SESSION['error'] = "Gagal mengupdate data buku.";
    }
    $stmt->close();
    header("Location: index.php");
    exit;
}
$conn->close();

?>


