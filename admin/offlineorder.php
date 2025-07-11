<?php
session_start();
include '../dbconnect.php';
date_default_timezone_set("Asia/Bangkok");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data input dari form
    $customer_name = $_POST['customer_name'];
    $order_date = $_POST['order_date'];
    $address = $_POST['address'];

    $product_ids = $_POST['product_id'];
    $variant_ids = $_POST['variant_id'];
    $qtys = $_POST['qty'];
    $total_price = 0;

    $products = [];
    foreach ($product_ids as $index => $product_id) {
        $variant_id = $variant_ids[$index];
        $qty = $qtys[$index];
        $product_price = calculateProductPrice($product_id, $variant_id, $qty, $conn);
        $total_price += $product_price;

        $products[] = [
            'product_id' => $product_id,
            'variant_id' => $variant_id,
            'qty' => $qty,
            'price' => $product_price
        ];
    }

    // Mengonversi array produk menjadi JSON
    $products_json = json_encode($products);

    // Menyimpan data pesanan ke dalam database
    $stmt = $conn->prepare("INSERT INTO offline_orders (customer_name, order_date, address, products, total_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $customer_name, $order_date, $address, $products_json, $total_price);
    $stmt->execute();
    $stmt->close();

    // Setelah sukses, alihkan ke halaman daftar pesanan offline
    header("Location: offlineorder.php");
    exit(); // Pastikan header redirect dieksekusi dengan benar
}

function calculateProductPrice($product_id, $variant_id, $qty, $conn)
{
    // Mengambil harga berdasarkan id varian
    $stmt = $conn->prepare("SELECT harga FROM varian_produk WHERE idvarian = ?");
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();

    // Jika varian ada, menghitung harga
    if ($variant) {
        return $variant['harga'] * $qty;
    }

    // Fallback jika varian tidak dipilih, gunakan harga produk
    $stmt = $conn->prepare("SELECT harga FROM produk WHERE idproduk = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    return $product['harga'] * $qty;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Kelola Offline Orders - Bandeng Bu Yuwono</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
    <style>
        /* Jika hanya ingin menghilangkan scrollbar pada sidebar */
        .sidebar-menu {
            overflow-y: hidden;
            /* Hides vertical scrollbar in sidebar */
        }

        /* Optional: If you want to hide the scrollbar for specific elements */
        .table-responsive {
            overflow: visible;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .action-buttons button {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="sidebar-menu">
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li class="active"><a href="index.php"><span>Home</span></a></li>
                            <li><a href="../"><span>Kembali ke Toko</span></a></li>
                            <li>
                                <a href="manageorder.php"><i class="ti-dashboard"></i><span>Kelola Pesanan</span></a>
                            </li>
                            <li>
                                <a href="offlineorder.php"><i class="ti-shopping-cart"></i><span>Kelola Pesanan Offline</span></a>
                            </li>
                            <li>
                                <a href="variant.php"><i class="ti-shopping-cart"></i><span>Kelola Varian</span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout"></i><span>Kelola Toko</span></a>
                                <ul class="collapse">
                                    <li><a href="kategori.php">Kategori</a></li>
                                    <li><a href="produk.php">Produk</a></li>
                                </ul>
                            </li>
                            <li><a href="customer.php"><span>Kelola Pelanggan</span></a></li>
                            <li><a href="user.php"><span>Kelola Staff</span></a></li>
                            <li>
                                <a href="pendapatan.php"><i class="fa fa-money"></i><span>Pendapatan</span></a>
                            </li>
                            <li>
                                <a href="../logout.php"><span>Logout</span></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <h3>Kelola Pesanan Offline</h3>
                    </div>
                </div>
            </div>

            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Daftar Pesanan Offline</h4>
                                <div class="data-tables datatable-dark">
                                    <table id="dataTable3" class="table table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pelanggan</th>
                                                <th>Tanggal Pemesanan</th>
                                                <th>Alamat</th>
                                                <th>Total Harga</th>
                                                <th>Produk</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $orders = $conn->query("SELECT * FROM offline_orders");
                                            while ($row = $orders->fetch_assoc()):
                                                $products = json_decode($row['products'], true);
                                            ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $row['customer_name']; ?></td>
                                                    <td><?= $row['order_date']; ?></td>
                                                    <td><?= $row['address']; ?></td>
                                                    <td>Rp<?= number_format($row['total_price']); ?></td>
                                                    <td>
                                                        <ul>
                                                            <?php foreach ($products as $product): ?>
                                                                <?php
                                                                // Get product name
                                                                $product_query = $conn->query("SELECT namaproduk FROM produk WHERE idproduk = " . $product['product_id']);
                                                                $product_name = $product_query->fetch_assoc()['namaproduk'];

                                                                // Get variant name with error handling
                                                                $variant_name = "Tanpa Varian";
                                                                if (!empty($product['variant_id'])) {
                                                                    $variant_query = $conn->query("SELECT varian_nama FROM varian_produk WHERE idvarian = " . $product['variant_id']);
                                                                    if ($variant_query && $variant_result = $variant_query->fetch_assoc()) {
                                                                        $variant_name = $variant_result['varian_nama'];
                                                                    }
                                                                }
                                                                ?>
                                                                <li><?= $product_name; ?> (Varian: <?= $variant_name; ?>) > Jumlah: <?= $product['qty']; ?>, Harga: Rp<?= number_format($product['price']); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <a href="edit_orderOffline.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                        <a href="delete_orderOffline.php?id=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Pesanan Offline</h4>
                                <form method="POST">
                                    <div class="form-group">
                                        <label>Nama Pelanggan</label>
                                        <input type="text" name="customer_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Pemesanan</label>
                                        <input type="datetime-local" name="order_date" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="address" class="form-control" required></textarea>
                                    </div>

                                    <div id="product-list">
                                        <div class="product-item mb-3">
                                            <label>Produk</label>
                                            <select name="product_id[]" class="form-control product-select" required>
                                                <option value="">Silahkan Pilih Produk</option>
                                                <?php
                                                $products = $conn->query("SELECT * FROM produk");
                                                while ($product = $products->fetch_assoc()):
                                                ?>
                                                    <option value="<?= $product['idproduk']; ?>"><?= $product['namaproduk']; ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                            <br>
                                            <label>Varian</label>
                                            <select name="variant_id[]" class="form-control variant-select" required>
                                                <option value="">Silahkan Pilih Varian</option>
                                            </select>
                                            <br>
                                            <label>Jumlah</label>
                                            <input type="number" name="qty[]" class="form-control" required>
                                            <div class="d-flex justify-content-end mt-2">
                                                <button type="button" class="btn btn-danger btn-sm remove-product">
                                                    <i class="fa fa-trash"></i> Hapus Produk
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" id="add-product" class="btn btn-success btn-sm">
                                            <i class="fa fa-plus"></i> Tambah Produk
                                        </button>
                                        <button type="submit" name="add" class="btn btn-primary btn-sm">
                                            <i class="fa fa-save"></i> Tambah Pesanan
                                        </button>
                                    </div>
                            </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <footer>
        <div class="footer-area">
            <p>Copyright Bandeng Presto Duri Lunak Bu Yuwono</p>
        </div>
    </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <!-- jquery latest version -->
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> -->
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <!-- start chart js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <!-- start highcharts js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- start zingchart js -->
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script>
        zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
        ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
    </script>
    <!-- all line chart activation -->
    <script src="assets/js/line-chart.js"></script>
    <!-- all pie chart -->
    <script src="assets/js/pie-chart.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable3').DataTable();
        });
    </script>

    <script>
        document.getElementById('add-product').addEventListener('click', function() {
            const productItem = document.querySelector('.product-item').cloneNode(true);
            productItem.querySelector('input').value = ''; // Clear quantity
            document.getElementById('product-list').appendChild(productItem);
        });

        document.getElementById('product-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-item').remove();
            }
        });
        document.querySelector('#product-list').addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const productId = e.target.value;
                const variantSelect = e.target.closest('.product-item').querySelector('.variant-select');

                // Clear previous variants
                variantSelect.innerHTML = '<option value="">Silahkan Pilih Varian</option>';

                if (productId) {
                    // Fetch variants for the selected product
                    fetch('get_variants.php?product_id=' + productId)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(variant => {
                                const option = document.createElement('option');
                                option.value = variant.idvarian;
                                option.textContent = variant.varian_nama + ' - Rp' + variant.harga;
                                variantSelect.appendChild(option);
                            });
                        });
                }
            }
        });
        // Menambahkan produk baru ke form
        document.querySelector('#add-product').addEventListener('click', function() {
            const productList = document.querySelector('#product-list');
            const newProduct = document.createElement('div');
            newProduct.classList.add('product-item', 'mb-3');
            newProduct.innerHTML = `
        <label>Produk</label>
        <select name="product_id[]" class="form-control product-select" required>
            <option value="">Silahkan Pilih Produk</option>
            <?php
            $products = $conn->query("SELECT * FROM produk");
            while ($product = $products->fetch_assoc()):
            ?>
                <option value="<?= $product['idproduk']; ?>" data-product-id="<?= $product['idproduk']; ?>"><?= $product['namaproduk']; ?></option>
            <?php endwhile; ?>
        </select>
        <br>
        <label>Varian</label>
        <select name="variant_id[]" class="form-control variant-select" required>
            <option value="">Silahkan Pilih Varian</option>
        </select>
        <br>
        <label>Jumlah</label>
        <input type="number" name="qty[]" class="form-control" required>
        <div class="d-flex justify-content-between mt-2">
            <button type="button" class="btn btn-danger btn-sm remove-product">
                <i class="fa fa-trash"></i> Hapus Produk
            </button>
        </div>
    `;
            productList.appendChild(newProduct);
        });

        // Menghapus produk dari form
        document.querySelector('#product-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-item').remove();
            }
        });
    </script>
</body>

</html>