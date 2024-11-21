<?php
session_start();

// Check if the order ID is provided in the URL
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Connect to the database
    $connection = new mysqli('localhost', 'root', '', 'dmshop');
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Fetch order details using the order ID
    $query = "SELECT * FROM `orders` WHERE order_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "Order not found.";
        exit;
    }

    $connection->close();
} else {
    echo "No order ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/agent.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .confirmation-container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding-top: 40px;
        }
        .confirmation-header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
        }
        .order-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .order-details h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-details p {
            font-size: 16px;
            line-height: 1.5;
        }
        .order-details .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .order-details .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <div class="confirmation-header">
        <h1>Order Confirmation</h1>
    </div>

    <div class="order-details">
        <h2>Order #<?php echo $order['order_id']; ?> Details</h2>
        <p><strong>Agent Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
        <p><strong>Total Amount:</strong> PHP <?php echo number_format($order['total_amount'], 2); ?></p>
        <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?></p>

        <!-- Optional: Provide a button to return to the home page or cart -->
        <a href="agent.php" class="btn">Return to Home</a>
    </div>
</div>

</body>
</html>
