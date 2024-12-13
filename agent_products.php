<?php
// Database connection
$connection = new mysqli('localhost', 'root', '', 'dmshop');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch sale products
$query = "SELECT products.*, categories.category_name FROM products JOIN categories ON products.category_id = categories.category_id WHERE on_sale = 1";
$result = $connection->query($query);

$saleProducts = [];
if ($result->num_rows > 0) {
    $saleProducts = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all products
$productsQuery = "SELECT products.*, categories.category_name FROM products JOIN categories ON products.category_id = categories.category_id";
$productsResult = $connection->query($productsQuery);
$products = $productsResult->fetch_all(MYSQLI_ASSOC);

// Fetch brands
$brandsQuery = "SELECT * FROM brands";
$brandsResult = $connection->query($brandsQuery);
$brands = $brandsResult->fetch_all(MYSQLI_ASSOC);

$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Display with Filters and Search</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding-top: 20px;
            margin: 0;
        }
        .display-container {
            padding: 25px 15px;
            margin: 65px auto;
            max-width: 1200px;
        }
        .sale-section {
            margin-bottom: 50px;
            background-color: #fff3e0;
            border: 1px solid #ff9800;
            padding: 20px;
            border-radius: 12px;
        }
        .sale-section h2 {
            font-size: 24px;
            font-weight: bold;
            color: #ff5722;
            margin-bottom: 20px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }
        .product-item {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            width: calc(33.333% - 25px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            text-align: center;
            position: relative;
        }
        .product-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        .product-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product-item h4 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
        }
        .filter-section {
            margin-top: 10px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            position: absolute;
        }
        .filter-section h3 {
            margin-bottom: 12px;
            font-size: 18px;
            font-weight: 600;
            color: #222;
        }
        .filter-section label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 15px;
            color: #555;
        }
        .filter-section .form-check-input {
            margin-right: 8px;
        }
        .filter-section .btn {
            width: 45%;
            margin-top: 10px;
            padding: 8px;
            border-radius: 20px;
            font-size: 14px;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }

        @media (max-width: 992px) {
            .product-item {
                width: calc(50% - 25px);
            }
        }
        @media (max-width: 576px) {
            .product-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'agent_header.php'; ?>

    <div class="display-container">
        <div class="row">
            <!-- Main Product Display -->
            <div class="col-md-9">
                <!-- Search Bar and Filter Button -->
                <div class="d-flex align-items-center mb-3">
                    <!-- Search Bar -->
                    <input type="text" id="search-bar" class="form-control me-2" placeholder="Search for products..." oninput="filterProducts()" style="flex-grow: 1;">
                    <!-- Filter Button -->
                    <button class="btn btn-primary filter-button" onclick="toggleFilterSection()">Filter Products</button>
                </div>

                <h1 class="mb-4">Products</h1>

                <!-- On Sale Products Section -->
                <div class="sale-section">
                    <h2>On Sale Products</h2>
                    <div class="product-container">
                        <?php if (count($saleProducts) > 0) : ?>
                            <?php foreach ($saleProducts as $product) : ?>
                                <div class="product-item" data-name="<?php echo strtolower($product['product_name']); ?>">
                                    <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                    <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <p>Price: <span class="text-decoration-line-through">$<?php echo number_format($product['old_price'], 2); ?></span> $<?php echo number_format($product['price'], 2); ?></p>
                                    <button type="button" class="btn btn-success" onclick="addToCart('<?php echo $product['product_id']; ?>')">Add to Cart</button>
                                    <button type="button" class="btn btn-warning">Buy Now</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No products available for sale.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- All Products Section -->
                <h2 class="mt-5">All Products</h2>
                <div class="product-container">
                <div class="product-container">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <?php
            // Set image path with fallback for missing or invalid images
            $imagePath = 'uploads/products/' . $product['product_image_url'];
            if (!file_exists($imagePath) || empty($product['product_image_url'])) {
                $imagePath = 'assets/img/default-image.png';
            }
            ?>
            <!-- Display product image -->
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">

            <!-- Display product details -->
            <h4><?= htmlspecialchars($product['product_name']) ?></h4>
            <p class="price">PHP <?= number_format($product['price'], 2) ?></p>
            <p>Stock Level: <?= $product['stock_level'] ?> available</p>

            <!-- Form for checking out -->
            <form method="POST" action="checkout.php" class="checkout-form">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <div class="form-group">
                    <label for="quantity-<?= $product['product_id'] ?>">Quantity:</label>
                    <input 
                        type="number" 
                        id="quantity-<?= $product['product_id'] ?>" 
                        name="quantity" 
                        min="1" 
                        max="<?= $product['stock_level'] ?>" 
                        required 
                        class="form-control">
                </div>
                <button type="submit" name="checkout" class="btn btn-success w-100">Checkout</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="col-md-3">
                <div class="filter-section">
                    <h3>Filter Products</h3>
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value="" id="filter-by-brand" onclick="applyFilters()"> By Brand
                    </label>
                    <div id="brand-filters" style="display:none;">
                        <?php foreach ($brands as $brand) : ?>
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" value="<?php echo $brand['brand_id']; ?>" onclick="applyFilters()"> <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn btn-success">Apply Filters</button>
                    <button type="button" class="btn btn-danger">Clear Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script>
        // Filter products by name
        function filterProducts() {
            let searchQuery = document.getElementById('search-bar').value.toLowerCase();
            let products = document.querySelectorAll('.product-item');

            products.forEach(product => {
                let productName = product.getAttribute('data-name');
                if (productName.includes(searchQuery)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Toggle the visibility of the filter section
        function toggleFilterSection() {
            const filterSection = document.querySelector('.filter-section');
            filterSection.style.display = (filterSection.style.display === 'none') ? 'block' : 'none';
        }

        // Add product to cart using AJAX
        function addToCart(productId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Product added to cart');
                } else {
                    alert('Error adding product to cart');
                }
            };
            xhr.send('product_id=' + productId);
        }
    </script>
</body>
</html>
