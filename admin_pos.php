<?php
include 'database/db_connect.php'; // Ensure this connects to your database correctly

// Fetch all products
$query = "SELECT * FROM products WHERE stock_level > 0";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories and brands for filters
$categoryQuery = "SELECT * FROM categories";
$categoryStmt = $pdo->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$brandQuery = "SELECT * FROM brands";
$brandStmt = $pdo->prepare($brandQuery);
$brandStmt->execute();
$brands = $brandStmt->fetchAll(PDO::FETCH_ASSOC);

$successMessage = '';
// Handle checkout
if (isset($_POST['checkout'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details
    $product_query = "SELECT * FROM products WHERE product_id = ?";
    $product_stmt = $pdo->prepare($product_query);
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $product['stock_level'] >= $quantity) {
        // Calculate total amount
        $total_amount = $product['price'] * $quantity;

        // Insert sale into store_sales table
        $sale_query = "INSERT INTO store_sales (product_id, product_name, product_price, quantity, total_amount) 
                       VALUES (?, ?, ?, ?, ?)";
        $sale_stmt = $pdo->prepare($sale_query);
        $sale_stmt->execute([$product['product_id'], $product['product_name'], $product['price'], $quantity, $total_amount]);

        // Update product stock level in the products table
        $update_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$quantity, $product_id]);

        $successMessage = "Sale recorded successfully. Total amount: PHP $total_amount";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Insufficient stock or invalid product.'
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert2 CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f5f7;
            padding-top: 40px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 40px;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 22%;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .product-card h4 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .product-card p {
            font-size: 0.9rem;
            color: #555;
        }
        .product-card .price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
        }
        .filter-section {
            padding: 25px;
            margin-bottom: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .filter-section h5 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 10px;
        }
        .filter-section label {
            font-size: 0.9rem;
            color: #555;
        }
        .btn-success {
            background-color: #28a745;
            color: #fff;
            font-weight: 600;
            padding: 10px;
            border-radius: 6px;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .checkout-form {
            margin-top: 15px;
        }
        .checkout-form .form-control {
            font-size: 0.9rem;
        }
        .d-flex > label {
            margin-right: 15px;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 1200px) {
            .product-card {
                width: 30%;
            }
        }

        @media (max-width: 992px) {
            .product-card {
                width: 45%;
            }
        }

        @media (max-width: 768px) {
            .product-card {
                width: 100%;
            }
            .filter-section {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 576px) {
            .filter-section h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="container">
        <h2 class="text-center mt-4 mb-4">POS</h2>

        <!-- Filter Section -->
        <div class="filter-section">
            <h5>Filter by Category</h5>
            <div class="d-flex flex-wrap mb-2">
                <?php foreach ($categories as $category): ?>
                    <label class="me-3">
                        <input type="checkbox" name="category_filter" value="<?= $category['category_id'] ?>"> <?= htmlspecialchars($category['category_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <h5>Filter by Brand</h5>
            <div class="d-flex flex-wrap">
                <?php foreach ($brands as $brand): ?>
                    <label class="me-3">
                        <input type="checkbox" name="brand_filter" value="<?= $brand['brand_id'] ?>"> <?= htmlspecialchars($brand['brand_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Display -->
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php
                    $imagePath = 'uploads/products/' . $product['product_image_url'];
                    if (!file_exists($imagePath) || empty($product['product_image_url'])) {
                        $imagePath = 'assets/img/default-image.png';
                    }
                    ?>
                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <h4><?= htmlspecialchars($product['product_name']) ?></h4>
                    <p class="price">PHP <?= number_format($product['price'], 2) ?></p>
                    <p>Stock Level: <?= $product['stock_level'] ?> available</p>

                    <!-- Checkout Form -->
                    <form method="POST" action="" class="checkout-form">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <div class="mb-3">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" min="1" max="<?= $product['stock_level'] ?>" required class="form-control">
                        </div>
                        <button type="submit" name="checkout" class="btn btn-success w-100">Checkout</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 JS -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
