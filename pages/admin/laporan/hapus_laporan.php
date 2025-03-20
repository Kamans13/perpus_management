<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'db_perpus');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

session_start();

// Periksa apakah ID laporan dikirimkan
if (isset($_POST['id_laporan'])) {
    $id_laporan = $_POST['id_laporan'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Hapus detail laporan yang terkait
        $deleteDetails = $conn->prepare("DELETE FROM detailpeminjaman WHERE id_peminjaman IN (
            SELECT id_peminjaman FROM peminjaman WHERE kode_peminjaman IN (
                SELECT periode FROM laporan WHERE id_laporan = ?
            )
        )");
        $deleteDetails->bind_param('i', $id_laporan);
        $deleteDetails->execute();

        // Hapus denda terkait (jika ada)
        $deleteDenda = $conn->prepare("DELETE FROM denda WHERE id_peminjaman IN (
            SELECT id_peminjaman FROM peminjaman WHERE kode_peminjaman IN (
                SELECT periode FROM laporan WHERE id_laporan = ?
            )
        )");
        $deleteDenda->bind_param('i', $id_laporan);
        $deleteDenda->execute();

        // Hapus laporan utama
        $deleteLaporan = $conn->prepare("DELETE FROM laporan WHERE id_laporan = ?");
        $deleteLaporan->bind_param('i', $id_laporan);
        $deleteLaporan->execute();

        // Commit transaksi
        $conn->commit();

        // Set notifikasi sukses
        $_SESSION['success'] = "Laporan berhasil dihapus!";
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $conn->rollback();

        // Set notifikasi error
        $_SESSION['error'] = "Gagal menghapus laporan: " . $e->getMessage();
    }
} else {
    // Set notifikasi error jika ID laporan tidak ditemukan
    $_SESSION['error'] = "ID laporan tidak ditemukan!";
}

// Redirect ke halaman daftar laporan
header("Location: index.php");
exit;
?>
