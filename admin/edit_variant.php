<?php
include '../dbconnect.php';

if (isset($_GET['id'])) {
    $idvarian = $_GET['id'];
    $query = "SELECT * FROM varian WHERE idvarian = '$idvarian'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
}

if (isset($_POST['updatevariant'])) {
    $idvarian = $_POST['idvarian'];
    $idproduk = $_POST['idproduk'];
    $varian_nama = $_POST['varian_nama'];
    $harga = $_POST['harga'];

    $query = "UPDATE varian SET idproduk='$idproduk', varian_nama='$varian_nama', harga='$harga' WHERE idvarian='$idvarian'";
    if (mysqli_query($conn, $query)) {
        header("Location: list_variant.php");
        exit();
    } else {
        echo "Gagal mengupdate varian.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ubah Varian</title>
</head>
<body>
    <h1>Ubah Varian</h1>
    <form method="POST">
        <input type="hidden" name="idvarian" value="<?= $data['idvarian'] ?>">
        <input type="text" name="idproduk" value="<?= $data['idproduk'] ?>" required>
        <input type="text" name="varian_nama" value="<?= $data['varian_nama'] ?>" required>
        <input type="number" name="harga" value="<?= $data['harga'] ?>" required>
        <button type="submit" name="updatevariant">Simpan</button>
    </form>
</body>
</html>
