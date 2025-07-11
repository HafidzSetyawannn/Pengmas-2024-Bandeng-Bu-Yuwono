<?php
include '../dbconnect.php'; // Sesuaikan path ke file koneksi database

if (isset($_POST['createproduct'])) {
    $namaproduk = $_POST['namaproduk'];
    $idkategori = $_POST['idkategori'];
    $deskripsi = $_POST['deskripsi'];
    $rate = $_POST['rate'];
    $hargabefore = $_POST['hargabefore'];
    $hargaafter = $_POST['hargaafter'];

    // Proses pengunggahan gambar
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
                    // Masukkan data produk baru ke database
                    $query_insert = "INSERT INTO produk (idkategori, namaproduk, gambar, deskripsi, rate, hargabefore, hargaafter) 
                                     VALUES ('$idkategori', '$namaproduk', '$pathdb', '$deskripsi', '$rate', '$hargabefore', '$hargaafter')";
                    $sql_insert = mysqli_query($conn, $query_insert);

                    if ($sql_insert) {
                        header("Location: produk.php");
                        exit();
                    } else {
                        echo "Gagal menambahkan data produk.";
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
        echo "Harap unggah gambar untuk produk.";
    }
} else {
    echo "Akses tidak sah!";
}
