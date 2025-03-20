<?php
// Include koneksi database dan session
include '../loggedas.php';
session_start();

// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID rak dari URL
$id_rak = $_GET['id_rak'];

// Ambil data dari form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_rak = $_POST['kode_rak'];
    $nama_rak = $_POST['nama_rak'];
    $lokasi_rak = $_POST['lokasi_rak'];

    // Cek apakah kode_rak sudah ada di rak lain
    $check_query = "SELECT * FROM rak WHERE kode_rak = '$kode_rak' AND id_rak != '$id_rak'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Jika kode_rak sudah ada, tampilkan pesan error
        $_SESSION['error'] = "Kode rak sudah ada. Silakan gunakan kode lain.";
    } else {
        // Update data rak
        $update_query = "UPDATE rak SET kode_rak = '$kode_rak', nama_rak = '$nama_rak', lokasi_rak = '$lokasi_rak' WHERE id_rak = '$id_rak'";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Rak berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }

    // Redirect ke halaman utama rak
    header("Location: index.php");
    exit;
}
?>
