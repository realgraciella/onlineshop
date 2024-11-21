<?php
session_start();
$total = 0;

// Check if the cart and quantity are set
if (isset($_SESSION['cart']) && isset($_SESSION['cart_quantity'])) {
    $cartProductIds = $_SESSION['cart'];
    $productIds = implode(',', $cartProductIds);  // Convert the array to a comma-separated string

    // Connect to the database
    $connection = new mysqli('localhost', 'root', '', 'dmshop');
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Fetch product details from the database
    $query = "SELECT * FROM products WHERE product_id IN ($productIds)";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        while ($item = $result->fetch_assoc()) {
            // Get the quantity from the session
            $quantity = isset($_SESSION['cart_quantity'][$item['product_id']]) ? $_SESSION['cart_quantity'][$item['product_id']] : 1;
            $total += $item['price'] * $quantity;
        }
    }

    $connection->close();
}

// Return the total in JSON format
echo json_encode(['success' => true, 'newTotal' => number_format($total, 2)]);
?>
