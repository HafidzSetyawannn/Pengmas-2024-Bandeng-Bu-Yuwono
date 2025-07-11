<?php
session_start();
if (!isset($_SESSION['log'])) {
	header('location:login.php');
} else {
}

$idorder = $_GET['id'];
include 'dbconnect.php';

if (isset($_POST['confirm'])) {

	$userid = $_SESSION['id'];
	$veriforderid = mysqli_query($conn, "SELECT * FROM cart WHERE orderid='$idorder'");
	$fetch = mysqli_fetch_array($veriforderid);
	$liat = mysqli_num_rows($veriforderid);

	if ($liat > 0) {
		$nama = $_POST['nama'];

		// Handle file upload
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["bukti"]["name"]);
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$uploadOk = 1;

		// Check if file is a real image
		$check = getimagesize($_FILES["bukti"]["tmp_name"]);
		if ($check !== false) {
			$uploadOk = 1;
		} else {
			echo "<div class='alert alert-danger'>File is not an image.</div>";
			$uploadOk = 0;
		}

		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<div class='alert alert-danger'>Sorry, file already exists.</div>";
			$uploadOk = 0;
		}

		// Check file size (limit to 2MB)
		if ($_FILES["bukti"]["size"] > 2000000) {
			echo "<div class='alert alert-danger'>Sorry, your file is too large.</div>";
			$uploadOk = 0;
		}

		// Allow certain file formats
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
			echo "<div class='alert alert-danger'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<div class='alert alert-danger'>Sorry, your file was not uploaded.</div>";
		} else {
			// Try to upload file
			// Dalam kondisi berhasil upload file
			if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
				// Ambil hanya nama file
				$filename = basename($_FILES["bukti"]["name"]);

				// Update cart table dengan nama file
				$update_cart = mysqli_query($conn, "UPDATE cart SET namarekening='$nama', bukti='$filename', status='Confirmed' WHERE orderid='$idorder'");

				if ($update_cart) {
					echo "<div class='alert alert-success'>
			  Terima kasih telah melakukan konfirmasi, team kami akan melakukan verifikasi.
			</div>
			<meta http-equiv='refresh' content='7; url=index.php'/>";
				} else {
					echo "<div class='alert alert-warning'>
			  Gagal Submit, silakan ulangi lagi.
			</div>
			<meta http-equiv='refresh' content='3; url=konfirmasi.php'/>";
				}
			} else {
				echo "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
			}
		}
	} else {
		echo "<div class='alert alert-danger'>
                Kode Order tidak ditemukan, harap masukkan kembali dengan benar.
              </div>
              <meta http-equiv='refresh' content='4; url=konfirmasi.php'/>";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Bandeng Bu Yuwono - Konfirmasi Pembayaran</title>
	<!-- for-mobile-apps -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Tokopekita, Richard's Lab" />
	<!-- //for-mobile-apps -->
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/font-awesome.css" rel="stylesheet">
	<script src="js/jquery-1.11.1.min.js"></script>
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
                        <li><a href="login.php">Masuk</a></li>';
					} else {
						if ($_SESSION['role'] == 'Member') {
							echo '
                            <li style="color:white">Halo, ' . $_SESSION["name"] . '
                            <li><a href="logout.php">Keluar?</a></li>';
						} else {
							echo '
                            <li style="color:white">Halo, ' . $_SESSION["name"] . '
                            <li><a href="admin">Admin Panel</a></li>
                            <li><a href="logout.php">Keluar?</a></li>';
						}
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

	<!-- breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<ol class="breadcrumb breadcrumb1 animated wow slideInLeft" data-wow-delay=".5s">
				<li><a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Home</a></li>
				<li class="active">Konfirmasi</li>
			</ol>
		</div>
	</div>
	<!-- //breadcrumbs -->

	<!-- register -->
	<div class="register">
		<div class="container">
			<h2>Konfirmasi</h2>
			<div class="login-form-grids">
				<h3>Informasi Pembayaran</h3>
				<form method="post" enctype="multipart/form-data">
					<input type="text" name="nama" placeholder="Nama Pemilik Rekening / Sumber Dana" required>
					<br>
					<h3>Upload Bukti Pembayaran</h3>
					<input type="file" class="form-control" name="bukti" required>
					<input type="submit" name="confirm" value="Kirim">
				</form>
			</div>
			<div class="register-home">
				<a href="index.php">Batal</a>
			</div>
		</div>
	</div>
	<!-- //register -->

	<!-- footer -->
	<div class="footer">
		<div class="container">
			<div class="w3_footer_grids">
				<div class="col-md-4 w3_footer_grid">
					<h3>Hubungi Kami</h3>
					<ul class="address">
						<li><i class="glyphicon glyphicon-map-marker" aria-hidden="true"></i>Bandeng Bu Yuwono, Kediri.</li>
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i><a href="mailto:info@email">bandengyuwono@gmail.com</a></li>
						<li><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>+081282357101</li>
					</ul>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
	<!-- //footer -->
</body>

</html>