<?php
session_start();
$connection = new mysqli('localhost', 'root', '', 'dmshop1');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $query = "SELECT * FROM product_variations WHERE product_id = $productId";
    $result = $connection->query($query);

    $variations = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $variations[] = $row;
        }
    }
    echo json_encode($variations);
}

$connection->close();
?>