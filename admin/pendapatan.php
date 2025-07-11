<?php
session_start();
include '../dbconnect.php';

// Mendapatkan pendapatan per bulan dengan lebih rinci dari detailorder berdasarkan varian_produk
$income_per_month = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(d.tanggal_transaksi, '%Y-%m') AS bulan_tahun,  -- Gabungkan bulan dan tahun
        COUNT(DISTINCT d.orderid) AS total_transaksi,  -- Count unique orders
        SUM(d.qty * vp.harga) AS total_pendapatan, -- Total revenue based on varian_produk
        AVG(d.qty * vp.harga) AS rata_rata_transaksi -- Average revenue per transaction based on varian_produk
    FROM detailorder d 
    JOIN varian_produk vp ON d.idvarian = vp.idvarian  -- Join with varian_produk table
    GROUP BY bulan_tahun 
    ORDER BY bulan_tahun DESC
");

// Mendapatkan pendapatan per bulan dari offline_orders
$income_offline_per_month = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(o.order_date, '%Y-%m') AS bulan_tahun,  -- Gabungkan bulan dan tahun
        COUNT(DISTINCT o.id) AS total_transaksi,  -- Count unique orders
        SUM(o.total_price) AS total_pendapatan, -- Total revenue
        AVG(o.total_price) AS rata_rata_transaksi -- Average revenue per transaction
    FROM offline_orders o
    GROUP BY bulan_tahun
    ORDER BY bulan_tahun DESC
");

// Check for errors in the query
if (!$income_per_month || !$income_offline_per_month) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="../favicon.png">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Pendapatan - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
    <style>
        .sidebar-menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        /* Print styles */
        @media print {

            .sidebar-menu,
            .header-area,
            footer,
            .nav-btn,
            .notification-area,
            .print-button {
                display: none !important;
            }

            .main-content {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        <!-- Sidebar menu area -->
        <div class="sidebar-menu">
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li class="active"><a href="index.php"><span>Home</span></a></li>
                            <li><a href="../"><span>Kembali ke Toko</span></a></li>
                            <li><a href="manageorder.php"><i class="ti-dashboard"></i><span>Kelola Pesanan</span></a></li>
                            <li><a href="offlineorder.php"><i class="ti-shopping-cart"></i><span>Kelola Pesanan Offline</span></a></li>
                            <li>
                                <a href="variant.php"><i class="ti-shopping-cart"></i><span>Kelola Varian</span></a>
                            </li>
                            <li><a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout"></i><span>Kelola Toko</span></a>
                                <ul class="collapse">
                                    <li><a href="kategori.php">Kategori</a></li>
                                    <li><a href="produk.php">Produk</a></li>
                                    <li><a href="pembayaran.php">Metode Pembayaran</a></li>
                                </ul>
                            </li>
                            <li><a href="customer.php"><span>Kelola Pelanggan</span></a></li>
                            <li><a href="user.php"><span>Kelola Staff</span></a></li>
                            <li><a href="pendapatan.php"><i class="fa fa-money"></i><span>Pendapatan</span></a></li>
                            <li><a href="../logout.php"><span>Logout</span></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content area -->
        <div class="main-content">
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area pull-right">
                            <li>
                                <h3>
                                    <div class="date">
                                        <script type='text/javascript'>
                                            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                            var myDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            var date = new Date();
                                            var day = date.getDate();
                                            var month = date.getMonth();
                                            var thisDay = date.getDay(),
                                                thisDay = myDays[thisDay];
                                            var yy = date.getYear();
                                            var year = (yy < 1000) ? yy + 1900 : yy;
                                            document.write(thisDay + ', ' + day + ' ' + months[month] + ' ' + year);
                                        </script>
                                    </div>
                                </h3>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pendapatan Per Bulan -->
            <div class="main-content-inner">
                <div class="sales-report-area mt-5 mb-5">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h2>Pendapatan Per Bulan</h2>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Bulan-Tahun</th>
                                                    <th>Total Transaksi</th>
                                                    <th>Total Pendapatan (Rp)</th>
                                                    <th>Rata-rata Transaksi (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Menampilkan pendapatan dari detailorder berdasarkan varian_produk
                                                while ($row = mysqli_fetch_assoc($income_per_month)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['bulan_tahun'] . "</td>";
                                                    echo "<td>" . $row['total_transaksi'] . "</td>";
                                                    echo "<td>Rp. " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>";
                                                    echo "<td>Rp. " . number_format($row['rata_rata_transaksi'], 0, ',', '.') . "</td>";
                                                    echo "</tr>";
                                                }

                                                // Menampilkan pendapatan dari offline_orders
                                                while ($row = mysqli_fetch_assoc($income_offline_per_month)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['bulan_tahun'] . "</td>";
                                                    echo "<td>" . $row['total_transaksi'] . "</td>";
                                                    echo "<td>Rp. " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>";
                                                    echo "<td>Rp. " . number_format($row['rata_rata_transaksi'], 0, ',', '.') . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary mt-3 print-button" onclick="window.print();"><i class="fa fa-print"></i> Cetak PDF</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer-area">
                <p>Copyright Bandeng Presto Duri Lunak Bu Yuwono</p>
            </div>
        </footer>
    </div>

    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/slicknav.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>