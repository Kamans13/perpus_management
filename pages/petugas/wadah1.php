<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_perpus";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Data untuk tabel peminjaman
$id_anggota = 9;
$id_petugas = 2;
$tanggal_pinjam = '2025-01-14';
$tanggal_kembali = '2025-01-21';
$status = 'Dipinjam';

// Insert ke tabel peminjaman
$sql_peminjaman = "INSERT INTO peminjaman (id_anggota, id_petugas, tanggal_pinjam, tanggal_kembali, status)
                   VALUES ($id_anggota, $id_petugas, '$tanggal_pinjam', '$tanggal_kembali', '$status')";

if ($conn->query($sql_peminjaman)) {
    // Ambil ID peminjaman yang baru ditambahkan
    $id_peminjaman = $conn->insert_id;

    // Data untuk buku yang dipinjam
    $books = [
        ['id_buku' => 1, 'jumlah' => 3], // Buku 1, jumlah 2
        ['id_buku' => 2, 'jumlah' => 3], // Buku 2, jumlah 1
        ['id_buku' => 3, 'jumlah' => 3]  // Buku 3, jumlah 3
    ];

    // Loop untuk insert detail peminjaman
    foreach ($books as $book) {
        $id_buku = $book['id_buku'];
        $jumlah = $book['jumlah'];

        // Insert data detail peminjaman ke tabel detailpeminjaman
        $sql_detail = "INSERT INTO detailpeminjaman (id_peminjaman, id_buku, jumlah)
                       VALUES ($id_peminjaman, $id_buku, $jumlah)";

        if (!$conn->query($sql_detail)) {
            echo "Error saat menyimpan ke tabel detailpeminjaman: " . $conn->error;
        }
    }

    echo "Peminjaman berhasil disimpan dengan ID peminjaman $id_peminjaman.<br>";
} else {
    echo "Error saat menyimpan ke tabel peminjaman: " . $conn->error;
}

// Tutup koneksi
$conn->close();
?>
