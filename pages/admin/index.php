<?php
//terusan file include koneksi berada di
include 'loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Tampilkan notifikasi selamat datang
if (isset($_SESSION['welcome_message'])) {
    echo "<script>alert('" . $_SESSION['welcome_message'] . "');</script>";
    unset($_SESSION['welcome_message']); // Hapus session setelah ditampilkan
}

//Fungsi Sapa
    // Ambil nama pengguna
    $id_user = $_SESSION['id_user'];
    $username = getUserName($id_user);

    // Menampilkan waktu sesuai dengan jam
    $waktu = getWaktu();

// 1. Total Buku
$sql_total_buku = "SELECT SUM(stok) AS total_buku FROM buku";
$result_total_buku = $conn->query($sql_total_buku);
$total_buku = $result_total_buku->fetch_assoc()['total_buku'] ?? 0;

// 2. Total Anggota
$sql_total_anggota = "SELECT COUNT(*) AS total_anggota FROM users WHERE role = 'anggota'";
$result_total_anggota = $conn->query($sql_total_anggota);
$total_anggota = $result_total_anggota->fetch_assoc()['total_anggota'] ?? 0;

// 3. Total Peminjaman
$sql_total_peminjaman = "SELECT COUNT(*) AS total_peminjaman FROM peminjaman";
$result_total_peminjaman = $conn->query($sql_total_peminjaman);
$total_peminjaman = $result_total_peminjaman->fetch_assoc()['total_peminjaman'] ?? 0;

// 4. Total Pengembalian
$sql_total_pengembalian = "SELECT COUNT(*) AS total_pengembalian FROM peminjaman WHERE status = 'Dikembalikan'";
$result_total_pengembalian = $conn->query($sql_total_pengembalian);
$total_pengembalian = $result_total_pengembalian->fetch_assoc()['total_pengembalian'] ?? 0;

// 5. Total Denda Belum Dibayar
$sql_total_denda = "SELECT SUM(jumlah_denda) AS total_denda FROM denda WHERE status_pembayaran = 'Belum Dibayar'";
$result_total_denda = $conn->query($sql_total_denda);
$total_denda = $result_total_denda->fetch_assoc()['total_denda'] ?? 0;

// 6. Buku Baru Ditambahkan (5 Terbaru)
$sql_buku_baru = "SELECT judul, pengarang, tanggal_ditambahkan FROM buku ORDER BY tanggal_ditambahkan DESC LIMIT 5";
$result_buku_baru = $conn->query($sql_buku_baru);

// Query untuk mendapatkan jumlah peminjaman dan pengembalian per hari
    $query_peminjaman_pengembalian = "
        SELECT 
            DATE(tanggal_pinjam) AS tanggal,
            SUM(CASE WHEN status = 'Dipinjam' THEN 1 ELSE 0 END) AS peminjaman,
            SUM(CASE WHEN status = 'Dikembalikan' THEN 1 ELSE 0 END) AS pengembalian
        FROM peminjaman
        WHERE tanggal_pinjam >= CURDATE() - INTERVAL 30 DAY
        GROUP BY DATE(tanggal_pinjam)
        ORDER BY tanggal_pinjam ASC
    ";

    $result_peminjaman_pengembalian = $conn->query($query_peminjaman_pengembalian);

    $peminjaman_data = [];
    $pengembalian_data = [];
    $labels = [];

    while ($row = $result_peminjaman_pengembalian->fetch_assoc()) {
        $labels[] = $row['tanggal'];
        $peminjaman_data[] = $row['peminjaman'];
        $pengembalian_data[] = $row['pengembalian'];
    }


// Query untuk mendapatkan data anggota paling aktif
    $query = "
            SELECT 
                a.id_anggota, 
                a.nama AS nama_anggota, 
                COUNT(p.id_peminjaman) AS jumlah_peminjaman
            FROM 
                anggota a
            JOIN 
                peminjaman p ON a.id_anggota = p.id_anggota
            JOIN
                users u ON a.id_user = u.id_user
            WHERE 
                u.role = 'anggota'
            GROUP BY 
                a.id_anggota
            ORDER BY 
                jumlah_peminjaman DESC
            LIMIT 5;

    ";

    $result = $conn->query($query);

    // Cek jika query berhasil
     if ($result->num_rows > 0) {
         // Data untuk chart
         $nama_anggota = [];
         $jumlah_peminjaman = [];
         while ($row = $result->fetch_assoc()) {
             $nama_anggota[] = $row['nama_anggota'];
             $jumlah_peminjaman[] = $row['jumlah_peminjaman'];
         }
     } else {
         echo "Tidak ada data ditemukan.";
         exit;
     }


//chart pie buku terfavorite 
    $query_kategori_favorit = "
        SELECT 
            k.nama_kategori, 
            SUM(dp.jumlah) AS total_buku
        FROM 
            detailpeminjaman dp
        JOIN 
            buku b ON dp.id_buku = b.id_buku
        JOIN 
            kategori k ON b.id_kategori = k.id_kategori
        GROUP BY 
            k.id_kategori
        ORDER BY 
            total_buku DESC
    ";

    $result_kategori_favorit = $conn->query($query_kategori_favorit);
    $kategori_buku = [];
    $jumlah_perkategori = [];  // Ganti nama variabel disini

    while ($row = $result_kategori_favorit->fetch_assoc()) {
        $kategori_buku[] = $row['nama_kategori'];
        $jumlah_perkategori[] = $row['total_buku'];  // Gunakan nama baru variabel
    }





$conn->close(); // Tutup koneksi
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard Admin - SB Admin</title>
        <link href="../../css/styles.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            .small-chart {
            max-width: 500px;  /* Adjust as needed */
            max-height: 180px; /* Adjust as needed */
            width: 100%;
            height: auto;
        }

        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php"><h6><i class="fas fa-swatchbook"></i>PerpusManager <br>
            by Kamanseptia | 17230312</h6> </a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a  class="dropdown-item"  href="../auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div id="layoutSidenav">
            <!-- Sidebar Menu-->
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAnggota" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="far fa-address-card	"></i></div>
                                Anggota
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseAnggota" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="anggota/index.php">Daftar Anggota</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBuku" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book	"></i></div>
                                Buku
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseBuku" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="buku/index.php">Daftar Buku</a>
                                    <a class="nav-link" href="rak/index.php">Rak Buku</a>
                                    <a class="nav-link" href="kategori/index.php">Kategori Buku</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePeminjaman" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                kelola Peminjaman
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>

                            <div class="collapse" id="collapsePeminjaman" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="peminjaman/index.php">Daftar Peminjaman</a>
                                    <a class="nav-link" href="pengembalian/index.php">Daftar Pengembalian</a>
                                    <a class="nav-link" href="pengembalian/pengembalian.php">Pengembalian Buku</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseDenda" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-bill-alt"></i></div>
                                Kelola Denda
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>

                            <div class="collapse" id="collapseDenda" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="denda/index.php">Daftar Denda</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLPetugas" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-friends	"></i></div>
                                Users
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLPetugas" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="users/index.php">Kelola Users</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLlaporan" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt	"></i></div>
                                Laporan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLlaporan" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="laporan/index.php">Kelola Laporan</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Hi! <?php echo htmlspecialchars($username); ?></div>
                        <div class="small">Selamat <?php echo htmlspecialchars($waktu); ?> ^.^</div>     
                    </div>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card  bg-danger text-white mb-4">
                                    <div class="card-body">
                                        <h3><strong><?php echo $total_anggota; ?></strong> <br>Anggota</h3> 
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card  bg-warning  text-white mb-4">
                                    <div class="card-body">
                                    <h3><strong><?php echo $total_peminjaman; ?></strong> <br>Peminjaman</h3> 
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                         <h3><strong><?php echo $total_pengembalian; ?></strong><br>Pengembalian</h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                    <h3><strong><?php echo $total_buku; ?></strong><br> Buku</h3>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- section chart -->
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Kategori Buku Terfavorit
                                    </div>
                                    <div class="card-body">
                                    <canvas id="kategoriBukuChart" width="100%" class="small-chart"></canvas>
                                    </div>
                                </div>
                            </div>                           
                            
                            <div class="col-xl">
                                <div class="card mb-12">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Top 5 Anggota
                                    </div>
                                    <div class="card-body"><canvas id="top5" width="100%" height="40"></canvas></div>
                                </div>
                            </div>

                            <!-- Kode HTML untuk Chart -->
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Peminjaman dan Pengembalian
                                    </div>
                                    <div class="card-body">
                                        <canvas id="myAreaChart" width="100%" height="40"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                        </div>
                        <!-- section table -->
                        <div class="card mb-4"> 
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Judul </th>
                                            <th>Pengarang</th>
                                            <th>Kategori</th>
                                            <th>Tahun</th>
                                            <th>Stok</th>
                                            <th>Peminjam</th>
                                            <th>Status</th>
                                            <th>Petugas</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Tanggal Kembali</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Judul </th>
                                            <th>Pengarang</th>
                                            <th>Kategori</th>
                                            <th>Tahun</th>
                                            <th>Stok</th>
                                            <th>Peminjam</th>
                                            <th>Status</th>
                                            <th>Petugas</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Tanggal Kembali</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            // Tampilkan data jika ada
                                            
                                            
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>

                <?php include 'includes/footer.php'?>
                
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.font.family = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.color = '#292b2c';

            // Pie Chart Example
            var ctx = document.getElementById('kategoriBukuChart').getContext('2d');
            var kategoriBukuChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($kategori_buku); ?>,
                    datasets: [{
                        label: 'Jumlah Buku',
                        data: <?php echo json_encode($jumlah_perkategori); ?>,
                        backgroundColor: [
                            'rgb(255, 99, 133)',
                            'rgb(54, 163, 235)',
                            'rgb(255, 207, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,  // Allow custom size
                    aspectRatio: 1,  // Optional, you can control the aspect ratio
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' Buku';
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,  // Padding to avoid tight fit
                            left: 20,
                            right: 20,
                            bottom: 20
                        }
                    }
                }
            });


        </script>

        <script>
            // Ambil data dari PHP
            var labels = <?php echo json_encode($labels); ?>;
            var peminjamanData = <?php echo json_encode($peminjaman_data); ?>;
            var pengembalianData = <?php echo json_encode($pengembalian_data); ?>;

            // Membuat chart area
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // Tanggal sebagai label
                    datasets: [{
                        label: "Peminjaman",
                        lineTension: 0.3,
                        backgroundColor: "rgb(255, 193, 7)",
                        borderColor: "rgb(255, 193, 7)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgb(255, 193, 7)",
                        pointBorderColor: "rgb(255, 255, 255)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgb(255, 193, 7)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: peminjamanData, // Data peminjaman
                    },
                    {
                        label: "Pengembalian",
                        lineTension: 0.3,
                        backgroundColor: "rgb(40, 167, 69)",
                        borderColor: "rgb(40, 167, 69)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgb(40, 167, 69)",
                        pointBorderColor: "rgb(255, 255, 255)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgb(40, 167, 69)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: pengembalianData, // Data pengembalian
                    }],
                },
                options: {
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                max: Math.max(Math.max(...peminjamanData), Math.max(...pengembalianData)),
                                maxTicksLimit: 5
                            },
                            gridLines: {
                                color: "rgba(0, 0, 0, .125)",
                            }
                        }],
                    },
                    legend: {
                        display: true
                    }
                }
            });
        </script>

        <script>
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.font.family = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.color = '#292b2c';


            // Data dari PHP ke JavaScript
            const namaAnggota = <?php echo json_encode($nama_anggota); ?>;
            const jumlahPeminjaman = <?php echo json_encode($jumlah_peminjaman); ?>;

            var ctx = document.getElementById("top5").getContext('2d');
            var myBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: namaAnggota, // Nama anggota sebagai label
                    datasets: [{
                        label: "Jumlah Peminjaman",
                        backgroundColor: "rgb(220, 53, 69)", // Warna batang
                        borderColor: "rgb(220, 53, 69)", // Warna border
                        data: jumlahPeminjaman, // Data jumlah peminjaman
                    }],
                },
                options: {
                    scales: {
                        x: {
                            grid: {
                                display: false // Hilangkan garis pada sumbu X
                            },
                            ticks: {
                                maxTicksLimit: 6 // Maksimal label pada sumbu X
                            }
                        },
                        y: {
                            beginAtZero: true, // Memastikan sumbu Y mulai dari nol
                            ticks: {
                                stepSize: 1, // Interval angka pada sumbu Y
                                callback: function(value) {
                                    return value.toFixed(0); // Menampilkan angka bulat
                                }
                            },
                            grid: {
                                display: true // Menampilkan garis pada sumbu Y
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Menyembunyikan legenda
                        }
                    }
                }
            });

        </script>








    </body>
</html>
