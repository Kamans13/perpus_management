<?php
include '../loggedas.php'; // koneksi database

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $jumlah = $_POST['jumlah'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Simpan peminjaman ke dalam tabel peminjaman
    $kode_peminjaman = "P" . rand(1000, 9999); // Contoh kode peminjaman acak
    $id_user = $_SESSION['user_id']; // Dapatkan ID petugas atau admin dari session
    $query_peminjaman = "INSERT INTO peminjaman (id_anggota, id_user, tanggal_pinjam, tanggal_kembali, status, kode_peminjaman)
                         VALUES ('$id_anggota', '$id_user', '$tanggal_pinjam', '$tanggal_kembali', 'Dipinjam', '$kode_peminjaman')";
    mysqli_query($conn, $query_peminjaman);

    // Ambil id_peminjaman yang baru saja disimpan
    $id_peminjaman = mysqli_insert_id($conn);

    // Simpan detail peminjaman (buku dan jumlahnya)
    foreach ($id_buku as $key => $buku_id) {
        $jumlah_buku = $jumlah[$key];
        $query_detail = "INSERT INTO detailpeminjaman (id_peminjaman, id_buku, jumlah)
                         VALUES ('$id_peminjaman', '$buku_id', '$jumlah_buku')";
        mysqli_query($conn, $query_detail);

        // Kurangi stok buku
        $query_update_stok = "UPDATE buku SET stok = stok - $jumlah_buku WHERE id_buku = '$buku_id'";
        mysqli_query($conn, $query_update_stok);
    }

    // Redirect ke halaman peminjaman berhasil
    header("Location: peminjaman_sukses.php");
}
?>

<form action="process_peminjaman.php" method="POST">
    <h3>Peminjaman Buku</h3>

    <!-- Pilih Anggota -->
    <label for="id_anggota">Nama Anggota:</label>
    <select name="id_anggota" required>
        <!-- Loop untuk memilih anggota -->
        <?php
        $query_anggota = "SELECT id_anggota, nama FROM anggota";
        $result_anggota = mysqli_query($conn, $query_anggota);
        while ($row_anggota = mysqli_fetch_assoc($result_anggota)) {
            echo "<option value='" . $row_anggota['id_anggota'] . "'>" . $row_anggota['nama'] . "</option>";
        }
        ?>
    </select><br>

    <!-- Pilih Buku dan Jumlah -->
    <label for="id_buku">Pilih Buku:</label>
    <select name="id_buku[]" multiple required>
        <!-- Loop untuk memilih buku -->
        <?php
        $query_buku = "SELECT id_buku, judul FROM buku WHERE stok > 0";
        $result_buku = mysqli_query($conn, $query_buku);
        while ($row_buku = mysqli_fetch_assoc($result_buku)) {
            echo "<option value='" . $row_buku['id_buku'] . "'>" . $row_buku['judul'] . "</option>";
        }
        ?>
    </select><br>

    <!-- Pilih Jumlah Buku -->
    <label for="jumlah">Jumlah Buku:</label>
    <input type="number" name="jumlah[]" min="1" required><br>

    <!-- Tanggal Pinjam -->
    <label for="tanggal_pinjam">Tanggal Pinjam:</label>
    <input type="date" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required><br>

    <!-- Tanggal Kembali (otomatis 2 minggu setelah peminjaman) -->
    <label for="tanggal_kembali">Tanggal Kembali:</label>
    <input type="date" name="tanggal_kembali" value="<?php echo date('Y-m-d', strtotime('+2 weeks')); ?>" readonly><br>

    <button type="submit">Proses Peminjaman</button>
</form>

