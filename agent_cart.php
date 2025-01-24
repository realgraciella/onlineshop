<?php
session_start();  // Start the session

// Include the PDO connection
include 'database/db_connect.php';

// Assuming you have the username stored in the session when the agent logs in
$username = $_SESSION['username']; // Make sure this is set when the agent logs in

try {
    // Fetch the agent's credit limit
    $creditLimitQuery = "SELECT credit_limit FROM agents WHERE agent_user = :username";
    $stmt = $pdo->prepare($creditLimitQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $creditLimit = $stmt->fetchColumn();

    // Initialize the cart items
    $cartItems = [];

    // Fetch cart items based on the username
    $cartQuery = "SELECT * FROM cart WHERE username = :username";
    $stmt = $pdo->prepare($cartQuery);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the total price of the cart
    $total = 0;
    foreach ($cartItems as $item) {
        $total += floatval($item['price_per_variation']) * intval($item['quantity']);
    }

    $creditLimitExceeded = $total > floatval($creditLimit); // Check if the total exceeds the credit limit
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css"> 
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .cart-container {
            margin-top: 60px;
            padding: 20px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .total-price {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <?php include 'agent_header.php'; ?>

    <div class="cart-container">
        <h1>Your Cart</h1>
        <?php if ($cartItems) : ?>
            <div class="cart-items-list">
                <?php foreach ($cartItems as $item) : ?>
                    <div class="cart-item">
                        <p><?php echo htmlspecialchars($item['product_id']); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="total-price">
                Total: PHP <span id="total_price"><?php echo number_format($total, 2); ?></span>
            </div>
            <?php if ($creditLimitExceeded) : ?>
                <div class="alert alert-warning text-center">
                    Credit limit exceeded! Please remove items until the total price is below PHP <?php echo number_format($creditLimit, 2); ?>.
                </div>
            <?php endif; ?>
            <div class="text-center mt-3">
                <button class="btn btn-success <?php echo $creditLimitExceeded ? 'disabled' : ''; ?>" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        <?php else:?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script>
        async function proceedToCheckout() {
            // Fetch the order details from the server
            const response = await fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.orderId) {
                // Generate PDF receipt using jsPDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                doc.setFontSize(16);
                doc.text('Receipt', 20, 20);
                doc.setFontSize(12);
                doc.text('Order ID: ' + data.orderId, 20, 30);
                doc.text('Username: ' + '<?php echo $username; ?>', 20, 40);
                doc.text('Order Date: ' + new Date().toLocaleString(), 20, 50);
                doc.text('Total Amount: PHP ' + data.totalAmount.toFixed(2), 20, 60);
                doc.text('Items:', 20, 70);

                // Add items to the PDF
                let y = 80; // Starting Y position for items
                data.items.forEach(item => {
                    doc.text('Product ID: ' + item.product_id + ' - Variation: ' + item.variation_value + ' - Quantity: ' + item.quantity, 20, y);
                    y += 10; // Increment Y position for the next item
                });

                // Save the PDF
                doc.save('receipt_' + data.orderId + '.pdf');
            } else {
                alert('Checkout failed. Please try again.');
            }
        }
    </script>
</body>
</html>