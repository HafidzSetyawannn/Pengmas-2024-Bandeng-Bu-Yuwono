<?php
session_start();
include '../dbconnect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Menghapus pesanan berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM offline_orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: offlineorder.php");
} else {
    echo "ID pesanan tidak ditemukan.";
}
?>