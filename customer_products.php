<?php
include 'database/db_connect.php';

// Fetch sale products
$saleQuery = "SELECT products.*, categories.category_name, product_variations.variation_value 
              FROM products 
              JOIN categories ON products.category_id = categories.category_id 
              JOIN product_variations ON products.product_id = product_variations.product_id 
              WHERE on_sale = 1";
$saleStmt = $pdo->prepare($saleQuery);
$saleStmt->execute();
$saleProducts = [];
while ($row = $saleStmt->fetch(PDO::FETCH_ASSOC)) {
    // Convert price and old_price to float
    $row['price'] = floatval($row['price']);
    $row['old_price'] = floatval($row['old_price']);
    $saleProducts[] = $row;
}

// Fetch all products
$productsQuery = "SELECT products.*, categories.category_name, product_variations.price_per_variation, product_variations.variation_value 
                  FROM products 
                  JOIN categories ON products.category_id = categories.category_id 
                  JOIN product_variations ON products.product_id = product_variations.product_id";
$productsStmt = $pdo->prepare($productsQuery);
$productsStmt->execute();
$products = [];
while ($row = $productsStmt->fetch(PDO::FETCH_ASSOC)) {
    // Convert price_per_variation to float
    $row['price_per_variation'] = floatval($row['price_per_variation']);
    $products[] = $row;
}

// Fetch brands
$brandsQuery = "SELECT * FROM brands";
$brandsStmt = $pdo->prepare($brandsQuery);
$brandsStmt->execute();
$brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);

// Close connection
$pdo = null;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Display with Filters and Search</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
            padding:  8px;
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
    <?php include 'customer_header.php'; ?>

    <div class="display-container">
        <div class="row">
            <div class="col-md-9">
                <div class="d-flex align-items-center mb-3">
                    <input type="text" id="search-bar" class="form-control me-2" placeholder="Search for products..." oninput="filterProducts()" style="flex-grow: 1;">
                    <button class="btn btn-primary filter-button" onclick="toggleFilterSection()">Filter Products</button>
                </div>

                <h1 class="mb-4">Products</h1>

                <div class="sale-section">
                    <h2>On Sale Products</h2>
                        <div class="product-container">
                                <?php if (count($saleProducts) > 0) : ?>
                                    <?php foreach ($saleProducts as $product) : ?>
                                        <div class="product-item" data-name="<?php echo strtolower($product['product_name']); ?>">
                                            <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                            <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                            <p>Variation: <?php echo htmlspecialchars($product['variation_value']); ?></p> <!-- Changed from category_name to variation_value -->
                                            <?php
                                                $oldPrice = floatval($product['old_price']);
                                                $newPrice = floatval($product['price']);
                                            ?>
                                            <p>Price: <span class="text-decoration-line-through">PHP <?php echo number_format($oldPrice, 2); ?></span> PHP <?php echo number_format($newPrice, 2); ?></p>
                                            <button type="button" class="btn btn-success" onclick="showVariationModal('<?php echo $product['product_id']; ?>')">Add to Cart</button>
                                            <button type="button" class="btn btn-warning" onclick="showVariationModal('<?php echo $product['product_id']; ?>', true)">Buy Now</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p>No products available for sale.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                </div>

                <h2 class="mt-5">All Products</h2>
                    <div class="product-container">
                        <?php foreach ($products as $product): ?>
                            <div class="product-item" data-name="<?php echo strtolower($product['product_name']); ?>">
                                <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                <p>Variation: <?php echo htmlspecialchars($product['variation_value']); ?></p> <!-- Changed from category_name to variation_value -->
                                <p>Price: PHP <?php echo number_format(floatval($product['price_per_variation']), 2); ?></p>
                                <p>Stock Level: <?php echo $product['stock_level']; ?> available</p>
                                <button type="button" class="btn btn-success" onclick="showVariationModal('<?php echo $product['product_id']; ?>')">Add to Cart</button>
                                <button type="button" class="btn btn-warning" onclick="showVariationModal('<?php echo $product['product_id']; ?>', true)">Buy Now</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
            </div>

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

    <!-- Modal for Product Variations -->
<div class="modal fade" id="variationModal" tabindex="-1" aria-labelledby="variationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variationModalLabel">Select Variation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProductId">
                <div class="form-group">
                    <label for="variationSelect">Select Variation:</label>
                    <select id="variationSelect" class="form-control"></select>
                </div>
                <div class="mt-3">
                    <label for="quantity">Quantity:</label>
                    <button type="button" class="btn btn-secondary" onclick="changeQuantity(-1)">-</button>
                    <input type="number" id="quantity" value="1" min="1" style="width: 50px; text-align: center;">
                    <button type="button" class="btn btn-secondary" onclick="changeQuantity(1)">+</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="addToCartButton">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

    <!-- JS Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedVariationId = null;

        function showVariationModal(productId) {
    $('#modalProductId').val(productId);
    $('#variationSelect').empty(); // Clear previous options

    // Fetch the product variations
    $.ajax({
        url: 'fetch_product_variations.php',
        type: 'POST',
        data: { product_id: productId },
        success: function(data) {
            try {
                const variations = JSON.parse(data);
                if (variations.length === 0) {
                    alert('No variations found for this product.');
                    return;
                }

                // Populate the dropdown with variations
                variations.forEach(variation => {
                    $('#variationSelect').append(`
                        <option value="${variation.variation_id}" data-price="${variation.price_per_variation}" data-stock="${variation.stock_per_variation}">
                            ${variation.variation_value} - PHP ${variation.price_per_variation} (Stock: ${variation.stock_per_variation})
                        </option>
                    `);
                });

                $('#variationModal').modal('show');
            } catch (e) {
                console.error('Error parsing variations :', e);
                alert('Error loading variations.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Error fetching variations.');
        }
    });
}

function addToCart(productId) {
    const variationId = $('#variationSelect').val();
    const quantity = $('#quantity').val();
    const pricePerVariation = $('#variationSelect option:selected').data('price');

    $.ajax({
        url: 'add_to_cart.php',
        type: 'POST',
        data: {
            product_id: productId,
            variation_id: variationId,
            quantity: quantity,
            price_per_variation: pricePerVariation
        },
        success: function(response) {
            alert('Product added to cart successfully!');
            $('#variationModal').modal('hide');
        },
        error: function() {
            alert('Error adding product to cart.');
        }
    });
}

// Update the button click event
$('#addToCartButton').off('click').on('click', function() {
    addToCart($('#modalProductId').val());
});

        function updateVariationDetails(variationId, price, stock) {
            selectedVariationId = variationId;
            $('#addToCartButton').data('price', price);
            $('#addToCartButton').data('stock', stock);
        }

        function changeQuantity(amount) {
            const quantityInput = $('#quantity');
            let currentQuantity = parseInt(quantityInput.val());
            currentQuantity += amount;
            if (currentQuantity < 1) currentQuantity = 1;
            quantityInput.val(currentQuantity);
        }

    
    </script>
</body>
</html>