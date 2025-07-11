<?php

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php'; // Pastikan path benar

// Set server key dan client key Midtrans
Config::$serverKey = 'SB-Mid-server-s681VL9VN7K38oIbiaDOv_3p';
Config::$clientKey = 'SB-Mid-client-e42GQW6gcpgEQ89I'; // Pastikan clientKey sudah diset

// Uncomment untuk lingkungan produksi
// Config::$isProduction = true;
Config::$isSanitized = Config::$is3ds = true;

// Mulai session
session_start();
include 'dbconnect.php';

$uid = $_SESSION['id'];

// Ambil detail keranjang
$caricart = mysqli_query($conn, "SELECT * FROM cart WHERE userid='$uid' AND status='Cart'");
$fetc = mysqli_fetch_array($caricart);
$orderidd = $fetc['orderid'] . '-' . time(); // Tambahkan timestamp agar order ID unik

// Ambil alamat user
$alamat_user = '';
$user_query = mysqli_query($conn, "SELECT alamat FROM login WHERE userid='$uid'");
if ($user_row = mysqli_fetch_array($user_query)) {
    $alamat_user = $user_row['alamat'];
}

// Hitung total harga dan buat item details
$subtotal = 0;
$item_details = [];

$brg = mysqli_query($conn, "
    SELECT p.namaproduk, vp.varian_nama, vp.harga, d.qty, vp.idvarian
    FROM detailorder d 
    JOIN produk p ON d.idproduk = p.idproduk 
    JOIN varian_produk vp ON d.idvarian = vp.idvarian 
    WHERE d.orderid = '$fetc[orderid]'
");

while ($b = mysqli_fetch_array($brg)) {
    $harga = $b['harga']; // Harga sesuai varian yang dipilih
    $qty = $b['qty'];
    $subtotal += $harga * $qty;

    // Tambahkan detail produk ke item_details
    $item_details[] = array(
        'id' => $b['idvarian'],
        'price' => $harga,
        'quantity' => $qty,
        'name' => $b['namaproduk'] . ' (' . $b['varian_nama'] . ')',
    );
}

// Ongkos kirim
$ongkir = 10000; // Ongkos kirim sebesar Rp10.000
$total = $subtotal + $ongkir;

// Tambahkan ongkir ke item details
$item_details[] = array(
    'id' => 'ongkir',
    'price' => $ongkir,
    'quantity' => 1,
    'name' => 'Ongkos Kirim',
);

// Data transaksi
$transaction_details = array(
    'order_id' => $orderidd,
    'gross_amount' => $total,
);

$transaction = array(
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
);

// Proses ketika form dikirimkan
$snap_token = '';
if (isset($_POST['submit'])) {
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']); // Ambil input alamat

    // Update database untuk menyimpan alamat
    $update_cart = mysqli_query($conn, "UPDATE cart SET alamat='$alamat' WHERE userid='$uid' AND status='Cart'");

    if (!$update_cart) {
        echo "Gagal menyimpan alamat.";
    } else {
        try {
            // Dapatkan Snap Token setelah alamat tersimpan
            $snap_token = Snap::getSnapToken($transaction);

            if ($snap_token) {
                $update_status = mysqli_query($conn, "UPDATE cart SET status='Payment' WHERE userid='$uid' AND status='Cart'");

                if ($update_status) {
                    // Update detailorder untuk menambahkan tanggal_transaksi
                    $update_date = mysqli_query($conn, "UPDATE detailorder SET tanggal_transaksi = NOW() WHERE orderid='$fetc[orderid]'");

                    if (!$update_date) {
                        echo "Gagal memperbarui tanggal transaksi.";
                    }
                } else {
                    echo "Gagal memperbarui status pesanan.";
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran dengan Midtrans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .container h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #28a745;
        }

        .payment-info {
            margin-bottom: 30px;
        }

        label {
            font-size: 16px;
            color: #343a40;
            margin-bottom: 10px;
            display: block;
            text-align: left;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Pembayaran</h1>
        <div class="payment-info">
            <p>Selesaikan pembayaran Anda dengan aman menggunakan Midtrans.</p>
        </div>

        <!-- Form untuk alamat -->
        <form method="POST" action="">
            <label for="alamat">Alamat Pengiriman:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($alamat_user); ?>" required>

            <button type="submit" name="submit" id="pay-button">Bayar Sekarang</button>
        </form>

        <!-- Sembunyikan Snap Token dari tampilan -->
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey; ?>"></script>
        <script type="text/javascript">
            // Simpan Snap Token dalam variabel JavaScript
            var snapToken = '<?php echo $snap_token; ?>';

            // Fungsi untuk memproses pembayaran setelah alamat tersimpan
            if (snapToken) {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        alert('Pembayaran berhasil!');
                        console.log(result);
                        window.location.href = 'http://localhost/pengmas1/cart.php';
                    },
                    onPending: function(result) {
                        alert('Pembayaran tertunda!');
                        console.log(result);
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal!');
                        console.log(result);
                    }
                });
            }
        </script>
    </div>
</body>

</html>