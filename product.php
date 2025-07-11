<?php
session_start();
include 'dbconnect.php';

$idproduk = $_GET['idproduk'];

if (isset($_POST['addprod'])) {
	if (!isset($_SESSION['log'])) {
	    header('location:login.php');
	} else {
	    $ui = $_SESSION['id'];
	    $idprod = $_POST['idprod'];
	    $idvarian = $_POST['varian']; // Ambil pilihan varian
	    $cek = mysqli_query($conn, "SELECT * FROM cart WHERE userid='$ui' AND status='Cart'");
	    $liat = mysqli_num_rows($cek);
	    $f = mysqli_fetch_array($cek);
	    $orid = $f['orderid'];
 
	    if ($liat > 0) {
		   // Cek apakah varian produk sudah ada di keranjang
		   $cekbrg = mysqli_query($conn, "SELECT * FROM detailorder WHERE idproduk='$idprod' AND orderid='$orid' AND idvarian='$idvarian'");
		   $liatlg = mysqli_num_rows($cekbrg);
 
		   if ($liatlg > 0) {
			  // Jika sudah ada, update jumlah
			  $brpbanyak = mysqli_fetch_array($cekbrg);
			  $jmlh = $brpbanyak['qty'];
			  $baru = $jmlh + 1;
			  $updateaja = mysqli_query($conn, "UPDATE detailorder SET qty='$baru' WHERE orderid='$orid' AND idproduk='$idprod' AND idvarian='$idvarian'");
 
			  if ($updateaja) {
				 echo "<div class='alert alert-success'>Barang sudah pernah dimasukkan ke keranjang, jumlah akan ditambahkan</div>";
			  } else {
				 echo "<div class='alert alert-warning'>Gagal menambahkan ke keranjang</div>";
			  }
		   } else {
			  // Jika belum ada, tambahkan produk dan varian ke keranjang
			  $tambahdata = mysqli_query($conn, "INSERT INTO detailorder (orderid, idproduk, qty, idvarian) VALUES('$orid', '$idprod', '1', '$idvarian')");
			  if ($tambahdata) {
				 echo "<div class='alert alert-success'>Berhasil menambahkan ke keranjang</div>";
			  } else {
				 echo "<div class='alert alert-warning'>Gagal menambahkan ke keranjang</div>";
			  }
		   }
	    } else {
		   // Jika belum ada order ID, buat order ID baru dan tambahkan produk serta varian ke keranjang
		   $oi = crypt(rand(22, 999), time());
		   $bikincart = mysqli_query($conn, "INSERT INTO cart (orderid, userid) VALUES('$oi','$ui')");
		   if ($bikincart) {
			  $tambahuser = mysqli_query($conn, "INSERT INTO detailorder (orderid, idproduk, qty, idvarian) VALUES('$oi', '$idprod', '1', '$idvarian')");
			  if ($tambahuser) {
				 echo "<div class='alert alert-success'>Berhasil menambahkan ke keranjang</div>";
			  } else {
				 echo "<div class='alert alert-warning'>Gagal menambahkan ke keranjang</div>";
			  }
		   } else {
			  echo "Gagal membuat keranjang";
		   }
	    }
	}
 };
 ?>

<!DOCTYPE html>
<html>

<head>
	<title>Hasil Alam - Produk</title>
	<style>
		.snipcart-details fieldset {
			display: flex;
			gap: 10px;
			padding: 0;
			border: none;
		}

		.snipcart-details .button {
			flex: 1;
			padding: 10px 30px;
			background-color: #5cb85c;
			color: white;
			border: none;
			cursor: pointer;
			text-align: center;
			text-decoration: none;
		}

		/* Styling untuk tombol ShopeeFood dan GoFood sama dengan Tambahkan Keranjang */
		.snipcart-details .button-shopee,
		.snipcart-details .button-gofood {
			flex: 1;
			padding: 10px 30px;
			/* Menyamakan padding */
			color: white;
			border: none;
			cursor: pointer;
			text-align: center;
			text-decoration: none;
		}

		.snipcart-details .button:hover {
			background-color: #4cae4c;
		}

		/* GoFood (Hijau) */
		.snipcart-details .button-gofood {
			background-color: #28a745;
		}

		.snipcart-details .button-gofood:hover {
			background-color: #218838;
		}

		/* Shopee Food (Oranye) */
		.snipcart-details .button-shopee {
			background-color: #ff6600;
		}

		.snipcart-details .button-shopee:hover {
			background-color: #cc5200;
		}

		.button-add {
			padding: 10px 20px;
			margin: 2px;
			text-transform: none;
		}
	</style>
	<!-- for-mobile-apps -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Tokopekita, Richard's Lab" />
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
				<!-- Brand and toggle get grouped for better mobile display -->
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
						<!-- Mega Menu -->
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
						<li><a href="konfirmasi.php">Daftar Order</a></li>
					</ul>
				</div>
			</nav>
		</div>
	</div>

	<!-- //navigation -->
	<!-- breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<ol class="breadcrumb breadcrumb1 animated wow slideInLeft" data-wow-delay=".5s">
				<li><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Home</a></li>
				<li class="active"><?php
								$p = mysqli_fetch_array(mysqli_query($conn, "Select * from produk where idproduk='$idproduk'"));
								echo $p['namaproduk'];
								// Ambil data varian produk dari tabel varian_produk
								$varian_query = mysqli_query($conn, "SELECT * FROM varian_produk WHERE idproduk='$idproduk'");
								?></li>
			</ol>
		</div>
	</div>
	<!-- //breadcrumbs -->
	<div class="products">
		<div class="container">
			<div class="agileinfo_single">

				<div class="col-md-4 agileinfo_single_left">
					<img id="example" src="<?php echo $p['gambar'] ?>" alt=" " class="img-responsive">
				</div>
				<div class="col-md-8 agileinfo_single_right">
					<h2><?php echo $p['namaproduk'] ?></h2>
					<div class="rating1">
						<span class="starRating">
							<?php
							$bintang = '<i class="fa fa-star blue-star" aria-hidden="true"></i>';
							$rate = $p['rate'];

							for ($n = 1; $n <= $rate; $n++) {
								echo '<i class="fa fa-star blue-star" aria-hidden="true"></i>';
							};
							?>
						</span>
					</div>
					<div class="w3agile_description">
						<h4>Deskripsi :</h4>
						<p><?php echo $p['deskripsi'] ?></p>
					</div>
					<!-- Formulir Pilihan Varian -->
					<form action="#" method="post" style="flex: 1;">
						<input type="hidden" name="idprod" value="<?php echo $idproduk ?>">

						<div class="form-group">
							<label for="varian">Pilih Varian:</label>
							<select name="varian" id="varian" class="form-control">
								<?php while ($varian = mysqli_fetch_array($varian_query)) { ?>
									<option value="<?php echo $varian['idvarian']; ?>">
										<?php echo $varian['varian_nama']; ?> - Rp. <?php echo number_format($varian['harga']); ?>
									</option>
								<?php } ?>
							</select>
						</div>
						<div class="snipcart-details agileinfo_single_right_details">
							<fieldset>
								<!-- Tombol Tambah ke Keranjang -->
								<form action="#" method="post" style="flex: 1;">
									<input type="hidden" name="idprod" value="<?php echo $idproduk ?>">
									<input type="submit" name="addprod" value="Tambahkan Keranjang" class="button button-add">
								</form>

								<!-- Tombol ShopeeFood -->
								<a href="https://shopee.co.id/universal-link/now-food/shop/21876722?deep_and_deferred=1&shareChannel=whatsapp"
									target="_blank" class="button button-shopee">
									<i class="fa fa-shopping-cart"></i> ShopeeFood
								</a>

								<!-- Tombol GoFood -->
								<a href="https://gofood.link/a/NxcpuNQ"
									target="_blank" class="button button-gofood">
									<i class="fa fa-motorcycle"></i> GoFood
								</a>
							</fieldset>
						</div>
				</div>
			</div>
			<div class="clearfix"> </div>
		</div>
	</div>
	</div>

	<!-- //footer -->
	<div class="footer">
		<div class="container">
			<div class="w3_footer_grids">
				<div class="col-md-4 w3_footer_grid">
					<h3>Hubungi Kami</h3>

					<ul class="address">
						<li><i class="glyphicon glyphicon-map-marker" aria-hidden="true"></i>Bandeng Bu Yuwono, Kediri.</li>
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i><a href="mailto:bandengyuwono@email">bandengyuwono@gmail.com</a></li>
						<li><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>081282357101</li>
					</ul>
				</div>
				<div class="col-md-3 w3_footer_grid">
					<h3>Tentang Kami</h3>
					<ul class="info">
						<li><i class="fa fa-arrow-right" aria-hidden="true"></i><a href="about.html">About Us</a></li>
						<li><i class="fa fa-arrow-right" aria-hidden="true"></i><a href="about.html">How To</a></li>
						<li><i class="fa fa-arrow-right" aria-hidden="true"></i><a href="about.html">FAQ</a></li>
					</ul>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>

		<div class="footer-copy">

			<div class="container">
				<p>© 2024 Bandeng Bu Yuwono</p>
			</div>
		</div>

	</div>
	<div class="footer-botm">
		<div class="container">
			<div class="w3layouts-foot">
				<ul>
					<li><a href="#" class="w3_agile_instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
					<li><a href="#" class="w3_agile_facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
					<li><a href="#" class="agile_twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
				</ul>
			</div>
			<div class="payment-w3ls">
				<img src="images/card.png" alt=" " class="img-responsive">
			</div>
			<div class="clearfix"> </div>
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
	<!-- //main slider-banner -->
</body>

</html>