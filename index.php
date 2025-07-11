<?php
session_start();
include 'dbconnect.php';
// Initialize visit counters
$total_today_visits = 0;
$total_monthly_visits = 0;
$total_yearly_visits = 0;
$total_visits = 0;
$total_online_visitors = 0;

// Mencatat kunjungan
$date_today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT * FROM visit_counter WHERE visit_date = '$date_today'");

if (mysqli_num_rows($result) > 0) {
	// Update count for today
	$row = mysqli_fetch_assoc($result);
	$new_count = $row['visit_count'] + 1;
	mysqli_query($conn, "UPDATE visit_counter SET visit_count = $new_count WHERE visit_date = '$date_today'");
} else {
	// Insert new entry for today
	mysqli_query($conn, "INSERT INTO visit_counter (visit_date, visit_count) VALUES ('$date_today', 1)");
}

// Total visits for today
$today_result = mysqli_query($conn, "SELECT visit_count FROM visit_counter WHERE visit_date = '$date_today'");
$today_data = mysqli_fetch_assoc($today_result);
$total_today_visits = $today_data['visit_count'] ? $today_data['visit_count'] : 0;

// Total visits for the current month
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$monthly_result = mysqli_query($conn, "SELECT SUM(visit_count) AS total_monthly FROM visit_counter WHERE visit_date BETWEEN '$month_start' AND '$month_end'");
$monthly_data = mysqli_fetch_assoc($monthly_result);
$total_monthly_visits = $monthly_data['total_monthly'] ? $monthly_data['total_monthly'] : 0;

// Total visits for the current year
$current_year = date('Y');
$yearly_result = mysqli_query($conn, "SELECT SUM(visit_count) AS total_yearly FROM visit_counter WHERE YEAR(visit_date) = '$current_year'");
$yearly_data = mysqli_fetch_assoc($yearly_result);
$total_yearly_visits = $yearly_data['total_yearly'] ? $yearly_data['total_yearly'] : 0;

// Total visits overall
$total_result = mysqli_query($conn, "SELECT SUM(visit_count) AS total_visits FROM visit_counter");
$total_data = mysqli_fetch_assoc($total_result);
$total_visits = $total_data['total_visits'] ? $total_data['total_visits'] : 0;

// Handle online visitors
$session_id = session_id();
$time = time();
$expire_time = $time - 1800; // 30 minutes expiration

// Remove expired sessions
mysqli_query($conn, "DELETE FROM online_visitors WHERE visit_time < FROM_UNIXTIME($expire_time)");

// Insert or update current session
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM online_visitors WHERE session_id = '$session_id'")) == 0) {
	mysqli_query($conn, "INSERT INTO online_visitors (session_id) VALUES ('$session_id')");
}

// Count online visitors
$online_visitors_result = mysqli_query($conn, "SELECT COUNT(*) AS online_count FROM online_visitors");
$online_visitors_data = mysqli_fetch_assoc($online_visitors_result);
$total_online_visitors = $online_visitors_data['online_count'];
?>

<!DOCTYPE html>
<html>

<head>
	<title>Bandeng Bu Yuwono</title>
	<!-- for-mobile-apps -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Falenda Flora, Ruben Agung Santoso" />
	<script type="application/x-javascript">
		addEventListener("load", function() {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
	</script>
	<!-- //for-mobile-apps -->
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<!-- font-awesome icons -->
	<link href="css/font-awesome.css" rel="stylesheet">
	<!-- //font-awesome icons -->
	<!-- js -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<!-- //js -->
	<link href='//fonts.googleapis.com/css?family=Raleway:400,100,100italic,200,200italic,300,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<!-- start-smoth-scrolling -->
	<script type="text/javascript" src="js/move-top.js"></script>
	<script type="text/javascript" src="js/easing.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".scroll").click(function(event) {
				event.preventDefault();
				$('html,body').animate({
					scrollTop: $(this.hash).offset().top
				}, 1000);
			});
		});
	</script>
	<!-- start-smoth-scrolling -->

	<title>Slideshow</title>
	<style>
		ul#demo1 {
			width: 90%;
			height: 50px;
			overflow: hidden;
			padding: 0;
			margin: 0;
			list-style: none;
		}

		ul#demo1 li {
			width: 100%;
			height: 100%;
		}

		ul#demo1 li img {
			width: 100%;
			height: 100%;
			object-fit: contain;
			object-position: center;
			display: block;
		}

		.visit-counter {
			background-color: rgba(255, 255, 255, 0.05);
			padding: 15px;
			border-radius: 5px;
		}

		.visit-counter h3 {
			color: #fff;
			font-size: 1.2em;
			margin-bottom: 15px;
		}

		.visit-counter .info {
			list-style: none;
			padding: 0;
		}

		.visit-counter .info li {
			color: #999;
			margin-bottom: 10px;
			font-size: 0.9em;
		}

		.visit-counter .info li i {
			margin-right: 10px;
			width: 20px;
			color: #fff;
		}

		@media (max-width: 991px) {
			.visit-counter {
				margin-top: 30px;
			}
		}
	</style>
</head>

<body>
	<!-- header -->
	<div class="agileits_header">
		<div class="container">
			<div class="w3l_offers">
				<p>DAPATKAN PENAWARAN MENARIK KHUSUS HARI INI, BELANJA SEKARANG!</p>
			</div>
			<div class="agile-login">
				<ul>
					<?php
					if (!isset($_SESSION['log'])) {
						echo '
					<li><a href="registered.php"> Daftar</a></li>
					<li><a href="login.php">Masuk</a></li>
					';
					} else {

						if ($_SESSION['role'] == 'Member') {
							echo '
					<li style="color:white">Halo, ' . $_SESSION["name"] . '
					<li><a href="logout.php">Keluar?</a></li>
					';
						} else {
							echo '
					<li style="color:white">Halo, ' . $_SESSION["name"] . '
					<li><a href="admin">Admin Panel</a></li>
					<li><a href="logout.php">Keluar?</a></li>
					';
						};
					}
					?>

				</ul>
			</div>
			<div class="product_list_header">
				<a href="cart.php"><button class="w3view-cart" type="submit" name="submit" value=""><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
				</a>
			</div>
			<div class="clearfix"> </div>
		</div>
	</div>

	<div class="logo_products">
		<div class="container">
			<div class="w3ls_logo_products_left1">
				<ul class="phone_email">
					<li><i class="fa fa-phone" aria-hidden="true"></i>Hubungi Kami : 081282357101</li>
				</ul>
			</div>
			<div class="w3ls_logo_products_left">
				<h1><a href="index.php">Bandeng Bu Yuwono</a></h1>
			</div>
			<div class="w3l_search">
				<form action="search.php" method="post">
					<input type="search" name="Search" placeholder="Cari produk...">
					<button type="submit" class="btn btn-default search" aria-label="Left Align">
						<i class="fa fa-search" aria-hidden="true"> </i>
					</button>
					<div class="clearfix"></div>
				</form>
			</div>

			<div class="clearfix"> </div>
		</div>
	</div>
	<!-- //header -->
	<!-- navigation -->
	<div class="navigation-agileits">
		<div class="container">
			<nav class="navbar navbar-default">
				<div class="navbar-header nav_2">
					<button type="button" class="navbar-toggle collapsed navbar-toggle1" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="bs-megadropdown-tabs">
					<ul class="nav navbar-nav">
						<li class="active"><a href="index.php" class="act">Home</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Kategori Produk<b class="caret"></b></a>
							<ul class="dropdown-menu multi-column columns-3">
								<div class="row">
									<div class="multi-gd-img">
										<ul class="multi-column-dropdown">
											<h6>Kategori</h6>

											<?php
											$kat = mysqli_query($conn, "SELECT * from kategori order by idkategori ASC");
											while ($p = mysqli_fetch_array($kat)) {

											?>
												<li><a href="kategori.php?idkategori=<?php echo $p['idkategori'] ?>"><?php echo $p['namakategori'] ?></a></li>

											<?php
											}
											?>
										</ul>
									</div>

								</div>
							</ul>
						</li>
						<li><a href="cart.php">Keranjang Saya</a></li>
						<li><a href="daftarorder.php">Daftar Order</a></li>
					</ul>
				</div>
			</nav>
		</div>
	</div>
	<!-- //navigation -->

	<!-- main-slider -->
	<?php if (!isset($_SESSION['log'])) { ?>
		<ul id="demo1">
			<li>
				<img src="images/Banner2 (950 x 400 px).png" alt="" />
			</li>
			<li>
				<img src="images/Banner1 (950 x 400 px).png" alt="" />
			</li>
		</ul>
	<?php } ?>
	<!-- //main-slider -->

	<!-- top-brands -->
	<div class="top-brands">
		<div class="container">
			<h2>Produk Kami</h2>
			<div class="grid_3 grid_5">
				<div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="expeditions" aria-labelledby="expeditions-tab">
							<div class="agile-tp">
								<h5>Penawaran Terbaik Minggu Ini
									<?php
									if (!isset($_SESSION['name'])) {
									} else {
										echo 'Untukmu, ' . $_SESSION['name'] . '!';
									}
									?>
								</h5>
							</div>
							<div class="agile_top_brands_grids">

								<?php
								$brgs = mysqli_query($conn, "SELECT * from produk order by idproduk ASC");
								$no = 1;
								while ($p = mysqli_fetch_array($brgs)) {

								?>
									<div class="col-md-4 top_brand_left">
										<div class="hover14 column">
											<div class="agile_top_brand_left_grid">
												<div class="agile_top_brand_left_grid_pos">
													<img src="images/offer.png" alt=" " class="img-responsive">
												</div>
												<div class="agile_top_brand_left_grid1">
													<figure>
														<div class="snipcart-item block">
															<div class="snipcart-thumb">
																<a href="product.php?idproduk=<?php echo $p['idproduk'] ?>"><img src="<?php echo $p['gambar'] ?>" width="300px" height="250px"></a>
																<p><?php echo $p['namaproduk'] ?></p>
																<div class="stars">
																	<i class="fa fa-star blue-star" aria-hidden="true"></i>
																	<i class="fa fa-star blue-star" aria-hidden="true"></i>
																	<i class="fa fa-star blue-star" aria-hidden="true"></i>
																	<i class="fa fa-star blue-star" aria-hidden="true"></i>
																	<i class="fa fa-star gray-star" aria-hidden="true"></i>
																</div>
																<h4>Rp<?php echo number_format($p['hargaafter']) ?> <span>Rp<?php echo number_format($p['hargabefore']) ?></span></h4>
															</div>
															<div class="snipcart-details top_brand_home_details">
																<fieldset>
																	<a href="product.php?idproduk=<?php echo $p['idproduk'] ?>"><input type="submit" class="button" value="Lihat Produk" /></a>
																</fieldset>
															</div>
														</div>
													</figure>
												</div>
											</div>
										</div>
									</div>
								<?php
								}
								?>

								<div class="clearfix"> </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- //top-brands -->

	<!-- //footer -->
	<div class="footer">
		<div class="container">
			<div class="w3_footer_grids">
				<div class="col-md-3 w3_footer_grid">
					<h3>Kontak</h3>
					<ul class="address">
						<li><i class="glyphicon glyphicon-map-marker" aria-hidden="true"></i>Bandeng Bu Yuwono, Kediri.</li>
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i><a href="mailto:bandengbuyuwono@gmail.com">bandengyuwono@gmail.com</a></li>
						<li><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>081282357101</li>
					</ul>
				</div>
				<div class="col-md-offset-6 col-md-3 w3_footer_grid">
					<div class="visit-counter">
						<h3>Statistik Kunjungan</h3>
						<ul class="info">
							<li><i class="fa fa-users"></i> Hari Ini: <?php echo $total_today_visits; ?></li>
							<li><i class="fa fa-calendar"></i> Bulan Ini: <?php echo $total_monthly_visits; ?></li>
							<li><i class="fa fa-calendar-check-o"></i> Tahun Ini: <?php echo $total_yearly_visits; ?></li>
							<li><i class="fa fa-line-chart"></i> Total Kunjungan: <?php echo $total_visits; ?></li>
							<li><i class="fa fa-user"></i> Pengunjung Online: <?php echo $total_online_visitors; ?></li>
						</ul>
					</div>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>

		<div class="footer-copy">
			<div class="container">
				<p>© 2024 Bandeng Bu Yuwono.</p>
			</div>
		</div>

	</div>
	<!-- //footer -->

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>

	<!-- top-header and slider -->
	<!-- here stars scrolling icon -->
	<script type="text/javascript">
		$(document).ready(function() {

			var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 4000,
				easingType: 'linear'
			};

			$().UItoTop({
				easingType: 'easeOutQuart'
			});

		});
	</script>
	<!-- //here ends scrolling icon -->

	<!-- main slider-banner -->
	<script src="js/skdslider.min.js"></script>
	<link href="css/skdslider.css" rel="stylesheet">
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#demo1').skdslider({
				'delay': 5000,
				'animationSpeed': 2000,
				'showNextPrev': true,
				'showPlayButton': true,
				'autoSlide': true,
				'animationType': 'fading'
			});

			jQuery('#responsive').change(function() {
				$('#responsive_wrapper').width(jQuery(this).val());
			});

		});
	</script>
	<a href="#home" id="toTop" class="scroll" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>
	<!-- //smooth scrolling -->
</body>

</html>