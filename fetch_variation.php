<?php
session_start();
include 'database/db_connect.php'; // Include the PDO connection

if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    
    // Prepare the query
    $query = "SELECT * FROM product_variations WHERE product_id = :product_id";
    $stmt = $pdo->prepare($query);

    // Bind the parameter to avoid SQL injection
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch all results
    $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output as JSON
    echo json_encode($variations);
}
?>
