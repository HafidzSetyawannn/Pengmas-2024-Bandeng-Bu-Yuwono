<?php
// Hubungkan dengan database
include '../dbconnect.php';

if(isset($_GET['id'])) {
    // Ambil ID yang dikirimkan melalui parameter GET
    $id = $_GET['id'];

    // Perintah SQL untuk menghapus data
    $hapusmet = mysqli_query($conn, "DELETE FROM pembayaran WHERE no = '$id'");

    // Cek apakah proses penghapusan berhasil
    if($hapusmet) {
        // Jika berhasil, redirect ke halaman pembayaran.php
        echo "<script>
                alert('Data Metode Pembayaran berhasil dihapus');
                window.location.href='pembayaran.php';
              </script>";
        exit();
    } else {
        // Jika gagal, tampilkan pesan kesalahan
        echo "<script>
                alert('Gagal menghapus data Metode Pembayaran');
                window.location.href='pembayaran.php';
              </script>";
        exit();
    }
} else {
    // Jika tidak ada ID yang dikirimkan, kembalikan ke halaman pembayaran.php
    header("Location: pembayaran.php");
    exit();
}
?>
