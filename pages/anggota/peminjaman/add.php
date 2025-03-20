<?php
//terusan file include koneksi berada di
include '../loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
    exit;
}

//fitur sapa
    // Ambil nama pengguna
    $id_user = $_SESSION['id_user'];
    $username = getUserName($id_user);

    // Menampilkan waktu sesuai dengan jam
    $waktu = getWaktu();


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

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Peminjaman</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <?php include '../includes/topnav.php' ?>

        <div id="layoutSidenav">

            <?php include '../includes/sidebar.php'?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Tambah Peminjaman</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Peminjaman</li>
                            <li class="breadcrumb-item active">Tambah Peminjaman</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-table me-1"></i>
                                    Form Peminjaman
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="process.php">
                                    <!-- Pilihan Anggota -->
                                    <div class="mb-3">
                                        <label for="id_anggota" class="form-label">Pilih Anggota:</label>
                                        <select id="id_anggota" name="id_anggota" class="form-select" required>
                                            <option value="">-- Pilih Anggota --</option>
                                            <?php while ($row = mysqli_fetch_assoc($result_anggota)) { ?>
                                                <option value="<?php echo $row['id_anggota']; ?>"><?php echo $row['nama']; ?></option>
                                            <?php } ?>
                                        </select>                                    
                                    </div>
                                        
                                    <!-- Tanggal Pinjam -->
                                    <div class="mb-3">
                                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam:</label>
                                        <input type="date" class="form-control" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>


                                    <!-- Tanggal Kembali -->
                                    <div class="mb-3">
                                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali:</label>
                                        <input type="date" class="form-control" name="tanggal_kembali" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                                    </div>


                                    <!-- Pilih Buku -->
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-8">
                                            <label for="buku" class="form-label">Pilih Buku:</label>
                                            </div>
                                            <div class="col-4">
                                                <button class="btn btn-primary" type="button" onclick="tambahBuku()">Tambah Buku</button><br><br>
                                            </div>
                                        </div>
                                        <div id="buku-container">
                                            <div class="buku-row">
                                                <div class="row">
                                                    <div class="col-5 ">
                                                        <select class="form-select" id="buku" name="buku[]" required>
                                                            <option value="">-- Pilih Buku --</option>
                                                            <?php while ($row = mysqli_fetch_assoc($result_buku)) { ?>
                                                                <option value="<?php echo $row['id_buku']; ?>"><?php echo $row['judul']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-5">
                                                        <input class="form-control" type="number" name="jumlah[]" min="1" placeholder="Jumlah" required>
                                                    </div>
                                                    <div class="col-2">
                                                        <button class="btn btn-danger" type="button" onclick="hapusBaris(this)">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Petugas yang input (hidden) -->
                                            <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

                                            <!-- Kode Peminjaman (hidden) -->
                                            <input type="hidden" name="kode_peminjaman" value="<?php echo $kode_peminjaman; ?>">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="index.php" class="btn btn-danger">Kembali</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>

                <?php include '../includes/footer.php'?>

            </div>
        </div>
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
                    <div style="border-top: 1px solid black; margin: 10px 0;"></div>
                    <div class="row"> 
                        <div class="col-5">
                            <select name="buku[]"  class="form-control" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php
                                mysqli_data_seek($result_buku, 0); // Reset pointer data buku
                                while ($row = mysqli_fetch_assoc($result_buku)) { ?>
                                    <option value="<?php echo $row['id_buku']; ?>"><?php echo $row['judul']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <input class="form-control" type="number" name="jumlah[]" min="1" placeholder="Jumlah" required>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-danger" type="button" onclick="hapusBaris(this)">Hapus</button>
                        </div>
                    </div>
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
                    // Dapatkan elemen parent dari tombol
                    var row = button.closest('.buku-row');
                    // Hapus elemen row dari container
                    if (row) {
                        row.remove();
                    }
                }

        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../../js/datatables-simple-demo.js"></script>
    </body>
</html>
