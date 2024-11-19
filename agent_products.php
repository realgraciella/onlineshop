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
                                    <button type="button" class="btn btn-success">Add to Cart</button>
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
                    <?php if (count($products) > 0) : ?>
                        <?php foreach ($products as $product) : ?>
                            <div class="product-item" data-name="<?php echo strtolower($product['product_name']); ?>">
                                <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                                <p>Price: PHP <?php echo number_format($product['price'], 2); ?></p>
                                <button type="button" class="btn btn-success" onclick="addToCart('<?php echo $product['product_id']; ?>')">Add to Cart</button>
                                <button type="button" class="btn btn-warning">Buy Now</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No products available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="col-md-3">
                <div class="filter-section">
                    <h3>Filter Products by Brand</h3>
                    <form id="brand-filter-form">
                        <?php if (count($brands) > 0) : ?>
                            <?php foreach ($brands as $brand) : ?>
                                <label>
                                    <input type="checkbox" name="brand" value="<?php echo $brand['brand_name']; ?>" class="form-check-input">
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="text-center">
                            <button type="button" class="btn btn-success" onclick="applyFilters()">Apply Filters</button>
                            <button type="button" class="btn btn-danger" onclick="clearFilters()">Clear Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFilterSection() {
            const filterSection = document.querySelector('.filter-section');
            filterSection.style.display = (filterSection.style.display === 'block') ? 'none' : 'block';
        }

        function filterProducts() {
            const searchQuery = document.getElementById('search-bar').value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const name = item.getAttribute('data-name');
                if (name.includes(searchQuery)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function applyFilters() {
            const checkboxes = document.querySelectorAll('input[name="brand"]:checked');
            const selectedBrands = Array.from(checkboxes).map(checkbox => checkbox.value.toLowerCase());
            const productItems = document.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const matchesBrand = selectedBrands.length === 0 || selectedBrands.some(brand => name.includes(brand));
                item.style.display = matchesBrand ? 'block' : 'none';
            });
        }

        function clearFilters() {
            const checkboxes = document.querySelectorAll('input[name="brand"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            applyFilters();
        }

        function addToCart(productId) {
            alert('Added to cart: ' + productId);
        }
    </script>
</body>
</html>
