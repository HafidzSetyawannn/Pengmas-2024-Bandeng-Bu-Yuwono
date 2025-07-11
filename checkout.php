<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['log'])) {
	header('location:login.php');
} else {
};
$uid = $_SESSION['id'];
$caricart = mysqli_query($conn, "select * from cart where userid='$uid' and status='Cart'");
$fetc = mysqli_fetch_array($caricart);
$orderidd = $fetc['orderid'];
$itungtrans = mysqli_query($conn, "select count(detailid) as jumlahtrans from detailorder where orderid='$orderidd'");
$itungtrans2 = mysqli_fetch_assoc($itungtrans);
$itungtrans3 = $itungtrans2['jumlahtrans'];

if (isset($_POST["checkout"])) {
	$q3 = mysqli_query($conn, "update cart set status='Payment' where orderid='$orderidd'");
	if ($q3) {
		echo "Berhasil Check Out
        <meta http-equiv='refresh' content='1; url= index.php'/>";
	} else {
		echo "Gagal Check Out
        <meta http-equiv='refresh' content='1; url= index.php'/>";
	}
} else {
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Bandeng Bu Yuwono - Checkout</title>
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
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/font-awesome.css" rel="stylesheet">
	<script src="js/jquery-1.11.1.min.js"></script>
	<link href='//fonts.googleapis.com/css?family=Raleway:400,100,100italic,200,200italic,300,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
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
</head>

<body>
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
                    <li><a href="login.php">Masuk</a></li>';
					} else {
						if ($_SESSION['role'] == 'Member') {
							echo '
                    <li style="color:white">Halo, ' . $_SESSION["name"] . '</li>
                    <li><a href="logout.php">Keluar?</a></li>';
						} else {
							echo '
                    <li style="color:white">Halo, ' . $_SESSION["name"] . '</li>
                    <li><a href="admin">Admin Panel</a></li>
                    <li><a href="logout.php">Keluar?</a></li>';
						}
					}
					?>
				</ul>
			</div>
			<div class="product_list_header">
				<a href="cart.php"><button class="w3view-cart" type="submit" name="submit" value=""><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button></a>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<div class="logo_products">
		<div class="container">
			<div class="w3ls_logo_products_left1">
				<ul class="phone_email">
					<li><i class="fa fa-phone" aria-hidden="true"></i>Contact Us : 0857 7747 2772</li>
				</ul>
			</div>
			<div class="w3ls_logo_products_left">
				<h1><a href="index.php">Bandeng Bu Yuwono</a></h1>
			</div>
			<div class="w3l_search">
				<form action="search.php" method="post">
					<input type="search" name="Search" placeholder="Cari produk...">
					<button type="submit" class="btn btn-default search" aria-label="Left Align">
						<i class="fa fa-search" aria-hidden="true"></i>
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

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
												echo '<li><a href="kategori.php?idkategori=' . $p['idkategori'] . '">' . $p['namakategori'] . '</a></li>';
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

	<div class="breadcrumbs">
		<div class="container">
			<ol class="breadcrumb breadcrumb1">
				<li><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Home</a></li>
				<li class="active">Checkout</li>
			</ol>
		</div>
	</div>

	<div class="checkout">
		<div class="container">
			<h1>Terima kasih, <?= $_SESSION['name'] ?> telah membeli <?php echo $itungtrans3 ?> di Bandeng Bu Yuwono</h1>
			<br>
			<div class="checkout-right">
				<table class="timetable_sub">
					<thead>
						<tr>
							<th>No.</th>
							<th>Produk</th>
							<th>Nama Produk</th>
							<th>Jumlah</th>
							<th>Sub Total</th>
							<th>Hapus</th>
						</tr>
					</thead>
					<?php
					$brg = mysqli_query($conn, "SELECT * FROM detailorder d
JOIN produk p ON d.idproduk = p.idproduk
JOIN varian_produk v ON d.idvarian = v.idvarian
WHERE d.orderid='$orderidd' 
ORDER BY d.idproduk ASC");
					$no = 1;
					$subtotal = 0;
					$ongkir = 10000; // Fixed shipping cost

					while ($b = mysqli_fetch_array($brg)) {
						// Get the price after variant, assuming variant price is stored in 'v.price'
						$hrg = $b['hargaafter']; // Base product price
						$variant_price = $b['harga']; // Variant-specific price (if any)
						$final_price = $variant_price; // Final price after variant

						$qtyy = $b['qty'];
						$totalharga = $final_price * $qtyy;
						$subtotal += $totalharga;

						echo '<tr class="rem1">
<form method="post">
<td class="invert">' . $no++ . '</td>
<td class="invert"><a href="product.php?idproduk=' . $b['idproduk'] . '"><img src="' . $b['gambar'] . '" width="100px" height="100px" /></a></td>
<td class="invert">' . $b['namaproduk'] . ' (' . $b['varian_nama'] . ')</td> <!-- Variant name -->
<td class="invert"><div class="quantity"> <div class="quantity-select"> <input type="text" class="text-center" value="' . $b['qty'] . '" readonly> </div></div></td>
<td class="invert">Rp' . number_format($totalharga) . '</td>
<td class="invert"><a href="hapusorder.php?idproduk=' . $b['idproduk'] . '&orderid=' . $orderidd . '" onclick="return confirm(\'Hapus item ini dari keranjang?\')"><input type="button" class="btn btn-danger" value="Hapus"/></a></td>
</form>
</tr>';
					}
					?>
					<tr>
						<td colspan="4" style="text-align:right;"><strong>Subtotal</strong></td>
						<td>Rp<?php echo number_format($subtotal); ?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:right;"><strong>Ongkos Kirim</strong></td>
						<td>Rp<?php echo number_format($ongkir); ?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:right;"><strong>Total</strong></td>
						<td>Rp<?php echo number_format($subtotal + $ongkir); ?></td>
						<td></td>
					</tr>
				</table>
			</div>
			<div class="checkout-right-basket">
				<form action="vendor/midtrans/midtrans-php/examples/snap/checkout-process-simple-version.php" method="post">
					<input type="submit" class="form-control btn btn-success" name="checkout" value="Bayar Sekarang">
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<div class="footer">
		<div class="container">
			<div class="w3_footer_grids">
				<div class="col-md-3 w3_footer_grid">
					<h3>Contact</h3>
					<ul class="address">
						<li><i class="glyphicon glyphicon-map-marker" aria-hidden="true"></i>Kediri</li>
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i><a href="mailto:info@example.com">bu.yuwono@bandeng.com</a></li>
						<li><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>0857 7747 2772</li>
					</ul>
				</div>
				<div class="col-md-3 w3_footer_grid">
					<h3>Information</h3>
					<ul class="info">
						<li><a href="about.html">Tentang Kami</a></li>
						<li><a href="contact.html">Hubungi Kami</a></li>
						<li><a href="faq.html">FAQ</a></li>
					</ul>
				</div>
				<div class="col-md-3 w3_footer_grid">
					<h3>Kategori</h3>
					<ul class="info">
						<?php
						$kat = mysqli_query($conn, "SELECT * from kategori order by idkategori ASC");
						while ($p = mysqli_fetch_array($kat)) {
							echo '<li><a href="kategori.php?idkategori=' . $p['idkategori'] . '">' . $p['namakategori'] . '</a></li>';
						}
						?>
					</ul>
				</div>
				<div class="col-md-3 w3_footer_grid">
					<h3>Profil</h3>
					<ul class="info">
						<li><a href="index.php">Home</a></li>
						<li><a href="cart.php">Keranjang Saya</a></li>
						<li><a href="daftarorder.php">Daftar Order</a></li>
					</ul>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="footer-copy">
			<div class="container">
				<p>© 2024 Bandeng Bu Yuwono. All rights reserved</p>
			</div>
		</div>
	</div>
	<script src="js/bootstrap.min.js"></script>
</body>

</html>