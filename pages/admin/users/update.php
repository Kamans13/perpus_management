<?php
include '../../../includes/koneksi.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_user = isset($_POST['id_user']) ? $_POST['id_user'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : ''; // Ambil data role
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Ambil data password

    // Validasi input
    if (!empty($id_user) && !empty($username) && !empty($email) && !empty($role)) {
        // Validasi nilai role
        if (!in_array($role, ['petugas', 'admin'])) {
            $_SESSION['error'] = "Role tidak valid!";
        } else {
            // Cek apakah password baru diisi
            if (!empty($password)) {
                // Jika password diisi, hash password dan update
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id_user = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $username, $email, $role, $hashed_password, $id_user);
            } else {
                // Jika password kosong, hanya update username, email, dan role
                $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id_user = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $username, $email, $role, $id_user);
            }

            // Eksekusi query
            if ($stmt->execute()) {
                $_SESSION['success'] = "Data pengguna berhasil diperbarui!";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['error'] = "Gagal memperbarui data pengguna: " . $stmt->error;
            }
        }
    } else {
        $_SESSION['error'] = "Semua kolom harus diisi!";
    }
}

?>
