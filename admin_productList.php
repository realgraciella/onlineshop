<?php
include 'database/db_connect.php';

// Handle search query if the form is submitted
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Fetch products and their variations from the database
$query = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_desc,
        pv.variation_value,
        pv.stock_per_variation,
        p.price,
        p.old_price,
        p.stock_level,
        p.on_sale
    FROM products p
    LEFT JOIN product_variations pv ON p.product_id = pv.product_id
    WHERE p.product_name LIKE :search_term OR p.product_desc LIKE :search_term
";
$stmt = $pdo->prepare($query);
$stmt->execute(['search_term' => '%' . $search_term . '%']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handling form submissions for price updates and stock changes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_price'])) {
        // Update price and sale status
        $product_id = $_POST['product_id'];
        $new_price = $_POST['new_price'];
        $on_sale = isset($_POST['on_sale']) ? 1 : 0;
        
        $update_query = "UPDATE products SET price = :new_price, on_sale = :on_sale, updated_at = NOW() WHERE product_id = :product_id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute(['new_price' => $new_price, 'on_sale' => $on_sale, 'product_id' => $product_id]);
        
        echo "<script>Swal.fire('Success', 'Successfully updated the price', 'success');</script>";
    }

    if (isset($_POST['update_stock'])) {
        // Update stock level
        $product_id = $_POST['product_id'];
        $new_stock = $_POST['new_stock'];

        $update_query = "UPDATE products SET stock_level = :new_stock, updated_at = NOW() WHERE product_id = :product_id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute(['new_stock' => $new_stock, 'product_id' => $product_id]);

        echo "<script>Swal.fire('Success', 'Successfully updated the stock level', 'success');</script>";
    }

    if (isset($_POST['delete_product'])) {
        // Delete product
        $product_id = $_POST['product_id'];

        $delete_query = "DELETE FROM products WHERE product_id = :product_id";
        $stmt = $pdo->prepare($delete_query);
        $stmt->execute(['product_id' => $product_id]);

        echo "<script>Swal.fire('Deleted', 'Product deleted successfully', 'success');</script>";
    }
}
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
    <link href="assets/css/admin.css" rel="stylesheet">

    <!-- Deferred JS loading -->
    <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script defer src="assets/vendor/aos/aos.js"></script>
    <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script defer src="assets/js/admin.js"></script>

    <style>
        /* Global Button Styles */
        h2 {
            margin-top: 50px auto;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .btn-del {
            background-color: #f44336;
            color: black;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #d32f2f;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-del:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }

        .btn-del:active {
            background-color: #004085;
            transform: translateY(1px);
        }

        .btn-mod {
            background-color: #ffe165;
            color: black;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #edc730;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-mod:hover {
            background-color: #edc730;
            color: white;
            transform: translateY(-2px);
        }

        .btn-mod:active {
            background-color: #004085;
            transform: translateY(1px);
        }

        .btn-res {
            background-color: #4CAF50;
            color: black;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #008a00;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-res:hover {
            background-color: #008a00;
            color: white;
            transform: translateY(-2px);
        }

        .btn-res:active {
            background-color: #004085;
            transform: translateY(1px);
        }

        /* Button container for modals */
        .modal-btn-container {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Add space between the buttons */
        }

        .modal-btn-container button {
            flex: 1; /* Ensures buttons take equal space */
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            transition: all 0.3s ease;
        }

        /* Cancel button styling */
        .cancel-btn {
            background-color: #f44336;
        }

        .cancel-btn:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }

        .cancel-btn:active {
            background-color: #c62828;
            transform: translateY(1px);
        }

        /* Modal and table button styling */
        #priceModal button,
        #stockModal button,
        #deleteModal button {
            width: 48%;
            padding: 12px 0;
        }

        button:focus {
            outline: none;
        }

        /* Modal Styling */
        #priceModal, #stockModal, #deleteModal {
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

        #priceModal h3, #stockModal h3, #deleteModal h3 {
            margin-top: 0;
            text-align: center;
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

        .save-btn {
            background-color: #4CAF50;
        }

        .save-btn:hover {
            background-color: #008a00;
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

        /* Search Bar Styling */
        .search-container {
            margin-top: 80px;
            text-align: center;
        }

        #searchInput {
            padding: 10px;
            font-size: 16px;
            width: 50%;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            outline: none;
            border-color: #4CAF50;
        }

    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<!-- Search Bar -->
<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search for products..." onkeyup="searchProducts()">
</div>

<div class="list-container">
    <h2>Product List</h2>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Variation Value</th>
                <th>Stock per Variation</th>
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
                    <td><?= htmlspecialchars($product['product_desc']) ?></td>
                    <td><?= htmlspecialchars($product['variation_value']) ?></td>
                    <td><?= htmlspecialchars($product['stock_per_variation']) ?></td>
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
                    <td><?= $product['on_sale'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <button class="btn-mod" onclick="showPriceForm(<?= $product['product_id'] ?>, <?= $product['price'] ?>, <?= $product['on_sale'] ?>)">Modify Price</button>
                        <button class="btn-res" onclick="showStockForm(<?= $product['product_id'] ?>, <?= $product['stock_level'] ?>)">Re-stock</button>
                        <button class="btn-del" onclick="showDeleteConfirmation(<?= $product['product_id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Price Modal -->
<div id="priceModal">
    <h3>Modify Price</h3>
    <form method="POST">
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
            <button type="submit" name="update_price" class="save-btn">Save Changes</button>
            <button type="button" class="btn cancel-btn" onclick="closePriceForm()">Cancel</button>
        </div>
    </form>
</div>

<!-- Stock Modal -->
<div id="stockModal">
    <h3>Modify Stock Level</h3>
    <form method="POST">
        <input type="hidden" name="product_id" id="stock_product_id">
        <div>
            <label for="new_stock">New Stock Level:</label>
            <input type="number" name="new_stock" id="new_stock" required min="0">
        </div>
        <div class="modal-btn-container">
            <button type="submit" name="update_stock" class="btn">Save Changes</button>
            <button type="button" class="btn cancel-btn" onclick="closeStockForm()">Cancel</button>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal">
    <h3>Are you sure you want to delete this product?</h3>
    <form method="POST">
        <input type="hidden" name="product_id" id="delete_product_id">
        <div class="modal-btn-container">
            <button type="submit" name="delete_product" class="btn">Yes</button>
            <button type="button" class="btn cancel-btn" onclick="closeDeleteConfirmation()">No</button>
        </div>
    </form>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" class="modal-overlay" onclick="closePriceForm(); closeStockForm(); closeDeleteConfirmation()"></div>

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

    function showStockForm(productId, currentStock) {
        document.getElementById('stock_product_id').value = productId;
        document.getElementById('new_stock').value = currentStock;
        document.getElementById('stockModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeStockForm() {
        document.getElementById('stockModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function showDeleteConfirmation(productId) {
        document.getElementById('delete_product_id').value = productId;
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeDeleteConfirmation() {
        document.getElementById('deleteModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function searchProducts() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const productName = row.cells[0].textContent.toLowerCase();
            if (productName.indexOf(filter) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
</script>
</body>
</html>
