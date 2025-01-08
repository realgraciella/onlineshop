<?php
session_start();
$connection = new mysqli('localhost', 'root', '', 'dmshop1');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
        exit;
    }

    $username = $_SESSION['username'];
    $product_id = $_POST['product_id'];
    $variation_id = $_POST['variation_id'];
    $variation_value = $_POST['variation_value'];
    $quantity = $_POST['quantity'];
    $price_per_variation = $_POST['price_per_variation'];

    // Prepare and bind the statement
    $stmt = $connection->prepare("INSERT INTO cart (username, product_id, variation_id, variation_value, quantity, price_per_variation, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("siisid", $username, $product_id, $variation_id, $variation_value, $quantity, $price_per_variation);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart.']);
    }

    // Close the prepared statement
    $stmt->close();
}

// Check if the product already exists in the cart
$checkStmt = $connection->prepare("SELECT quantity FROM cart WHERE username = ? AND product_id = ? AND variation_id = ?");
$checkStmt->bind_param("sii", $username, $product_id, $variation_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Product exists, update the quantity
    $checkStmt->bind_result($existingQuantity);
    $checkStmt->fetch();
    $newQuantity = $existingQuantity + $quantity;

    $updateStmt = $connection->prepare("UPDATE cart SET quantity = ? WHERE username = ? AND product_id = ? AND variation_id = ?");
    $updateStmt->bind_param("isii", $newQuantity, $username, $product_id, $variation_id);
    $updateStmt->execute();
    $updateStmt->close();

    echo json_encode(['success' => true, 'message' => 'Product quantity updated in cart!']);
} else {
    // Product does not exist, insert a new entry
    $stmt = $connection->prepare("INSERT INTO cart (username, product_id, variation_id, variation_value, quantity, price_per_variation, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("siisid", $username, $product_id, $variation_id, $variation_value, $quantity, $price_per_variation);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart.']);
    }
    $stmt->close();
}

$checkStmt->close();

$connection->close();
?>