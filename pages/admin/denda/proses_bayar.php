<?php
include '../loggedas.php';
session_start(); // Mulai sesi untuk menggunakan notifikasi

// Pastikan parameter id_denda ada
if (!isset($_POST['id_denda'])) {
    $_SESSION['error'] = "ID denda tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$id_denda = $_POST['id_denda'];

// Cek apakah denda sudah dibayar
$query_check = "SELECT d.status_pembayaran, p.id_peminjaman 
                FROM denda d
                JOIN peminjaman p ON d.id_peminjaman = p.id_peminjaman
                WHERE d.id_denda = '$id_denda'";
$result_check = mysqli_query($conn, $query_check);

if ($result_check && mysqli_num_rows($result_check) > 0) {
    $denda_check = mysqli_fetch_assoc($result_check);

    if ($denda_check['status_pembayaran'] == 'Sudah Dibayar') {
        $_SESSION['error'] = "Denda sudah dibayar, tidak dapat diproses lagi.";
        header("Location: index.php");
        exit;
    }

    // Update status pembayaran dan tanggal bayar
    $tanggal_bayar = date('Y-m-d'); // Tanggal saat ini
    $query_update_denda = "UPDATE denda 
                           SET status_pembayaran = 'Sudah Dibayar', tanggal_bayar = '$tanggal_bayar' 
                           WHERE id_denda = '$id_denda'";

    if (mysqli_query($conn, $query_update_denda)) {
        // Ambil ID peminjaman terkait
        $id_peminjaman = $denda_check['id_peminjaman'];

        // Update status peminjaman menjadi "Dikembalikan"
        $query_update_peminjaman = "UPDATE peminjaman 
                                    SET status = 'Dikembalikan'
                                    WHERE id_peminjaman = '$id_peminjaman'";
        
        if (mysqli_query($conn, $query_update_peminjaman)) {
            $_SESSION['success'] = "Pembayaran denda berhasil diproses.";
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal mengubah status peminjaman: " . mysqli_error($conn);
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Gagal memproses pembayaran denda: " . mysqli_error($conn);
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Denda tidak ditemukan.";
    header("Location: index.php");
    exit;
}

mysqli_close($conn);
?>
