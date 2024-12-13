<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmshop"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the order ID from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch the order details
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Display the receipt
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt for Order #<?php echo htmlspecialchars($order['order_id']); ?></title>
    <link href="assets/css/receipt.css" rel="stylesheet"> <!-- Include your CSS for styling -->
</head>
<body>
    <h1>Receipt</h1>
    <p>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></p>
    <p>Username: <?php echo htmlspecialchars($order['username']); ?></p>
    <p>Name: <?php echo htmlspecialchars($order['name']); ?></p>
    <p>Items: <?php echo htmlspecialchars($order['items']); ?></p>
    <p>Total Amount: <?php echo number_format($order['total_amount'], 2); ?></p>
    <p>Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
    <p>Payment Method: <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p>Payment Status: <?php echo htmlspecialchars($order['payment_status']); ?></p>
    <button onclick="window.print()">Print this receipt</button>
</body>
</html>