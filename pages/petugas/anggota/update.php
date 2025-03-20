
<?php
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

// Cek apakah metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_user = isset($_POST['id_user']) ? $_POST['id_user'] : '';
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
    $nomor_hp = isset($_POST['nomor_hp']) ? $_POST['nomor_hp'] : '';

    // Validasi input
    if (!empty($id_user) && !empty($nama) && !empty($username) && !empty($email) && !empty($alamat) && !empty($nomor_hp)) {
        // Query untuk update data anggota di tabel users
        $sql_users = "UPDATE users SET username = ?, email = ? WHERE id_user = ?";

        // Query untuk update data anggota di tabel anggota
        $sql_anggota = "UPDATE anggota SET nama = ?, alamat = ?, nomor_hp = ? WHERE id_user = ?";

        // Mulai transaksi untuk memastikan kedua update dilakukan dengan aman
        $conn->begin_transaction();

        try {
            // Persiapkan statement untuk update tabel users
            $stmt_users = $conn->prepare($sql_users);
            $stmt_users->bind_param("ssi", $username, $email, $id_user);
            $stmt_users->execute();

            // Persiapkan statement untuk update tabel anggota
            $stmt_anggota = $conn->prepare($sql_anggota);
            $stmt_anggota->bind_param("sssi", $nama, $alamat, $nomor_hp, $id_user);
            $stmt_anggota->execute();

            // Commit transaksi jika kedua query berhasil
            $conn->commit();

            $_SESSION['success'] = "Data anggota berhasil diperbarui!";
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika ada kesalahan
            $conn->rollback();
            $_SESSION['error'] = "Gagal memperbarui data anggota: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Semua kolom harus diisi!";
    }
}
?>