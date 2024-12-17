<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
    exit;
}

$connection = new mysqli('localhost', 'root', '', 'dmshop1');

if ($connection->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$username = $_POST['username'];
$product_id = $_POST['product_id'];
$variation_id = $_POST['variation_id'];
$quantity = $_POST['quantity'];

// Fetch the price_per_variation for the selected variation
$variationQuery = "SELECT price_per_variation FROM product_variations WHERE variation_id = ?";
$stmt = $connection->prepare($variationQuery);
$stmt->bind_param("i", $variation_id);
$stmt->execute();
$stmt->bind_result($price_per_variation);
$stmt->fetch();
$stmt->close();

if ($price_per_variation) {
    // Insert into cart table
    $insertQuery = "INSERT INTO cart (username, product_id, variation_id, quantity, price_per_variation, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("siids", $username, $product_id, $variation_id, $quantity, $price_per_variation);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to cart.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Variation not found.']);
}

$connection->close();
?>