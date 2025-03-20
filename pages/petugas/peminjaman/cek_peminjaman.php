<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_peminjaman = $_POST['kode_peminjaman'];
    $tanggal_pengembalian = date('Y-m-d');

    // 1. Ambil data peminjaman
    $queryPeminjaman = "SELECT * FROM peminjaman WHERE kode_peminjaman = ? AND status = 'Dipinjam'";
    $stmt = $conn->prepare($queryPeminjaman);
    $stmt->bind_param("s", $kode_peminjaman);
    $stmt->execute();
    $resultPeminjaman = $stmt->get_result();

    if ($resultPeminjaman->num_rows > 0) {
        $peminjaman = $resultPeminjaman->fetch_assoc();
        $id_peminjaman = $peminjaman['id_peminjaman'];
        $tanggal_kembali = $peminjaman['tanggal_kembali'];

        // 2. Hitung denda jika terlambat
        $dendaPerHari = 5000; // Tarif denda per hari
        $denda = 0;
        if (strtotime($tanggal_pengembalian) > strtotime($tanggal_kembali)) {
            $selisihHari = (strtotime($tanggal_pengembalian) - strtotime($tanggal_kembali)) / (60 * 60 * 24);
            $denda = $selisihHari * $dendaPerHari;
        }

        // 3. Update status peminjaman menjadi Dikembalikan
        $queryUpdatePeminjaman = "UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali = ? WHERE id_peminjaman = ?";
        $stmtUpdate = $conn->prepare($queryUpdatePeminjaman);
        $stmtUpdate->bind_param("si", $tanggal_pengembalian, $id_peminjaman);
        $stmtUpdate->execute();

        // 4. Update stok buku
        $queryDetailPeminjaman = "SELECT * FROM detailpeminjaman WHERE id_peminjaman = ?";
        $stmtDetail = $conn->prepare($queryDetailPeminjaman);
        $stmtDetail->bind_param("i", $id_peminjaman);
        $stmtDetail->execute();
        $resultDetail = $stmtDetail->get_result();

        while ($detail = $resultDetail->fetch_assoc()) {
            $id_buku = $detail['id_buku'];
            $jumlah = $detail['jumlah'];
            $queryUpdateStok = "UPDATE buku SET stok = stok + ? WHERE id_buku = ?";
            $stmtUpdateStok = $conn->prepare($queryUpdateStok);
            $stmtUpdateStok->bind_param("ii", $jumlah, $id_buku);
            $stmtUpdateStok->execute();
        }

        // 5. Simpan data denda jika ada
        if ($denda > 0) {
            $queryInsertDenda = "INSERT INTO denda (id_peminjaman, jumlah_denda, tanggal_bayar, status_pembayaran) VALUES (?, ?, NULL, 'Belum Dibayar')";
            $stmtInsertDenda = $conn->prepare($queryInsertDenda);
            $stmtInsertDenda->bind_param("id", $id_peminjaman, $denda);
            $stmtInsertDenda->execute();
        }

        echo "Pengembalian berhasil diproses.";
    } else {
        echo "Kode peminjaman tidak ditemukan atau sudah dikembalikan.";
    }
}
?>
