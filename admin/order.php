<?php
session_start();
include '../dbconnect.php';
$orderids = $_GET['orderid'];
$liatcust = mysqli_query($conn, "select * from login l, cart c where orderid='$orderids' and l.userid=c.userid");
$checkdb = mysqli_fetch_array($liatcust);
date_default_timezone_set("Asia/Bangkok");
$ongkir = 10000; // Ongkir tetap Rp10.000

if (isset($_POST['kirim'])) {
	$updatestatus = mysqli_query($conn, "update cart set status='Pengiriman' where orderid='$orderids'");
	$del =  mysqli_query($conn, "delete from konfirmasi where orderid='$orderids'");

	if ($updatestatus && $del) {
		echo " <div class='alert alert-success'>
			<center>Pesanan dikirim.</center>
		  </div>
		<meta http-equiv='refresh' content='1; url= manageorder.php'/>  ";
	} else {
		echo "<div class='alert alert-warning'>
			Gagal Submit, silakan coba lagi
		  </div>
		 <meta http-equiv='refresh' content='1; url= manageorder.php'/> ";
	}
};

if (isset($_POST['selesai'])) {
	$updatestatus = mysqli_query($conn, "update cart set status='Selesai' where orderid='$orderids'");

	if ($updatestatus) {
		echo " <div class='alert alert-success'>
			<center>Transaksi diselesaikan.</center>
		  </div>
		<meta http-equiv='refresh' content='1; url= manageorder.php'/>  ";
	} else {
		echo "<div class='alert alert-warning'>
			Gagal Submit, silakan coba lagi
		  </div>
		 <meta http-equiv='refresh' content='1; url= manageorder.php'/> ";
	}
};

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8">
	<link rel="icon"
		type="image/png"
		href="../favicon.png">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Bandeng Bu Yuwono - Pesanan <?php echo $checkdb['namalengkap']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/themify-icons.css">
	<link rel="stylesheet" href="assets/css/metisMenu.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/slicknav.min.css">

	<!-- amchart css -->
	<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
	<!-- Start datatable css -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">

	<!-- others css -->
	<link rel="stylesheet" href="assets/css/typography.css">
	<link rel="stylesheet" href="assets/css/default-css.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/responsive.css">
	<style>
		/* Lightbox styles */
		.lightbox {
			display: none;
			/* Hidden by default */
			position: fixed;
			z-index: 1000;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			overflow: auto;
			background-color: rgba(0, 0, 0, 0.9);
		}

		.lightbox-content {
			margin: auto;
			display: block;
			max-width: 80%;
			max-height: 80%;
		}

		.close {
			position: absolute;
			top: 20px;
			right: 40px;
			color: white;
			font-size: 40px;
			font-weight: bold;
			cursor: pointer;
		}
	</style>

	<!-- modernizr css -->
	<script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>



</head>

<body>
	<!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
	<!-- preloader area start -->
	<div id="preloader">
		<div class="loader"></div>
	</div>
	<!-- preloader area end -->
	<!-- page container area start -->
	<div class="page-container">
		<!-- sidebar menu area start -->
		<div class="sidebar-menu">
			<div class="main-menu">
				<div class="menu-inner">
					<nav>
						<ul class="metismenu" id="menu">
							<li class="active"><a href="index.php"><span>Home</span></a></li>
							<li><a href="../"><span>Kembali ke Toko</span></a></li>
							<li>
								<a href="manageorder.php"><i class="ti-dashboard"></i><span>Kelola Pesanan</span></a>
							</li>
							<li>
								<a href="offlineorder.php"><i class="ti-shopping-cart"></i><span>Kelola Pesanan Offline</span></a>
							</li>
							<li>
                                <a href="variant.php"><i class="ti-shopping-cart"></i><span>Kelola Varian</span></a>
                            </li>
							<li>
								<a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout"></i><span>Kelola Toko
									</span></a>
								<ul class="collapse">
									<li><a href="kategori.php">Kategori</a></li>
									<li><a href="produk.php">Produk</a></li>
									<li><a href="pembayaran.php">Metode Pembayaran</a></li>
								</ul>
							</li>
							<li><a href="customer.php"><span>Kelola Pelanggan</span></a></li>
							<li><a href="user.php"><span>Kelola Staff</span></a></li>
							<li>
								<a href="pendapatan.php"><i class="fa fa-money"></i><span>Pendapatan</span></a>
							</li>
							<li>
								<a href="../logout.php"><span>Logout</span></a>

							</li>

						</ul>
					</nav>
				</div>
			</div>
		</div>
		<!-- sidebar menu area end -->
		<!-- main content area start -->
		<div class="main-content">
			<!-- header area start -->
			<div class="header-area">
				<div class="row align-items-center">
					<!-- nav and search button -->
					<div class="col-md-6 col-sm-8 clearfix">
						<div class="nav-btn pull-left">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>
					<!-- profile info & task notification -->
					<div class="col-md-6 col-sm-4 clearfix">
						<ul class="notification-area pull-right">
							<li>
								<h3>
									<div class="date">
										<script type='text/javascript'>
											// <!--
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
											//-->
										</script></b>
									</div>
								</h3>

							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- header area end -->


			<!-- page title area end -->
			<div class="main-content-inner">

				<!-- market value area start -->
				<div class="row mt-5 mb-5">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div class="d-sm-flex justify-content-between align-items-center">
									<h3>Order ID: #<?php echo $orderids; ?></h3>
								</div>
								<p>Nama Pelanggan: <strong><?php echo $checkdb['namalengkap']; ?></strong></p>
								<p>Waktu Order: <strong><?php echo $checkdb['tglorder']; ?></strong></p>

								<div class="data-tables datatable-dark">
									<table id="dataTable3" class="display" style="width:100%">
										<thead class="thead-dark">
											<tr>
												<th>No</th>
												<th>Produk</th>
												<th>Jumlah</th>
												<th>Harga</th>
												<th>Total</th>
												<th>Alamat</th>
												<th>Nama Rekening</th> <!-- Kolom Nama Rekening -->
												<th>Bukti Pembayaran</th> <!-- Kolom Bukti Pembayaran -->
											</tr>
										</thead>
										<tbody>
											<?php
											$brgs = mysqli_query($conn, "SELECT * FROM detailorder d, produk p, cart c WHERE d.orderid='$orderids' AND d.idproduk=p.idproduk AND c.orderid='$orderids' ORDER BY d.idproduk ASC");
											$no = 1;
											while ($p = mysqli_fetch_array($brgs)) {
												$total = $p['qty'] * $p['hargaafter'];
												$alamat = $checkdb['alamat']; // Ambil alamat dari $checkdb
												$namarek = $checkdb['namarekening']; // Ambil nama rekening dari $checkdb
												$bukti = $checkdb['bukti']; // Ambil bukti pembayaran dari $checkdb
											?>
												<tr>
													<td><?php echo $no++; ?></td>
													<td><?php echo $p['namaproduk']; ?></td>
													<td><?php echo $p['qty']; ?></td>
													<td>Rp<?php
															// Get the price from the varian_produk table
															$query = mysqli_query($conn, "SELECT harga FROM varian_produk WHERE idvarian = '{$p['idvarian']}'");
															$varian = mysqli_fetch_assoc($query);
															echo number_format($varian['harga']);
															?></td>
													<td>Rp<?php
															$total = $p['qty'] * $varian['harga'];
															echo number_format($total);
															?></td>
													<td><?php echo $alamat; ?></td>
													<td><?php echo $namarek; ?></td>
													<td>
														<?php
														// Display proof of payment image
														if ($bukti != '') {
															echo '<img class="proof-image" src="../uploads/' . $bukti . '" alt="Bukti Pembayaran" width="100" height="100" onclick="openLightbox(this.src)">';
														} else {
															echo 'Tidak ada bukti pembayaran';
														}
														?>
													</td>
												</tr>
											<?php
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="4" style="text-align:right">Subtotal:</th>
												<th>Rp<?php
														$result1 = mysqli_query($conn, "SELECT SUM(d.qty * vp.harga) AS count 
FROM detailorder d
JOIN varian_produk vp ON d.idvarian = vp.idvarian
WHERE d.orderid = '$orderids'");
														$row1 = mysqli_fetch_assoc($result1);
														$subtotal = $row1['count']; // Total without shipping
														echo number_format($subtotal);
														?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="4" style="text-align:right">Subtotal:</th>
												<th>Rp<?php echo number_format($subtotal); ?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="4" style="text-align:right">Ongkir:</th>
												<th>Rp<?php echo number_format($ongkir); ?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th colspan="4" style="text-align:right">Total:</th>
												<th>Rp<?php echo number_format($subtotal + $ongkir); ?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>

									</table>
								</div>
								<br>
								<?php
								if ($checkdb['status'] == 'Confirmed') {
									// Ambil informasi pembayaran
									$ambilinfo = mysqli_query($conn, "SELECT * FROM konfirmasi WHERE orderid='$orderids'");
									while ($tarik = mysqli_fetch_array($ambilinfo)) {
										$met = $tarik['payment'];
										$namarek = $tarik['namarekening'];
										$tglb = $tarik['tglbayar'];
										$bukti = $tarik['bukti']; // Kolom bukti yang berisi nama file gambar

										echo '
        <h5>Informasi Pembayaran</h5>
        <div class="data-tables datatable-dark">
            <table id="dataTable2" class="display" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>Metode</th>
                        <th>Pemilik Rekening</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Bukti Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>' . $met . '</td>
                        <td>' . $namarek . '</td>
                        <td>' . $tglb . '</td>
                        <td>';

										// Menampilkan gambar bukti pembayaran
										if ($bukti != '') {
											echo '<img src="../uploads/' . $bukti . '" alt="Bukti Pembayaran" width="100" height="100">';
										} else {
											echo 'Tidak ada bukti pembayaran';
										}

										echo '  </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br><br>
        <form method="post">
            <input type="submit" name="kirim" class="form-control btn btn-success" value="Kirim" />
        </form>';
									}
								}

								// Ensure "Selesaikan" button is always visible
								echo '
 <form method="post">
	<input type="submit" name="selesai" class="form-control btn btn-success" value="Selesaikan" />
 </form>
 ';
								?> <br>
							</div>

						</div>
					</div>
				</div>





				<!-- row area start-->
			</div>
		</div>
		<!-- main content area end -->
		<!-- footer area start-->
		<footer>
			<div class="footer-area">
				<p>Copyright Bandeng Presto Duri Lunak Bu Yuwono</p>
			</div>
		</footer>
		<!-- footer area end-->
	</div>
	<!-- page container area end -->



	<script type="text/javascript">
		$(document).ready(function() {
			$('#dataTable3').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'print'
				]
			});
		});
		$(document).ready(function() {
			$('#dataTable2').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'print'
				]
			});
		});
	</script>
	<script>
		function openLightbox(src) {
			var lightbox = document.getElementById("lightbox");
			var lightboxImg = document.getElementById("lightbox-img");
			lightbox.style.display = "block";
			lightboxImg.src = src;
		}

		function closeLightbox() {
			var lightbox = document.getElementById("lightbox");
			lightbox.style.display = "none";
		}
	</script>

	<!-- jquery latest version -->
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
	<!-- bootstrap 4 js -->
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	<script src="assets/js/metisMenu.min.js"></script>
	<script src="assets/js/jquery.slimscroll.min.js"></script>
	<script src="assets/js/jquery.slicknav.min.js"></script>
	<!-- Start datatable js -->
	<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

	<!-- others plugins -->
	<script src="assets/js/plugins.js"></script>
	<script src="assets/js/scripts.js"></script>
	<!-- Lightbox for displaying images -->
	<div id="lightbox" class="lightbox" onclick="closeLightbox()">
		<span class="close">&times;</span>
		<img class="lightbox-content" id="lightbox-img" src="">
	</div>



</body>

</html>