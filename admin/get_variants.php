<?php
include '../dbconnect.php';

if (isset($_GET['product_id'])) {
     $product_id = $_GET['product_id'];

     // Query untuk mendapatkan varian berdasarkan produk
     $stmt = $conn->prepare("SELECT idvarian, varian_nama, harga FROM varian_produk WHERE idproduk = ?");
     $stmt->bind_param("i", $product_id);
     $stmt->execute();
     $result = $stmt->get_result();

     $variants = [];
     while ($row = $result->fetch_assoc()) {
          $variants[] = [
               'idvarian' => $row['idvarian'],
               'varian_nama' => $row['varian_nama'],
               'harga' => $row['harga'],
          ];
     }

     // Mengembalikan data dalam format JSON
     echo json_encode($variants);
}
