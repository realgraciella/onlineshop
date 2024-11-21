<?php
session_start();

if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    // Remove the product from the cart
    if (($key = array_search($productId, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        unset($_SESSION['cart_quantity'][$productId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
