<?php
session_start();  // Start the session

// Define the credit limit
define('CREDIT_LIMIT', 1500);

// Initialize the cart_quantity array if it's not set
if (!isset($_SESSION['cart_quantity'])) {
    $_SESSION['cart_quantity'] = [];
}

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

// Calculate the total price of the cart
$total = 0;
if ($cartItems) {
    foreach ($cartItems as $item) {
        // Ensure that the quantity is properly initialized
        $quantity = isset($_SESSION['cart_quantity'][$item['product_id']]) ? $_SESSION['cart_quantity'][$item['product_id']] : 1;
        $itemTotal = $item['price'] * $quantity;
        $total += $itemTotal;
    }
}

$creditLimitExceeded = $total > CREDIT_LIMIT; // Check if the total exceeds the credit limit
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
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
        .cart-item img {
            max-width: 100px;
            max-height: 100px;
        }
        .cart-item-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .total-price {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
        .btn {
            border-radius: 5px;
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
                <?php
                foreach ($cartItems as $item) {
                    $quantity = isset($_SESSION['cart_quantity'][$item['product_id']]) ? $_SESSION['cart_quantity'][$item['product_id']] : 1;
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $item['product_image_url']; ?>" alt="Product Image">
                        <div class="cart-item-info">
                            <p><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></p>
                            <p>Price: PHP <?php echo number_format($item['price'], 2); ?></p>
                            <p>Quantity: 
                                <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'decrease')">-</button>
                                <span id="quantity_<?php echo $item['product_id']; ?>"><?php echo $quantity; ?></span>
                                <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'increase')">+</button>
                            </p>
                        </div>
                        <div>
                            <button class="btn btn-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">Remove</button>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="total-price">
                Total: PHP <span id="total_price"><?php echo number_format($total, 2); ?></span>
            </div>
            <?php if ($creditLimitExceeded) : ?>
                <div class="alert alert-warning text-center">
                    Credit limit exceeded! Please remove items until the total price is below PHP 1500.
                </div>
            <?php endif; ?>
            <div class="text-center mt-3">
                <button class="btn btn-success <?php echo $creditLimitExceeded ? 'disabled' : ''; ?>" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        <?php else : ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script>
        // Function to remove product from cart
        function removeFromCart(productId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'remove_from_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Product removed from cart');
                    window.location.reload();  // Reload the page to update the cart
                } else {
                    alert('Error removing product from cart');
                }
            };
            xhr.send('product_id=' + productId);
        }

        // Function to update product quantity
        function updateQuantity(productId, action) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_quantity.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('quantity_' + productId).innerText = response.newQuantity;
                        updateCartTotal();  // Update the cart total after the quantity change
                    } else {
                        alert('Error updating quantity');
                    }
                }
            };
            xhr.send('product_id=' + productId + '&action=' + action);
        }

        // Function to update the cart total (recalculate after quantity change)
        function updateCartTotal() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_cart_total.php', true);  // Make sure this file recalculates the total
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Update the total price on the page
                        document.getElementById('total_price').innerText = 'Total: PHP ' + response.newTotal;
                        checkCreditLimit(response.newTotal);  // Check if the total exceeds the credit limit
                    }
                }
            };
            xhr.send();
        }

        // Function to check credit limit and disable Proceed to Checkout button if exceeded
        function checkCreditLimit(newTotal) {
            const creditLimit = 1500;  // Define credit limit
            const proceedButton = document.querySelector('.btn-success');
            
            if (newTotal > creditLimit) {
                proceedButton.classList.add('disabled');
                alert('Credit limit exceeded! Please remove items until the total price is below PHP 1500.');
            } else {
                proceedButton.classList.remove('disabled');
            }
        }

        // Function to proceed to checkout
        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
