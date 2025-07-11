<?php
session_start();
include '../dbconnect.php';

// Periksa apakah parameter 'orderid' ada
if (!isset($_GET['orderid'])) {
    echo "<script>alert('ID Pesanan tidak ditemukan!'); window.location='manageorder.php';</script>";
    exit;
}

$orderid = $_GET['orderid'];

// Hapus data pesanan dari tabel 'cart' dan 'detailorder'
$deleteCartQuery = "DELETE FROM cart WHERE orderid='$orderid'";
$deleteDetailQuery = "DELETE FROM detailorder WHERE orderid='$orderid'";

if (mysqli_query($conn, $deleteDetailQuery) && mysqli_query($conn, $deleteCartQuery)) {
    echo "<script>alert('Pesanan berhasil dihapus!'); window.location='manageorder.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus pesanan!'); window.location='manageorder.php';</script>";
}
?>
