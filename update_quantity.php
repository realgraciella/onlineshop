<?php
session_start();

if (isset($_POST['product_id']) && isset($_POST['action'])) {
    $productId = $_POST['product_id'];
    $action = $_POST['action'];
    
    // Ensure that the cart quantity session is initialized
    if (!isset($_SESSION['cart_quantity'])) {
        $_SESSION['cart_quantity'] = [];
    }
    
    // Get the current quantity of the product
    $currentQuantity = isset($_SESSION['cart_quantity'][$productId]) ? $_SESSION['cart_quantity'][$productId] : 1;
    
    // Modify the quantity based on the action
    if ($action == 'increase') {
        $_SESSION['cart_quantity'][$productId] = $currentQuantity + 1;
    } elseif ($action == 'decrease' && $currentQuantity > 1) {
        $_SESSION['cart_quantity'][$productId] = $currentQuantity - 1;
    }
    
    // Return the new quantity as a response
    echo json_encode(['success' => true, 'newQuantity' => $_SESSION['cart_quantity'][$productId]]);
} else {
    echo json_encode(['success' => false]);
}
?>
