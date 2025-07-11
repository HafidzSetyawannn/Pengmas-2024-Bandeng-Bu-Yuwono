<?php
include '../dbconnect.php';

if (isset($_GET['id'])) {
    $idvarian = $_GET['id'];
    $query = "DELETE FROM varian WHERE idvarian = '$idvarian'";
    if (mysqli_query($conn, $query)) {
        header("Location: list_variant.php");
        exit();
    } else {
        echo "Gagal menghapus varian.";
    }
}
?>
