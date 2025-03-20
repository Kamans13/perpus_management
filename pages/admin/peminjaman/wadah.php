<?php
// Sertakan file koneksi dan cek sesi
session_start();
include '../loggedas.php'; // Sesuaikan dengan file koneksi Anda

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data anggota dan buku
$query_anggota = "SELECT * FROM anggota";
$result_anggota = mysqli_query($conn, $query_anggota);

$query_buku = "SELECT * FROM buku";
$result_buku = mysqli_query($conn, $query_buku);

// Ambil kode peminjaman terakhir
$query_kode = "SELECT kode_peminjaman FROM peminjaman ORDER BY id_peminjaman DESC LIMIT 1";
$result_kode = mysqli_query($conn, $query_kode);
$row_kode = mysqli_fetch_assoc($result_kode);
$kode_terakhir = $row_kode ? (int)substr($row_kode['kode_peminjaman'], 3) : 0;
$kode_peminjaman = 'PMJ' . str_pad($kode_terakhir + 1, 5, '0', STR_PAD_LEFT);
?>

<form method="POST" action="process.php">
    <!-- Pilihan Anggota -->
    <label for="id_anggota">Pilih Anggota:</label>
    <select id="id_anggota" name="id_anggota" required>
        <option value="">-- Pilih Anggota --</option>
        <?php while ($row = mysqli_fetch_assoc($result_anggota)) { ?>
            <option value="<?php echo $row['id_anggota']; ?>"><?php echo $row['nama']; ?></option>
        <?php } ?>
    </select><br><br>

    <!-- Tanggal Pinjam -->
    <label for="tanggal_pinjam">Tanggal Pinjam:</label>
    <input type="date" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required><br><br>

    <!-- Tanggal Kembali -->
    <label for="tanggal_kembali">Tanggal Kembali:</label>
    <input type="date" name="tanggal_kembali" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required><br><br>

    <!-- Pilih Buku -->
    <label for="buku">Pilih Buku:</label>
    <div id="buku-container">
        <div class="buku-row">
            <select id="buku" name="buku[]" required>
                <option value="">-- Pilih Buku --</option>
                <?php while ($row = mysqli_fetch_assoc($result_buku)) { ?>
                    <option value="<?php echo $row['id_buku']; ?>"><?php echo $row['judul']; ?></option>
                <?php } ?>
            </select>
            <input type="number" name="jumlah[]" min="1" placeholder="Jumlah" required>
            <button type="button" onclick="hapusBaris(this)">Hapus</button>
        </div>
        <button type="button" onclick="tambahBuku()">Tambah Buku</button><br><br>

        <!-- Petugas yang input (hidden) -->
        <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

        <!-- Kode Peminjaman (hidden) -->
        <input type="hidden" name="kode_peminjaman" value="<?php echo $kode_peminjaman; ?>">
    </div>

    <div class="d-flex justify-content-center gap-2">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="index.php" class="btn btn-danger">Kembali</a>
    </div>
</form>

<!-- Menambahkan CDN Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    // Inisialisasi Select2 untuk elemen select
    $(document).ready(function() {
        $('#id_anggota').select2({
            placeholder: '-- Pilih Anggota --',
            allowClear: true
        });
        $('#buku').select2({
            placeholder: '-- Pilih Buku --',
            allowClear: true
        });
    });

    // Fungsi untuk menambahkan buku
    function tambahBuku() {
        var bukuContainer = document.getElementById('buku-container');
        var newRow = document.createElement('div');
        newRow.classList.add('buku-row');
        newRow.innerHTML = `
            <select name="buku[]" required>
                <option value="">-- Pilih Buku --</option>
                <?php
                mysqli_data_seek($result_buku, 0); // Reset pointer data buku
                while ($row = mysqli_fetch_assoc($result_buku)) { ?>
                    <option value="<?php echo $row['id_buku']; ?>"><?php echo $row['judul']; ?></option>
                <?php } ?>
            </select>
            <input type="number" name="jumlah[]" min="1" placeholder="Jumlah" required>
            <button type="button" onclick="hapusBaris(this)">Hapus</button>
        `;
        bukuContainer.appendChild(newRow);

        // Inisialisasi Select2 pada dropdown baru
        $('select').select2({
            placeholder: '-- Pilih Buku --',
            allowClear: true
        });
    }

    // Fungsi untuk menghapus baris buku
    function hapusBaris(button) {
        var row = button.parentNode;
        row.parentNode.removeChild(row);
    }
</script>
