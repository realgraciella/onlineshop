<?php
session_start();  // Start the session

// Include the PDO connection file
include 'database/db_connect.php';

// Assuming you have the username stored in the session
$username = $_SESSION['username'];
$orderDate = date('Y-m-d H:i:s');
$totalAmount = 0;
$items = [];

try {
    // Fetch cart items based on the username
    $cartQuery = "SELECT * FROM cart WHERE username = :username";
    $stmt = $pdo->prepare($cartQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $row;  // Add each cart item to the items array
        $totalAmount += floatval($row['price_per_variation']) * intval($row['quantity']);
    }
    
    // Insert order into the orders table
    $orderQuery = "INSERT INTO orders (username, name, items, total_amount, order_date, order_status, payment_method, payment_status) 
                    VALUES (:username, :name, :items, :total_amount, :order_date, :order_status, :payment_method, :payment_status)";
    $orderStatus = 'Pending';
    $paymentMethod = 'Credit Card'; // Example payment method
    $paymentStatus = 'Unpaid'; // Example payment status
    $orderItems = json_encode($items); // Convert items array to JSON

    $stmt = $pdo->prepare($orderQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':name', $username, PDO::PARAM_STR);
    $stmt->bindParam(':items', $orderItems, PDO::PARAM_STR);
    $stmt->bindParam(':total_amount', $totalAmount, PDO::PARAM_STR);
    $stmt->bindParam(':order_date', $orderDate, PDO::PARAM_STR);
    $stmt->bindParam(':order_status', $orderStatus, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
    $stmt->bindParam(':payment_status', $paymentStatus, PDO::PARAM_STR);
    $stmt->execute();
    $orderId = $pdo->lastInsertId(); // Get the last inserted order ID
    
    // Clear the cart after checkout
    $clearCartQuery = "DELETE FROM cart WHERE username = :username";
    $stmt = $pdo->prepare($clearCartQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Return the order ID and total amount as JSON
    echo json_encode(['orderId' => $orderId, 'totalAmount' => $totalAmount, 'items' => $items]);

} catch (PDOException $e) {
    echo json_encode(['error' => "Database error: " . $e->getMessage()]);
    exit;
}