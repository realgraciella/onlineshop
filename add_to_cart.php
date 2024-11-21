<?php
session_start();  // Start the session

// Check if product_id is passed
if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];  // Assuming user ID is stored in the session

    // If the cart does not exist in the session, initialize it as an empty array
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product is already in the session cart
    if (!in_array($productId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $productId;  // Add product to session cart

        // Connect to the database
        $connection = new mysqli('localhost', 'root', '', 'dmshop');
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Get the current date
        $addedDate = date('Y-m-d H:i:s');

        // Insert the product into the user_cart table with default quantity of 1
        $query = "INSERT INTO user_cart (user_id, product_id, quantity, added_date) VALUES (?, ?, 1, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('iis', $userId, $productId, $addedDate);  // Bind parameters
        if ($stmt->execute()) {
            echo "Product added to cart!";
        } else {
            echo "Error adding product to cart: " . $connection->error;
        }

        $stmt->close();
        $connection->close();
    } else {
        echo "Product is already in the cart.";
    }
} else {
    echo "No product ID provided.";
}
?>
