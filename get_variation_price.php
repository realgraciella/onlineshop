<?php
include 'database/db_connect.php';

if (isset($_GET['variation_id'])) {
    $variation_id = $_GET['variation_id'];

    $query = "SELECT price FROM product_variations WHERE variation_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$variation_id]);
    $variation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($variation) {
        echo json_encode(['price' => $variation['price']]);
    } else {
        echo json_encode(['error' => 'Variation not found']);
    }
} else {
    echo json_encode(['error' => 'No variation ID provided']);
}
?>