<?php
include '../dbconnect.php';

if (isset($_POST['createvariant'])) {
    $idproduk = $_POST['idproduk'];
    $varian_nama = $_POST['varian_nama'];
    $harga = $_POST['harga'];

    $query = "INSERT INTO varian_produk (idproduk, varian_nama, harga) VALUES ('$idproduk', '$varian_nama', '$harga')";
    if (mysqli_query($conn, $query)) {
        header("Location: variant.php");
        exit();
    } else {
        echo "Gagal menambahkan varian.";
    }
}
?>

