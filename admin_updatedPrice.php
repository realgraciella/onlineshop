<?php
// update_price.php
include 'database/db_connect.php';

if (isset($_POST['product_id']) && isset($_POST['new_price'])) {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];
    $on_sale = $_POST['on_sale'];

    try {
        // Fetch current product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $old_price = $product['price']; // Store old price

            // Update price and sale status
            $sql = "UPDATE products SET price = :new_price, old_price = :old_price, on_sale = :on_sale, updated_at = NOW() WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':new_price', $new_price);
            $stmt->bindParam(':old_price', $old_price);
            $stmt->bindParam(':on_sale', $on_sale, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id);

            if ($stmt->execute()) {
                echo "Price updated successfully.";
            } else {
                echo "Error updating price.";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
