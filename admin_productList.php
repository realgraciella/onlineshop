<?php
// product_list.php
include 'database/db_connect.php';

// Fetch products from the database
$query = "SELECT * FROM products";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product List</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Minified CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet"> <!-- Ensure minified CSS -->

    <!-- Deferred JS loading -->
    <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script defer src="assets/vendor/aos/aos.js"></script>
    <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script defer src="assets/js/admin.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 90px;
            color: #333;
        }

        .list-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #5f6368;
            color: white;
            text-transform: uppercase;
        }

        td {
            background-color: #f9f9f9;
        }

        .old-price {
            text-decoration: line-through;
            color: #999;
        }

        .on-sale {
            background-color: #e6f7e6;
            font-weight: bold;
        }

        .btn {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }

        #priceModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            z-index: 1000;
        }

        #priceModal h3 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        #priceModal input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        #priceModal label {
            font-size: 16px;
            color: #333;
        }

        .modal-btn-container {
            display: flex;
            justify-content: space-between;
        }

        .modal-btn-container button {
            width: 48%;
        }

        .cancel-btn {
            background-color: #f44336;
        }

        .cancel-btn:hover {
            background-color: #d32f2f;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

    <div class="list-container">
        <h2>Product List</h2>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Old Price</th>
                    <th>Stock Level</th>
                    <th>On Sale</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr class="<?= $product['on_sale'] ? 'on-sale' : '' ?>">
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td>
                            <?php if ($product['on_sale']) : ?>
                                <span class="old-price"><?= number_format($product['old_price'], 2) ?></span><br>
                                <?= number_format($product['price'], 2) ?>
                            <?php else : ?>
                                <?= number_format($product['price'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['old_price']) : ?>
                                <?= number_format($product['old_price'], 2) ?>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['stock_level']) ?></td>
                        <td>
                            <button class="btn" onclick="showPriceForm(<?= $product['product_id'] ?>, <?= $product['price'] ?>, <?= $product['on_sale'] ?>)">Modify Price</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="priceModal">
        <h3>Modify Price</h3>
        <form id="priceForm" method="POST">
            <input type="hidden" name="product_id" id="product_id">
            <div>
                <label for="new_price">New Price:</label>
                <input type="number" name="new_price" id="new_price" required step="0.01">
            </div>
            <div>
                <label for="on_sale">On Sale:</label>
                <input type="checkbox" name="on_sale" id="on_sale">
            </div>
            <div class="modal-btn-container">
                <button type="submit" class="btn">Save Changes</button>
                <button type="button" class="btn cancel-btn" onclick="closePriceForm()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Modal Overlay -->
    <div id="modalOverlay" class="modal-overlay" onclick="closePriceForm()"></div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showPriceForm(productId, currentPrice, onSale) {
            document.getElementById('product_id').value = productId;
            document.getElementById('new_price').value = currentPrice;
            document.getElementById('on_sale').checked = onSale == 1;
            document.getElementById('priceModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        }

        function closePriceForm() {
            document.getElementById('priceModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        }

        document.getElementById('priceForm').onsubmit = function(e) {
            e.preventDefault();
            let productId = document.getElementById('product_id').value;
            let newPrice = document.getElementById('new_price').value;
            let onSale = document.getElementById('on_sale').checked;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update_price.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'The product price has been updated.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error updating the price.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
            };
            xhr.send("product_id=" + productId + "&new_price=" + newPrice + "&on_sale=" + (onSale ? 1 : 0));
        };
    </script>
</body>
</html>
