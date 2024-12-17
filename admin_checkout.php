<?php
session_start();
include 'database/db_connect.php'; 

// Initialize variables
$checkoutData = [];
$successMessage = '';
$errorMessage = '';

// Fetch all products with their first variation price and category name
$query = "SELECT p.*, pv.price_per_variation AS variation_price, c.category_name 
          FROM products p 
          LEFT JOIN product_variations pv ON p.product_id = pv.product_id 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          GROUP BY p.product_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if data is received from the previous page
if (isset($_GET['data'])) {
    $checkoutData = json_decode($_GET['data'], true);
} else {
    $checkoutData = []; // Initialize as an empty array if no data is received
}

// Fetch sales data from the pos table based on the logged-in user, excluding completed sales
$username = $_SESSION['username'] ?? null;
if ($username) {
    $pos_query = "SELECT product_name, variation_value, quantity, price, total_amount 
                  FROM pos 
                  WHERE username = ? AND pos_status != 'Completed'";
    $pos_stmt = $pdo->prepare($pos_query);
    $pos_stmt->execute([$username]);
    $salesData = $pos_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $salesData = []; // No sales data if user is not logged in
}

// Handle the sale if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($checkoutData as $item) {
        $product_name = $item['product_name'];
        $quantity = $item['quantity'];
        $variation_id = $item['variation_id'];
        $product_price = $item['price']; 
        $total_amount = $product_price * $quantity; 
        $sale_date = date('Y-m-d H:i:s'); 

        // Fetch product details
        $product_query = "SELECT * FROM products WHERE product_name = ?";
        $product_stmt = $pdo->prepare($product_query);
        $product_stmt->execute([$product_name]);
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch variation details
        $variation_query = "SELECT * FROM product_variations WHERE variation_id = ?";
        $variation_stmt = $pdo->prepare($variation_query);
        $variation_stmt->execute([$variation_id]);
        $variation = $variation_stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $variation) {
            if ($variation['stock_per_variation'] >= $quantity) {
                // Insert sale into store_sales table
                $sale_query = "INSERT INTO store_sales (product_id, product_name, product_value, price_per_variation, quantity, total_amount, sale_date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $sale_stmt = $pdo->prepare($sale_query);
                if (!$sale_stmt->execute([$product['product_id'], $product['product_name'], $variation['variation_value'], $product_price, $quantity, $total_amount, $sale_date])) {
                    $errorInfo = $sale_stmt->errorInfo();
                    $errorMessage = "Error inserting sale: " . $errorInfo[2];
                }

                // Update product stock level in the products table
                // Only update stock levels after the sale is confirmed
                $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_name = ?";
                $update_product_stmt = $pdo->prepare($update_product_query);
                $update_product_stmt->execute([$quantity, $product_name]);

                // Update stock level for the specific variation
                $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE variation_id = ?";
                $update_variation_stmt = $pdo->prepare($update_variation_query);
                $update_variation_stmt->execute([$quantity, $variation_id]);

                $successMessage = "Sale recorded successfully for $product_name. Total amount: PHP $total_amount";
            } else {
                $errorMessage = "Insufficient stock for $product_name.";
            }
        } else {
            $errorMessage = "Invalid product.";
        }
    }

    // Print the bill after successful sale
    if ($successMessage) {
        echo "<script>
            window.print();
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .checkout-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 3rem;
        }
        h2 {
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #008a00;
            padding: 0.6rem 1.2rem;
            color: black;
        }
        .btn-primary:hover {
            background-color: #008a00;
            border-color: rgb(0, 0, 0);
        }
        table {
            margin-top: 2rem;
        }
        table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .alert {
            margin-top: 1rem;
        }
        .text-center {
            margin-bottom: 2rem;
        }
        .text-end {
            font-weight: bold;
            color: black;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="checkout-container">
        <h2 class="text-center">Checkout</h2>

        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th>Product Name</th>
                    <th>Variation</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesData as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['variation_value']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                        <td class="text-center">PHP <?= number_format(htmlspecialchars($item['price']), 2) ?></td>
                        <td class="text-end">PHP <?= number_format($item['total_amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        ```html
        </table>

        <div class="text-end">
            <strong>Total Amount:</strong> PHP <?= number_format(array_sum(array_column($salesData, 'total_amount')), 2) ?>
        </div>

        <form method="POST" action="print_receipt.php" class="mt-4">
            <input type="hidden" name="checkout_data" value='<?= htmlspecialchars(json_encode($checkoutData)) ?>'>
            <button type="submit" class="btn btn-primary w-100">Complete Purchase</button>
        </form>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>