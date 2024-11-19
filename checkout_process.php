<?php
include 'database/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true); // Get JSON data from the request

if ($data) {
    $pdo->beginTransaction();

    try {
        foreach ($data as $orderItem) {
            $product_id = $orderItem['product_id'];
            $quantity = $orderItem['quantity'];
            $total_amount = $orderItem['total_amount'];

            // Fetch product details
            $product_query = "SELECT stock_level, product_name, price FROM products WHERE product_id = ?";
            $product_stmt = $pdo->prepare($product_query);
            $product_stmt->execute([$product_id]);
            $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['stock_level'] >= $quantity) {
                // Update stock level
                $update_stock_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?";
                $update_stock_stmt = $pdo->prepare($update_stock_query);
                $update_stock_stmt->execute([$quantity, $product_id]);

                // Insert sale record
                $sale_query = "INSERT INTO store_sales (product_id, product_name, product_price, quantity, total_amount) 
                               VALUES (?, ?, ?, ?, ?)";
                $sale_stmt = $pdo->prepare($sale_query);
                $sale_stmt->execute([$product_id, $product['product_name'], $product['price'], $quantity, $total_amount]);
            } else {
                throw new Exception('Insufficient stock for product: ' . $product['product_name']);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
