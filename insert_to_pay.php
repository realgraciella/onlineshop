<?php
// Include the database connection
include 'database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $productValue = $_POST['product_value'];
    $pricePerVariation = $_POST['price_per_variation'];
    $quantity = $_POST['quantity'];
    $dueDate = $_POST['due_date'];

    $insertSql = "INSERT INTO to_pay_products (username, product_id, variation_value, price_per_variation, quantity, total_amount, due_date, payment_status, created_at, updated_at) 
                  VALUES (:username, :product_id, :variation_value, :price_per_variation, :quantity, :total_amount, :due_date, 'pending', NOW(), NOW())";

    $insertStmt = $pdo->prepare($insertSql);

    $insertStmt->execute([
        ':username' => 'admin', // Replace with the actual username
        ':product_id' => $productId,
        ':variation_value' => $productValue,
        ':price_per_variation' => $pricePerVariation,
        ':quantity' => $quantity,
        ':total_amount' => $pricePerVariation * $quantity,
        ':due_date' => $dueDate
    ]);

    if ($insertStmt->rowCount() > 0) {
        echo 'success';
    } else {
        echo 'error';
    }
}

// Close the PDO connection
$pdo = null;
?>