<?php
// File include koneksi berada di sini
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $periode = "$bulan-$tahun";

    // Hitung total peminjaman
    $sqlPeminjaman = "SELECT COUNT(*) as total_peminjaman FROM peminjaman WHERE MONTH(tanggal_pinjam) = ? AND YEAR(tanggal_pinjam) = ?";
    $stmt = $conn->prepare($sqlPeminjaman);
    $stmt->bind_param("ss", $bulan, $tahun);
    $stmt->execute();
    $resultPeminjaman = $stmt->get_result()->fetch_assoc();
    $total_peminjaman = $resultPeminjaman['total_peminjaman'];

    // Hitung total pengembalian
    $sqlPengembalian = "SELECT COUNT(*) as total_pengembalian FROM peminjaman WHERE MONTH(tanggal_kembali) = ? AND YEAR(tanggal_kembali) = ? AND status = 'Dikembalikan'";
    $stmt = $conn->prepare($sqlPengembalian);
    $stmt->bind_param("ss", $bulan, $tahun);
    $stmt->execute();
    $resultPengembalian = $stmt->get_result()->fetch_assoc();
    $total_pengembalian = $resultPengembalian['total_pengembalian'];

    // Hitung total denda
    $sqlDenda = "SELECT SUM(jumlah_denda) as total_denda FROM denda WHERE MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?";
    $stmt = $conn->prepare($sqlDenda);
    $stmt->bind_param("ss", $bulan, $tahun);
    $stmt->execute();
    $resultDenda = $stmt->get_result()->fetch_assoc();
    $total_denda = $resultDenda['total_denda'] ?? 0;

    // Simpan ke tabel laporan
    $sqlInsert = "INSERT INTO laporan (periode, total_peminjaman, total_pengembalian, total_denda) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("siii", $periode, $total_peminjaman, $total_pengembalian, $total_denda);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Laporan untuk periode $periode berhasil dibuat!";
    } else {
        $_SESSION['error'] = "Gagal membuat laporan: " . $stmt->error;
    }

    header("Location: index.php");
    exit;
}
?>
