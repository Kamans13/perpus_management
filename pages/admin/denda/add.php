<?php 
include '../loggedas.php';
session_start(); // Mulai sesi untuk menggunakan notifikasi

// Ambil data peminjaman yang terlambat
$query = "
    SELECT p.id_peminjaman, p.kode_peminjaman, p.tanggal_kembali, DATEDIFF(NOW(), p.tanggal_kembali) AS terlambat
    FROM peminjaman p
    WHERE p.status = 'Dipinjam' AND p.tanggal_kembali < NOW()
";

$result = mysqli_query($conn, $query);

// Pastikan query berhasil
if (!$result) {
    $_SESSION['error'] = 'Query gagal: ' . mysqli_error($conn);
    header("Location: index.php"); // Sesuaikan halaman tujuan jika perlu
    exit;
}

// Proses setiap peminjaman yang terlambat
while ($row = mysqli_fetch_assoc($result)) {
    $id_peminjaman = $row['id_peminjaman'];
    $terlambat_hari = $row['terlambat'];
    
    // Pastikan hanya memproses peminjaman yang terlambat
    if ($terlambat_hari > 0) {
        // Hitung denda per hari (misalnya 1000 per hari)
        $denda = $terlambat_hari * 1000;
        
        // Masukkan data denda ke tabel denda
        $insert_denda = "
            INSERT INTO denda (id_peminjaman, jumlah_denda, tanggal_bayar, status_pembayaran)
            VALUES ($id_peminjaman, $denda, NULL, 'Belum Dibayar')
        ";
        
        if (mysqli_query($conn, $insert_denda)) {
            // Berhasil menambahkan denda
            $_SESSION['success'] = "Denda untuk peminjaman {$row['kode_peminjaman']} berhasil ditambahkan sebesar Rp. " . number_format($denda, 0, ',', '.') . ".";
            
            // Update status peminjaman menjadi 'Denda'
            $update_status = "UPDATE peminjaman SET status = 'Denda' WHERE id_peminjaman = $id_peminjaman";
            if (mysqli_query($conn, $update_status)) {
                $_SESSION['success'] .= " Data Denda baru berhasil ditambahkan ";
            } else {
                $_SESSION['error'] = "Gagal mengubah status peminjaman {$row['kode_peminjaman']}.";
            }
        } else {
            $_SESSION['error'] = "Gagal menambahkan denda untuk peminjaman {$row['kode_peminjaman']}.";
        }
    }
}

// Setelah proses selesai, alihkan kembali ke halaman sebelumnya atau halaman yang diinginkan
header("Location: index.php"); // Sesuaikan halaman tujuan jika perlu
exit;
?>
