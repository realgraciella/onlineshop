<?php
include('database/db_connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div style='text-align: center; margin-top: 20px;'>
            <p>Please log in to view your cart.</p>
            <a href='login.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;'>Login</a>
          </div>";
    return;
}

// Check if the cart session exists and contains items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    return;
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

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

    <style>
        /* Styling inspired by your reference */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .cart-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            max-width: 700px;
            margin: 0 auto;
        }
        .cart-header {
            font-weight: bold;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #eee;
            padding: 15px 0;
        }
        .cart-item:first-child {
            border-top: none;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 15px;
        }
        .cart-item-details {
            flex-grow: 1;
            margin-left: 15px;
        }
        .cart-item-details h4 {
            margin: 0;
            font-size: 1em;
            color: #333;
        }
        .cart-item-details p {
            margin: 5px 0;
            color: #777;
            font-size: 0.9em;
        }
        .cart-item-price,
        .cart-item-quantity {
            min-width: 80px;
            text-align: right;
            font-size: 0.95em;
            color: #444;
        }
        .cart-total {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 20px;
            text-align: right;
        }
        .empty-cart {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php
include 'agent_header.php'; 
?>

    <div class="cart-container">
        <div class="cart-header">My Cart</div>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <!-- Restrict items by user ID (Assumes each cart item includes a 'user_id' field) -->
            <?php if ($item['user_id'] === $_SESSION['user_id']): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'default_image.jpg'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                    </div>
                    <div class="cart-item-details">
                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                        <p><?php echo htmlspecialchars($item['description'] ?? 'No description available.'); ?></p>
                    </div>
                    <div class="cart-item-quantity">
                        <?php echo htmlspecialchars($item['quantity']); ?> pcs
                    </div>
                    <div class="cart-item-price">
                        ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
                <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="cart-total">
            Total: ₱<?php echo number_format($total, 2); ?>
        </div>
    </div>

</body>
</html>
