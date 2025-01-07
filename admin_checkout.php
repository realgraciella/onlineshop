<?php
session_start();
include 'database/db_connect.php'; 

// Initialize variables
$checkoutData = [];
$successMessage = '';
$errorMessage = '';
$abortMessage = ''; // Variable to hold abort message

// Fetch sales agents from the users table
$agents_query = "SELECT user_id, username FROM users WHERE role = 'Sales Agent'";
$agents_stmt = $pdo->prepare($agents_query);
$agents_stmt->execute();
$salesAgents = $agents_stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $agent_id = $_POST['sales_agent'] ?? null; // Get selected agent ID
    $customer_name = $_POST['customer_name'] ?? null; // Get customer name

    if (isset($_POST['abort_checkout'])) {
        // Clear the sales data for the logged-in user
        if ($username) {
            $clear_sales_query = "DELETE FROM pos WHERE username = ?";
            $clear_sales_stmt = $pdo->prepare($clear_sales_query);
            $clear_sales_stmt->execute([$username]);
            $abortMessage = "Checkout cancelled and sales data cleared."; // Set abort message
        } else {
            $errorMessage = "User not logged in.";
        }
    } else {
        $name = $agent_id ? $salesAgents[array_search($agent_id, array_column($salesAgents, 'user_id'))]['username'] : $customer_name;

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
                    $sale_query = "INSERT INTO store_sales (product_id, name, product_name, product_value, price_per_variation, quantity, total_amount, sale_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $sale_stmt = $pdo->prepare($sale_query);
                    if (!$sale_stmt->execute([$product['product_id'], $name, $product['product_name'], $variation['variation_value'], $product_price, $quantity, $total_amount, $sale_date])) {
                        $errorInfo = $sale_stmt->errorInfo();
                        $errorMessage = "Error inserting sale: " . $errorInfo[2];
                    }

                    // Insert into sales table
                    $sales_insert_query = "INSERT INTO sales (agent_id, product_id, username, product_name, product_value, price_per_variation, quantity, sale_amount, sale_date, created_at) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $sales_insert_stmt = $pdo->prepare($sales_insert_query);
                    $created_at = date('Y-m-d H:i:s');
                    $username_display = $username ?? 'Customer'; // Use 'Customer' if no agent is selected
                    if (!$sales_insert_stmt->execute([$agent_id, $product['product_id'], $username_display, $product['product_name'], $variation['variation_value'], $product_price, $quantity, $total_amount, $sale_date, $created_at])) {
                        $errorInfo = $sales_insert_stmt->errorInfo();
                        $errorMessage = "Error inserting sale into sales table: " . $errorInfo[2];
                    }

                    // Update product stock level in the products table
                    $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_name = ?";
                    $update_product_stmt = $pdo->prepare($update_product_query);
                    $update_product_stmt->execute([$quantity, $product_name]);

                    // Update stock level for the specific variation
                    $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE variation_id = ?";
                    $update_variation_stmt = $pdo->prepare($update_variation_query);
                    $update_variation_stmt->execute([$quantity, $variation_id]);

                    $successMessage = "Sale recorded successfully for $product_name. Total amount: PHP " . number_format($total_amount, 2);
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

    // Check if a product needs to be removed
    if (isset($_POST['remove_product'])) {
        $product_name_to_remove = $_POST['remove_product'];

        // Remove the product from the pos table
        if ($username) {
            $remove_query = "DELETE FROM pos WHERE username = ? AND product_name = ?";
            $remove_stmt = $pdo->prepare($remove_query);
            $remove_stmt->execute([$username, $product_name_to_remove]);
            $successMessage = "Product '$product_name_to_remove' removed successfully.";
        } else {
            $errorMessage = "User  not logged in.";
        }
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
        .btn-danger {
            background-color: #dc3545;
            border-color: #c82333;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
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
        .customer-input {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="checkout-container">
        <h2 class="text-center">Checkout</h2>

        <form method="POST" action="admin_checkout.php" class="mb-4">
            <div class="mb-3">
                <label for="customer_type" class="form-label">Select Customer Type</label>
                <div>
                    <button type="button" id="salesAgentButton" class="btn btn-secondary">Sales Agent</button>
                    <button type="button" id="customerButton" class="btn btn-secondary">One-Time Customer</button>
                </div>
            </div>
            <div class="mb-3" id="salesAgentDropdown" style="display: none;">
                <label for="sales_agent" class="form-label">Select Sales Agent</label>
                <select name="sales_agent" id="sales_agent" class="form-select">
                    <option value="">--</option>
                    <?php foreach ($salesAgents as $agent): ?>
                        <option value="<?= htmlspecialchars($agent['user_id']) ?>"><?= htmlspecialchars($agent['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 customer-input" id="customerInput" style="display: none;">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter customer name">
            </div>
        </form>

        <!-- Success and Error Messages -->
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

        <?php if ($abortMessage): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($abortMessage) ?>
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
                        <td>
                            <form method="POST" action="admin_checkout.php" class="d-inline">
                                <input type="hidden" name="remove_product" value="<?= htmlspecialchars($item['product_name']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
            <strong>Total Amount:</strong> PHP <?= number_format(array_sum(array_column($salesData, 'total_amount')), 2) ?>
        </div>

        <form method="POST" action="print_receipt.php" class="mt-4">
            <input type="hidden" name="checkout_data" value='<?= htmlspecialchars(json_encode($checkoutData)) ?>'>
            <button type="submit" class="btn btn-primary w-100">Complete Purchase</button>
        </form>

        <form method="POST" action="admin_checkout.php" class="mt-4">
            <input type="hidden" name="abort_checkout" value="1">
            <button type="submit" class="btn btn-danger w-100">Cancel Checkout</button>
        </form>
    </div>

    <div class="modal fade" id="abortModal" tabindex="-1" aria-labelledby="abortModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="abortModalLabel">Checkout Cancelled</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    The checkout process has been successfully cancelled, and all sales data has been cleared.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initially hide the dropdown and customer input
            $('#salesAgentDropdown').hide();
            $('#customerInput').hide();

            $('#salesAgentButton').click(function() {
                $('#salesAgentDropdown').show();
                $('#customerInput').hide();
            });

            $('#customerButton').click(function() {
                $('#salesAgentDropdown').hide();
                $('#customerInput').show();
            });

            <?php if ($abortMessage): ?>
                $('#abortModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>
