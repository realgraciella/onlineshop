<?php
include 'database/db_connect.php';

$product_id = $_GET['product_id'];
$quantity = $_GET['quantity'];

// Update stock level in products table
$query = "UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$quantity, $product_id]);

echo json_encode(['success' => true]);
?>
