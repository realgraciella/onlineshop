<?php
session_start();

// Ensure the agent is logged in and their username is available in the session
if (!isset($_SESSION['username'])) {
    header('Location: login.php');  // Redirect to login page if not logged in
    exit;
}

$username = $_SESSION['username'];  // Get the logged-in agent's username

// Check if there are products in the cart
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    // Fetch the products from the database based on the product IDs in the cart
    $cartProductIds = $_SESSION['cart'];
    $productIds = implode(',', $cartProductIds);  // Convert the array to a comma-separated string

    // Connect to the database
    $connection = new mysqli('localhost', 'root', '', 'dmshop');
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Fetch product details from the database
    $query = "SELECT * FROM products WHERE product_id IN ($productIds)";
    $result = $connection->query($query);

    $cartItems = [];  // Initialize the $cartItems array

    if ($result->num_rows > 0) {
        // Loop through the result and add each product to the $cartItems array
        while ($product = $result->fetch_assoc()) {
            $cartItems[] = $product;  // Add product to cartItems array
        }
    } else {
        $cartItems = null;  // Set to null if no products were found
    }

    $connection->close();
} else {
    $cartItems = null;  // Set to null if no products in the session cart
}

// Calculate the total amount
$totalAmount = 0;
if ($cartItems) {
    foreach ($cartItems as $item) {
        $totalAmount += $item['price'];  // Add price of each product to the total amount
    }
}

// Proceed with the checkout only if there are items in the cart and the form is submitted
if ($cartItems && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect agent's details from the form
    $name = $_POST['name'];
    $paymentMethod = $_POST['payment_method'];

    // Define order status and payment status
    $orderStatus = 'Pending';  // Initial order status
    $paymentStatus = 'Unpaid';  // Initial payment status

    // Insert the order into the 'order' table
    $connection = new mysqli('localhost', 'root', '', 'dmshop');
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Start a transaction
    $connection->begin_transaction();

    try {
        // Insert order details into the 'order' table
        $orderQuery = "INSERT INTO `orders` (username, name, total_amount, order_date, order_status, payment_method, payment_status) 
        VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $connection->prepare($orderQuery);
        $stmt->bind_param('ssdsds', $username, $name, $totalAmount, $orderStatus, $paymentMethod, $paymentStatus);


        // Execute and check if successful
        if ($stmt->execute()) {
            $orderId = $stmt->insert_id;  // Get the last inserted order ID

            // Commit the transaction
            $connection->commit();

            // Clear the cart session
            unset($_SESSION['cart']);

            // Redirect to order confirmation page
            header('Location: order_confirmation.php?order_id=' . $orderId);
            exit; // Ensure script stops executing here after redirect
        } else {
            // If the order insert fails, rollback transaction
            $connection->rollback();
            echo "Error: " . $stmt->error;
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $connection->rollback();
        echo "Error: " . $e->getMessage();
    }

    $connection->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .checkout-container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        .checkout-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .checkout-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group button {
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #218838;
        }

        .cart-summary {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-summary h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .cart-summary ul {
            list-style-type: none;
            padding: 0;
        }

        .cart-summary li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .total-amount {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }

        .gcash-fields {
            display: none;  /* Hide Gcash fields initially */
        }
    </style>
</head>
<body>
<?php include 'agent_header.php'; ?>
    <header>
        <h1>Checkout Page</h1>
    </header>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Complete Your Order</h2>

            <?php if ($cartItems) : ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Agent Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <select id="payment_method" name="payment_method" required onchange="toggleGcashFields(this.value)">
                            <option value="--">Select payment method</option>
                            <option value="Cash">Cash</option>
                            <option value="Gcash">Gcash</option>
                        </select>
                    </div>

                    <div class="gcash-fields">
                        <div class="form-group">
                            <label for="gcash_number">Gcash Number:</label>
                            <input type="text" id="gcash_number" name="gcash_number" placeholder="Enter Gcash Number">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit">Place Order</button>
                    </div>
                </form>

                <div class="cart-summary">
                    <h3>Your Cart Summary</h3>
                    <ul>
                        <?php foreach ($cartItems as $item) : ?>
                            <li><?= $item['product_name'] ?> - ₱<?= number_format($item['price'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="total-amount">Total: ₱<?= number_format($totalAmount, 2) ?></div>
                </div>
            <?php else : ?>
                <p>Your cart is empty. Please add items before placing an order.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleGcashFields(paymentMethod) {
            var gcashFields = document.querySelector('.gcash-fields');
            if (paymentMethod === 'Gcash') {
                gcashFields.style.display = 'block';
            } else {
                gcashFields.style.display = 'none';
            }
        }
    </script>

</body>
</html>
