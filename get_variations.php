<?php
include 'database/db_connect.php';

$product_name = $_GET['product_name'];
$query = "SELECT variation_id, variation_value, price_per_variation AS price FROM product_variations WHERE product_id = (SELECT product_id FROM products WHERE product_name = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$product_name]);
$variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($variations);
?>

