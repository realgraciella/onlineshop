<?php
session_start();  // Start the session

// Connect to the database
$connection = new mysqli('localhost', 'root', '', 'dmshop1');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Assuming you have the username stored in the session
$username = $_SESSION['username'];
$orderDate = date('Y-m-d H:i:s');
$totalAmount = 0;
$items = [];

// Fetch cart items based on the username
$cartQuery = "SELECT * FROM cart WHERE username = ?";
$stmt = $connection->prepare($cartQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $items[] = $row;  // Add each cart item to the items array
    $totalAmount += floatval($row['price_per_variation']) * intval($row['quantity']);
}

// Insert order into the orders table
$orderQuery = "INSERT INTO orders (username, name, items, total_amount, order_date, order_status, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$orderStatus = 'Pending';
$paymentMethod = 'Credit Card'; // Example payment method
$paymentStatus = 'Unpaid'; // Example payment status
$orderItems = json_encode($items); // Convert items array to JSON

$stmt = $connection->prepare($orderQuery);
$stmt->bind_param("sssdssss", $username, $username, $orderItems, $totalAmount, $orderDate, $orderStatus, $paymentMethod, $paymentStatus);
$stmt->execute();
$orderId = $stmt->insert_id; // Get the last inserted order ID
$stmt->close();

// Clear the cart after checkout
$clearCartQuery = "DELETE FROM cart WHERE username = ?";
$stmt = $connection->prepare($clearCartQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->close();

$connection->close();

// Return the order ID and total amount as JSON
echo json_encode(['orderId' => $orderId, 'totalAmount' => $totalAmount, 'items' => $items]);
?>