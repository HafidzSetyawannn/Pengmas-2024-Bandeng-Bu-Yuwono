<?php
include '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];

    $deletekategori = mysqli_query($conn, "DELETE FROM kategori WHERE idkategori='$id'");

    if ($deletekategori) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'Invalid Request';
}
