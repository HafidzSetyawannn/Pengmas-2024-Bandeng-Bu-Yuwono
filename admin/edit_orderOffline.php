<?php
session_start();
include '../dbconnect.php';
date_default_timezone_set("Asia/Bangkok");

// Fetch order data based on the given ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $orderQuery = $conn->query("SELECT * FROM offline_orders WHERE id = $id");
    if ($orderQuery->num_rows > 0) {
        $order = $orderQuery->fetch_assoc();
        $products = json_decode($order['products'], true) ?? [];
    } else {
        $order = null;
        $products = [];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $customer_name = $_POST['customer_name'];
    $order_date = $_POST['order_date'];
    $address = $_POST['address'];

    $products = [];
    $product_ids = $_POST['product_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $total_price = 0;

    foreach ($product_ids as $index => $product_id) {
        $qty = $qtys[$index] ?? 0;
        $product_price = calculateProductPrice($product_id, $qty, $conn);
        $total_price += $product_price;
        $products[] = [
            'product_id' => $product_id,
            'qty' => $qty,
            'price' => $product_price
        ];
    }

    $products_json = json_encode($products);

    $stmt = $conn->prepare("UPDATE offline_orders SET customer_name = ?, order_date = ?, address = ?, products = ?, total_price = ? WHERE id = ?");
    $stmt->bind_param("ssssdi", $customer_name, $order_date, $address, $products_json, $total_price, $id);
    if ($stmt->execute()) {
        header("Location: offlineorder.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Helper function to calculate product price
function calculateProductPrice($product_id, $qty, $conn)
{
    $result = $conn->query("SELECT hargaafter FROM produk WHERE idproduk = $product_id");
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        return $product['hargaafter'] * $qty;
    }
    return 0;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pesanan Offline</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .form-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .product-item {
            margin-bottom: 15px;
        }

        .remove-product {
            cursor: pointer;
            color: #fff;
            background-color: #dc3545;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            height: fit-content;
        }

        .remove-product:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Edit Pesanan Offline</h2>
        <div class="form-section">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $order['id']; ?>">

                <div class="mb-3">
                    <label for="customer_name" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= $order['customer_name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="order_date" class="form-label">Tanggal Pemesanan</label>
                    <input type="datetime-local" class="form-control" id="order_date" name="order_date" value="<?= date('Y-m-d\TH:i', strtotime($order['order_date'])); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= $order['address']; ?></textarea>
                </div>

                <div id="product-list">
                    <label class="form-label">Produk</label>
                    <?php foreach ($products as $index => $product): ?>
                        <div class="row product-item align-items-center">
                            <div class="col-md-6 mb-3">
                                <select name="product_id[]" class="form-select" required>
                                    <option value="">Pilih Produk</option>
                                    <?php
                                    $all_products = $conn->query("SELECT * FROM produk");
                                    while ($row = $all_products->fetch_assoc()):
                                    ?>
                                        <option value="<?= $row['idproduk']; ?>" <?= $row['idproduk'] == $product['product_id'] ? 'selected' : ''; ?>>
                                            <?= $row['namaproduk']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <input type="number" class="form-control" name="qty[]" value="<?= $product['qty']; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="remove-product btn btn-danger btn-sm">Hapus</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" id="add-product" class="btn btn-success btn-sm mb-3">
                    Tambah Produk
                </button>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Tambah produk baru
            $('#add-product').click(function () {
                const productHtml = `
                <div class="row product-item">
                    <div class="col-md-6 mb-3">
                        <select name="product_id[]" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            <?php
                            $all_products = $conn->query("SELECT * FROM produk");
                            while ($row = $all_products->fetch_assoc()):
                            ?>
                                <option value="<?= $row['idproduk']; ?>"><?= $row['namaproduk']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="number" class="form-control" name="qty[]" placeholder="Jumlah" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="remove-product btn btn-danger btn-sm">Hapus</button>
                    </div>
                </div>`;
                $('#product-list').append(productHtml);
            });

            // Hapus produk
            $(document).on('click', '.remove-product', function () {
                $(this).closest('.product-item').remove();
            });
        });
    </script>
</body>
</html>
