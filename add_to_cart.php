<?php
session_start();
include 'database/db_connect.php'; // Include the PDO connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
        exit;
    }

// Continue with your existing logic to add to the cart...
    $username = $_SESSION['username'];
    $product_id = $_POST['product_id'];
    $variation_id = $_POST['variation_id'];
    $variation_value = $_POST['variation_value'];
    $quantity = $_POST['quantity'];
    $price_per_variation = $_POST['price_per_variation'];

    try {
        // Check if the product already exists in the cart
        $checkStmt = $pdo->prepare("SELECT quantity FROM cart WHERE username = :username AND product_id = :product_id AND variation_id = :variation_id");
        $checkStmt->execute([':username' => $username, ':product_id' => $product_id, ':variation_id' => $variation_id]);

        if ($checkStmt->rowCount() > 0) {
            // Product exists, update the quantity
            $existingQuantity = $checkStmt->fetchColumn();
            $newQuantity = $existingQuantity + $quantity;

            $updateStmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE username = :username AND product_id = :product_id AND variation_id = :variation_id");
            $updateStmt->execute([':quantity' => $newQuantity, ':username' => $username, ':product_id' => $product_id, ':variation_id' => $variation_id]);

            echo json_encode(['success' => true, 'message' => 'Product quantity updated in cart!']);
        } else {
            // Product does not exist, insert a new entry
            $stmt = $pdo->prepare("INSERT INTO cart (username, product_id, variation_id, variation_value, quantity, price_per_variation, created_at) VALUES (:username, :product_id, :variation_id, :variation_value, :quantity, :price_per_variation, NOW())");
            $stmt->execute([':username' => $username, ':product_id' => $product_id, ':variation_id' => $variation_id, ':variation_value' => $variation_value, ':quantity' => $quantity, ':price_per_variation' => $price_per_variation]);

            echo json_encode(['success' => true, 'message' => 'Product added to cart successfully!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart.']);
    }
}
?>