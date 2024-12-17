<?php
session_start();
include 'database/db_connect.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die(json_encode(['success' => false, 'message' => 'Invalid JSON input.']));
    }

    // Check if the user is logged in
    $username = $_SESSION['username'] ?? null;
    if (!$username) {
        die(json_encode(['success' => false, 'message' => 'User  not logged in.']));
    }

    // Check if checkoutList is set and is an array
    if (isset($input['checkoutList']) && is_array($input['checkoutList'])) {
        foreach ($input['checkoutList'] as $item) {
            // Validate item data
            if (empty($item['product_name']) || empty($item['quantity']) || empty($item['variation_id'])) {
                die(json_encode(['success' => false, 'message' => 'Invalid item data.']));
            }

            // Fetch product details
            $product_query = "SELECT * FROM products WHERE product_name = ?";
            $product_stmt = $pdo->prepare($product_query);
            $product_stmt->execute([$item['product_name']]);
            $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch variation details
            $variation_query = "SELECT * FROM product_variations WHERE variation_id = ?";
            $variation_stmt = $pdo->prepare($variation_query);
            $variation_stmt->execute([$item['variation_id']]);
            $variation = $variation_stmt->fetch(PDO::FETCH_ASSOC);

            // Check if product and variation exist and if there's enough stock
            if ($product && $variation && $variation['stock_per_variation'] >= $item['quantity']) {
                // Insert into pos table with status "On Process"
                $pos_query = "INSERT INTO pos (username, product_id, product_name, variation_id, quantity, price, total_amount, sale_date, pos_status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'On Process')";
                $pos_stmt = $pdo->prepare($pos_query);
                if (!$pos_stmt->execute([$username, $product['product_id'], $item['product_name'], $item['variation_id'], $item['quantity'], $item['price'], $item['total_amount']])) {
                    $errorInfo = $pos_stmt->errorInfo();
                    die(json_encode(['success' => false, 'message' => "Error inserting into pos table: " . $errorInfo[2]]));
                }

                // Note: Do not update stock levels here, as the sale is not yet confirmed
            } else {
                echo json_encode(['success' => false, 'message' => "Insufficient stock for {$item['product_name']} or invalid product."]);
                exit;
            }
        }

        // If everything is successful
        echo json_encode(['success' => true, 'message' => 'Checkout completed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid checkout data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>