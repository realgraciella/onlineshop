<?php
include 'database/db_connect.php';

$variation_id = $_GET['variation_id'];
$quantity = $_GET['quantity'];

// Update stock level in product_variations table
$query = "UPDATE product_variations SET stock_level = stock_level - ? WHERE variation_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$quantity, $variation_id]);

echo json_encode(['success' => true]);
?>
