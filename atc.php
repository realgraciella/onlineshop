<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

include 'database/db_connect.php'; // Include the PDO connection

$username = $_POST['username'];
$product_id = $_POST['product_id'];
$variation_id = $_POST['variation_id'];
$quantity = $_POST['quantity'];

// Fetch the price_per_variation for the selected variation
$variationQuery = "SELECT price_per_variation FROM product_variations WHERE variation_id = :variation_id";
$stmt = $pdo->prepare($variationQuery);
$stmt->bindParam(':variation_id', $variation_id, PDO::PARAM_INT);
$stmt->execute();

$price_per_variation = $stmt->fetchColumn();

if ($price_per_variation) {
    // Insert into cart table
    $insertQuery = "INSERT INTO cart (username, product_id, variation_id, quantity, price_per_variation, created_at) 
                    VALUES (:username, :product_id, :variation_id, :quantity, :price_per_variation, NOW())";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':variation_id', $variation_id, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':price_per_variation', $price_per_variation, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Variation not found.']);
}
?>
