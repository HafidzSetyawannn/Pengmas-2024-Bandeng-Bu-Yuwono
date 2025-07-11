<?php
session_start();
include '../dbconnect.php';

if (!isset($_GET['orderid'])) {
    echo "ID Pesanan tidak ditemukan!";
    exit;
}

$orderid = $_GET['orderid'];

// Ambil data pesanan berdasarkan orderid
$query = mysqli_query($conn, "SELECT * FROM cart WHERE orderid='$orderid'");
$order = mysqli_fetch_assoc($query);

if (!$order) {
    echo "Pesanan tidak ditemukan!";
    exit;
}

// Proses form update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $alamat = $_POST['alamat'];

    // Update data pesanan
    $updateQuery = "UPDATE cart SET status='$status', alamat='$alamat' WHERE orderid='$orderid'";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Pesanan berhasil diperbarui!'); window.location='manageorder.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui pesanan!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Pesanan</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="orderid">ID Pesanan</label>
                <input type="text" class="form-control" id="orderid" value="<?php echo $order['orderid']; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="status">Status Pesanan</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Diproses" <?php if ($order['status'] == 'Diproses') echo 'selected'; ?>>Diproses</option>
                    <option value="Dikirim" <?php if ($order['status'] == 'Dikirim') echo 'selected'; ?>>Dikirim</option>
                    <option value="Selesai" <?php if ($order['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                    <option value="Dibatalkan" <?php if ($order['status'] == 'Dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat Pengiriman</label>
                <textarea name="alamat" id="alamat" class="form-control" required><?php echo $order['alamat']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manageorder.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>

</html>
