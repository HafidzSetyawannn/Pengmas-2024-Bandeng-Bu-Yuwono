<?php
include '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $idproduk = $_GET['id'];

    // Hapus data produk
    $deleteproduk = mysqli_query($conn, "DELETE FROM produk WHERE idproduk='$idproduk'");

    if ($deleteproduk) {
        header("Location: produk.php");
        exit();
    } else {
        echo 'Gagal menghapus produk.';
    }
} else {
    echo 'Invalid Request';
}