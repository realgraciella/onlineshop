<?php
include 'database/db_connect.php';

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];

    // Fetch variations for the product
    $query = "SELECT * FROM product_variations WHERE product_id = (SELECT product_id FROM products WHERE product_name = ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$product_name]);
    $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($variations);
}
?>