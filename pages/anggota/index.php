<?php
//terusan file include koneksi berada di
include 'loggedas.php';

session_start();
// Periksa apakah user memiliki peran 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../../auth/login.php");
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

    // Menghitung total buku yang sedang dipinjam oleh user login
    $sql_total_buku = "
    SELECT COUNT(*) AS total_buku 
    FROM detailpeminjaman dp 
    JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman 
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN users u ON a.id_user = u.id_user
    WHERE p.status = 'Dipinjam' AND u.id_user = ?";

    $stmt = $conn->prepare($sql_total_buku);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result_total_buku = $stmt->get_result();
    $total_buku = $result_total_buku->fetch_assoc()['total_buku'];

    
    // Menghitung total peminjaman oleh user login
    $sql_total_peminjaman = "
        SELECT COUNT(*) AS total_peminjaman 
        FROM peminjaman p
        JOIN anggota a ON p.id_anggota = a.id_anggota
        JOIN users u ON a.id_user = u.id_user
        WHERE u.id_user = ?";
        
    $stmt = $conn->prepare($sql_total_peminjaman);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result_total_peminjaman = $stmt->get_result();
    $total_peminjaman = $result_total_peminjaman->fetch_assoc()['total_peminjaman'];

    
    // Menghitung total pengembalian oleh user login
    $sql_total_pengembalian = "
    SELECT COUNT(*) AS total_pengembalian 
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN users u ON a.id_user = u.id_user
    WHERE p.status = 'Dikembalikan' AND u.id_user = ?";
    $stmt = $conn->prepare($sql_total_pengembalian);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result_total_pengembalian = $stmt->get_result();
    $total_pengembalian = $result_total_pengembalian->fetch_assoc()['total_pengembalian'];
    
    // Menghitung total denda oleh user login
    $sql_total_denda = "SELECT SUM(d.jumlah_denda) AS total_denda
    FROM denda d
    JOIN peminjaman p ON d.id_peminjaman = p.id_peminjaman
    WHERE p.id_user = ? AND d.status_pembayaran = 'Belum Dibayar'";
    $stmt = $conn->prepare($sql_total_denda);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result_total_denda = $stmt->get_result();
    $total_denda = $result_total_denda->fetch_assoc()['total_denda'];
    
    // Query untuk kategori buku terfavorit berdasarkan user login
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
    JOIN 
        peminjaman p ON dp.id_peminjaman = p.id_peminjaman
    JOIN 
        anggota a ON p.id_anggota = a.id_anggota
    JOIN 
        users u ON a.id_user = u.id_user
    WHERE 
        u.id_user = ?  -- Menyaring berdasarkan user yang login
    GROUP BY 
        k.id_kategori
    ORDER BY 
        total_buku DESC
    ";

    $stmt = $conn->prepare($query_kategori_favorit);
    $stmt->bind_param("i", $id_user);  // Menggunakan id_user yang sedang login
    $stmt->execute();
    $result_kategori_favorit = $stmt->get_result();

    $kategori_buku = [];
    $jumlah_perkategori = [];

    while ($row = $result_kategori_favorit->fetch_assoc()) {
    $kategori_buku[] = $row['nama_kategori'];
    $jumlah_perkategori[] = $row['total_buku'];
    }

    // Query untuk mendapatkan Top 5 Buku berdasarkan jumlah peminjaman
    $query = "
    SELECT b.judul, SUM(dp.jumlah) AS total_peminjaman
    FROM buku b
    JOIN detailpeminjaman dp ON b.id_buku = dp.id_buku
    JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN users u ON a.id_user = u.id_user
    WHERE u.id_user = ?
    GROUP BY b.id_buku
    ORDER BY total_peminjaman DESC
    LIMIT 5;
    ";

    // Prepare statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil data untuk chart
    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
    $labels[] = $row['judul'];
    $data[] = $row['total_peminjaman'];
    }

    $stmt->close();

    
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

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBuku" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book	"></i></div>
                                Buku
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseBuku" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="buku/index.php">Daftar Buku</a>                               
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
                                        <h3><strong><?php echo $total_buku; ?></strong> <br>Buku </h3> 
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
                                    <h3><strong><?php echo number_format($total_denda, 2); ?></strong><br> Denda</h3>
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
                                        Top 5 Buku yang dipinjam
                                    </div>
                                    <div class="card-body"><canvas id="top5Books" width="100%" height="40"></canvas>
                                    </div>
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
            // Ambil data dari PHP ke dalam JavaScript
            const labels = <?php echo json_encode($labels); ?>;
            const data = <?php echo json_encode($data); ?>;

            // Setup chart
            const Chartctx = document.getElementById('top5Books').getContext('2d');
            const myChart = new Chart(Chartctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: data,
                        backgroundColor: '#42a5f5',
                        borderColor: '#1e88e5',
                        borderWidth: 1
                    }]
                },
                options: {
                indexAxis: 'y',  // Ini untuk membuat bar chart menjadi horizontal
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true  // Memastikan sumbu X dimulai dari 0
                    }
                }
            }
            });
        </script>


    </body>
</html>



