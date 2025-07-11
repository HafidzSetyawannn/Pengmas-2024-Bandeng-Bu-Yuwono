<?php
include '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_GET['id'];
    $namakategori = $_POST['namakategori'];

    $updatekategori = mysqli_query($conn, "UPDATE kategori SET namakategori='$namakategori' WHERE idkategori='$id'");

    if ($updatekategori) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'Invalid Request';
}
