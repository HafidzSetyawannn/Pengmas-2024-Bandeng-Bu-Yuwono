<?php
include '../dbconnect.php'; // Sesuaikan path ke file koneksi database

if (isset($_POST['updateproduct'])) {
    $idproduk = $_POST['idproduk'];
    $namaproduk = $_POST['namaproduk'];
    $idkategori = $_POST['idkategori'];
    $deskripsi = $_POST['deskripsi'];
    $rate = $_POST['rate'];
    $hargabefore = $_POST['hargabefore'];
    $hargaafter = $_POST['hargaafter'];

    // Proses penggantian gambar jika diunggah
    if (!empty($_FILES['uploadgambar']['name'])) {
        $nama_file = $_FILES['uploadgambar']['name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $random = crypt($nama_file, time());
        $ukuran_file = $_FILES['uploadgambar']['size'];
        $tipe_file = $_FILES['uploadgambar']['type'];
        $tmp_file = $_FILES['uploadgambar']['tmp_name'];
        $path = "../produk/" . $random . '.' . $ext;
        $pathdb = "produk/" . $random . '.' . $ext;

        // Validasi tipe dan ukuran file gambar
        if ($tipe_file == "image/jpeg" || $tipe_file == "image/png") {
            if ($ukuran_file <= 5000000) { // 5 MB
                if (move_uploaded_file($tmp_file, $path)) {
                    // Hapus gambar lama jika berhasil diunggah gambar baru
                    $query_select_old_image = mysqli_query($conn, "SELECT gambar FROM produk WHERE idproduk = '$idproduk'");
                    $old_image = mysqli_fetch_assoc($query_select_old_image)['gambar'];
                    if ($old_image && file_exists("../" . $old_image)) {
                        unlink("../" . $old_image);
                    }

                    // Update data produk dengan gambar baru
                    $query_update = "UPDATE produk SET idkategori='$idkategori', namaproduk='$namaproduk', gambar='$pathdb', deskripsi='$deskripsi', rate='$rate', hargabefore='$hargabefore', hargaafter='$hargaafter' WHERE idproduk='$idproduk'";
                    $sql_update = mysqli_query($conn, $query_update);

                    if ($sql_update) {
                        header("Location: produk.php");
                        exit();
                    } else {
                        echo "Gagal mengupdate data produk.";
                    }
                } else {
                    echo "Gagal mengunggah file gambar.";
                }
            } else {
                echo "Ukuran file gambar tidak boleh lebih dari 5 MB.";
            }
        } else {
            echo "Tipe file gambar harus JPEG atau PNG.";
        }
    } else {
        // Update data produk tanpa mengganti gambar
        $query_update = "UPDATE produk SET idkategori='$idkategori', namaproduk='$namaproduk', deskripsi='$deskripsi', rate='$rate', hargabefore='$hargabefore', hargaafter='$hargaafter' WHERE idproduk='$idproduk'";
        $sql_update = mysqli_query($conn, $query_update);

        if ($sql_update) {
            header("Location: produk.php");
            exit();
        } else {
            echo "Gagal mengupdate data produk.";
        }
    }
} else {
    echo "Akses tidak sah!";
}