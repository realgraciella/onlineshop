<!DOCTYPE html>
<html lang="en">
<head>
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
        }

        .status-box {
            width: 50%;
            max-width: 600px;
            padding: 20px;
            border: 2px solidrgb(0, 0, 0);
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .status-box h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #343a40;
        }

        .status {
            margin: 20px;
            font-size: 1.5em;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'customer_header.php'; ?>
    <div class="status-box">
        <h1>Order Status</h1>
        <p class="status" id="orderStatus">Order Not Placed</p>
    </div>

    <script>
        // Placeholder function to simulate order status retrieval
        function getOrderStatus() {
            // Replace this logic with real backend status retrieval if needed
            const orderPlaced = false; // Change to true to simulate an order placed
            return orderPlaced ? "Order Placed" : "Order Not Placed";
        }

        // Update the status on page load
        document.getElementById("orderStatus").textContent = getOrderStatus();
    </script>
</body>
</html>