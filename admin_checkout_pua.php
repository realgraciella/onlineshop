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
        // Get the agent ID from the input
        $agent_id = $input['agent_id'] ?? null;

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
                // Calculate total amount
                $total_amount = $item['price'] * $item['quantity'];

                // Insert into product_under_agents table
                $pua_query = "INSERT INTO product_under_agents (agent_id, username, product_id, product_value, price_per_variation, quantity, total_amount, sale_date, due_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))";
                $pua_stmt = $pdo->prepare($pua_query);
                if (!$pua_stmt->execute([$agent_id, $username, $product['product_id'], $variation['variation_value'], $item['price'], $item['quantity'], $total_amount])) {
                    $errorInfo = $pua_stmt->errorInfo();
                    die(json_encode(['success' => false, 'message' => "Error inserting into product_under_agents table: " . $errorInfo[2]]));
                }

                // Update stock levels after successful sale
                $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?";
                $update_product_stmt = $pdo->prepare($update_product_query);
                $update_product_stmt->execute([$item['quantity'], $product['product_id']]);

                // Update stock level for the specific variation
                $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE variation_id = ?";
                $update_variation_stmt = $pdo->prepare($update_variation_query);
                $update_variation_stmt->execute([$item['quantity'], $item['variation_id']]);
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