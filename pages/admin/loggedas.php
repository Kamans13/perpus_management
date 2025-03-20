<?php

include 'C:/xampp/htdocs/perpustakaan/includes/koneksi.php';

// Setel timezone ke GMT+7 (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mendapatkan waktu (pagi, siang, sore, malam)
function getWaktu() {
    $jam = date("H"); // Mendapatkan jam saat ini (0-23)
    
    // Menentukan waktu berdasarkan jam
    if ($jam >= 5 && $jam < 12) {
        return "Pagi";
    } elseif ($jam >= 12 && $jam < 15) {
        return "Siang";
    } elseif ($jam >= 15 && $jam < 18) {
        return "Sore";
    } else {
        return "Malam";
    }
}

function getUserName($id_user) {
    global $conn;
    
    // Membuat SQL untuk mengambil username berdasarkan id_user
    $sql = "SELECT u.username 
            FROM users u
            WHERE u.id_user = ?";
    
    // Persiapkan dan jalankan query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();

        // Periksa hasil query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['username']; // Kembalikan username dari tabel users
        } else {
            return "Unknown User"; // Jika tidak ditemukan data
        }
    } else {
        return "Query preparation failed: " . $conn->error; // Menampilkan error jika query gagal disiapkan
    }
}
?>