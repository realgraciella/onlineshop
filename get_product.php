<?php
include 'database/db_connect.php';

// get_product.php
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Fetch product details
    $productQuery = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $pdo->prepare($productQuery);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch product variations
    $variationQuery = "SELECT * FROM product_variations WHERE product_id = ?";
    $variationStmt = $pdo->prepare($variationQuery);
    $variationStmt->execute([$product_id]);
    $variations = $variationStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'product_name' => $product['product_name'],
        'price' => $product['price'],
        'stock_level' => $product['stock_level'],
        'variations' => $variations
    ]);
}

?>
