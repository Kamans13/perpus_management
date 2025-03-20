<?php
// Include koneksi dan session check
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil kode peminjaman dari form
    $kode_peminjaman = $_POST['kode_peminjaman'];

    // Update status peminjaman menjadi 'Dikembalikan'
    $updateQuery = "
        UPDATE peminjaman 
        SET status = 'Dikembalikan', tanggal_kembali = NOW()
        WHERE kode_peminjaman = '$kode_peminjaman' AND status != 'Dikembalikan'
    ";

    if (mysqli_query($conn, $updateQuery)) {
        // Ambil id_peminjaman yang baru saja diupdate
        $queryPeminjaman = "
            SELECT id_peminjaman, tanggal_pinjam FROM peminjaman WHERE kode_peminjaman = '$kode_peminjaman'
        ";
        $resultPeminjaman = mysqli_query($conn, $queryPeminjaman);
        $peminjaman = mysqli_fetch_assoc($resultPeminjaman);

        if ($peminjaman) {
            // Cek keterlambatan
            $tanggal_pinjam = $peminjaman['tanggal_pinjam'];
            $tanggal_kembali_sekarang = date('Y-m-d');  // tanggal sekarang (tanggal pengembalian)
            $batas_kembali = date('Y-m-d', strtotime($tanggal_pinjam . ' + 7 days'));  // batas 7 hari

            // Jika terlambat, hitung denda
            if ($tanggal_kembali_sekarang > $batas_kembali) {
                $jumlah_hari_terlambat = (strtotime($tanggal_kembali_sekarang) - strtotime($batas_kembali)) / 86400; // Hari keterlambatan
                $denda_per_hari = 1000; // Denda per hari keterlambatan

                // Hitung jumlah denda
                $jumlah_denda = $jumlah_hari_terlambat * $denda_per_hari;

                // Masukkan denda ke tabel denda
                $insertDendaQuery = "
                    INSERT INTO denda (id_peminjaman, jumlah_denda, status_pembayaran)
                    VALUES ('{$peminjaman['id_peminjaman']}', '$jumlah_denda', 'Belum Dibayar')
                ";
                mysqli_query($conn, $insertDendaQuery);
            }

            // Ambil detail buku yang dipinjam
            $queryDetail = "
                SELECT id_buku, jumlah FROM detailpeminjaman WHERE id_peminjaman = {$peminjaman['id_peminjaman']}
            ";
            $resultDetail = mysqli_query($conn, $queryDetail);

            while ($row = mysqli_fetch_assoc($resultDetail)) {
                // Perbarui stok buku setelah pengembalian
                $updateStokQuery = "
                    UPDATE buku
                    SET stok = stok + {$row['jumlah']}
                    WHERE id_buku = {$row['id_buku']}
                ";
                mysqli_query($conn, $updateStokQuery);
            }

            // Set notifikasi sukses
            $_SESSION['success'] = "Pengembalian berhasil diproses.";
            header("Location: index.php");
            exit;
        }
    } else {
        // Jika gagal
        $_SESSION['error'] = "Gagal memproses pengembalian. Silakan coba lagi.";
        header("Location: index.php");
        exit;
    }
}
?>
