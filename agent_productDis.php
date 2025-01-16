<?php
include('database/db_connect.php');

// Fetch products from the database using PDO
$sql = "SELECT p.product_id, p.product_name, p.price, p.product_image_url, p.stock_level, b.brand_name, c.category_name 
        FROM products p
        JOIN brands b ON p.brand_id = b.brand_id
        JOIN categories c ON p.category_id = c.category_id";

$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Display</title>

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
</head>
<body>
    <?php include 'agent_header.php'; ?>

    <div class="product-container">
        <?php
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="product-item">
                    <img src="<?php echo $row['product_image_url']; ?>" alt="<?php echo $row['product_name']; ?>">
                    <h3><?php echo $row['product_name']; ?></h3>
                    <p>Brand: <?php echo $row['brand_name']; ?></p>
                    <p>Category: <?php echo $row['category_name']; ?></p>
                    <p>Price: PHP <?php echo number_format($row['price'], 2); ?></p>
                    <p>Stock: <?php echo $row['stock_level']; ?> items</p>
                    <button class="add-to-cart" onclick="addToCart(<?php echo $row['product_id']; ?>)">Add to Cart</button>
                    <button class="buy-now" onclick="buyNow(<?php echo $row['product_id']; ?>)">Buy Now</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>

    <script>
        function addToCart(productId) {
            // Add the product to cart logic here (e.g., via AJAX)
            alert('Product ' + productId + ' added to cart!');
        }

        function buyNow(productId) {
            // Redirect to the checkout page with the product ID (or handle the purchase logic here)
            window.location.href = 'checkout.php?product_id=' + productId;
        }
    </script>
</body>
</html>

<?php
// No need to explicitly close the connection for PDO, as it will be closed automatically when the script ends
?>
