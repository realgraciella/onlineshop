<?php
include 'database/db_connect.php'; 

// Fetch all products
$query = "SELECT products.*, categories.category_name FROM products JOIN categories ON products.category_id = categories.category_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch brands for filter
$brandQuery = "SELECT * FROM brands";
$brandStmt = $pdo->prepare($brandQuery);
$brandStmt->execute();
$brands = $brandStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch product variations for modal
$variationQuery = "SELECT * FROM product_variations WHERE product_id = ?";
$variationStmt = $pdo->prepare($variationQuery);

$successMessage = '';
// Handle checkout
if (isset($_POST['checkout'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $variation_id = $_POST['variation_id']; // Get the variation_id from the POST request

    // Fetch product details
    $product_query = "SELECT * FROM products WHERE product_name = ?";
    $product_stmt = $pdo->prepare($product_query);
    $product_stmt->execute([$product_name]);
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch variation details
    $variation_query = "SELECT * FROM product_variations WHERE variation_id = ?";
    $variation_stmt = $pdo->prepare($variation_query);
    $variation_stmt->execute([$variation_id]);
    $variation = $variation_stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $variation && $variation['stock_per_variation'] >= $quantity) {
        // Calculate total amount
        $total_amount = $variation['price'] * $quantity; // Use variation price if applicable

        // Insert sale into store_sales table
        $sale_query = "INSERT INTO store_sales (product_id, product_name, product_price, quantity, total_amount, variation_id) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $sale_stmt = $pdo->prepare($sale_query);
        $sale_stmt->execute([$product['product_id'], $product['product_name'], $variation['price'], $quantity, $total_amount, $variation_id]);

        // Update product stock level in the products table
        $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_name = ?";
        $update_product_stmt = $pdo->prepare($update_product_query);
        $update_product_stmt->execute([$quantity, $product_name]);

        // Update stock level for the specific variation
        $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE variation_id = ?";
        $update_variation_stmt = $pdo->prepare($update_variation_query);
        $update_variation_stmt->execute([$quantity, $variation_id]);

        $successMessage = "Sale recorded successfully. Total amount: PHP $total_amount";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Insufficient stock or invalid product.'
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert2 CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f5f7;
            padding-top: 40px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 50px;
            width: 70%;
        }
        .product-card {
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
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product-card h4 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .product-card p {
            font-size: 0.9rem;
            color: #555;
        }
        .product-card .price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
        }
        .filter-section {
            padding: 25px;
            margin-bottom: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .filter-section select,
        .filter-section input {
            width: 48%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 0.9rem;
        }
        .checkout-list {
            position: fixed;
            right: 20px;
            top: 80px;
            width: 25%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .checkout-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .checkout-item button {
            padding: 4px 8px;
            font-size: 0.8rem;
            background-color: #dc3545;
            border: none;
            color: white;
            border-radius: 4px;
        }
        .checkout-item span {
            font-weight: 600;
        }
        /* Modal Styling */
        .modal-body {
            font-size: 0.9rem;
        }

    /* Media Queries for Responsiveness */
@media (max-width: 1200px) {
    .product-card {
        width: 30%;
    }
}

@media (max-width: 992px) {
    .product-card {
        width: 45%;
    }
}

@media (max-width: 768px) {
    .product-card {
        width: 100%;
    }
    .filter-section {
        flex-direction: column;
        align-items: flex-start;
    }
    .checkout-list {
        width: 100%;
        position: static;
        box-shadow: none;
    }
}
</style>
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="container">
<h2 class="text-center mt-4 mb-4">POS</h2>

<!-- Filter and Search Section -->
<div class="filter-section">
    <input type="text" id="searchInput" placeholder="Search for products..." onkeyup="searchProducts()">
    <select id="brandFilter" onchange="filterProducts()">
        <option value="">Select a brand</option>
        <?php foreach ($brands as $brand): ?>
            <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Product Display -->
<div class="product-container" id="productContainer">
    <?php foreach ($products as $product): ?>
        <div class="product-card" data-brand-id="<?= $product['brand_id'] ?>" data-name="<?php echo strtolower($product['product_name']); ?>" onclick="showProductModal('<?= $product['product_name'] ?>')">
            <img src="<?php echo $product['product_image_url']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
            <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
            <p>Stock: <?php echo $product['stock_level']; ?></p>
            <p class="price">PHP <?php echo number_format($product['price'], 2); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<!-- Checkout List -->
<div class="checkout-list">
    <h4>Checkout List</h4>
    <div id="checkoutList"></div>
    <button class="btn btn-success" onclick="checkout()">Checkout</button>
</div>

<!-- Modal for Product Details -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>Variation: </h5>
                <select id="variationSelect">
                    <option value="">Select variation</option>
                </select>
                <h5>Quantity: </h5>
                <button onclick="adjustQuantity(-1)">-</button>
                <span id="quantityDisplay">1</span>
                <button onclick="adjustQuantity(1)">+</button>
                <button onclick="addToList()">Add to List</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 JS -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    let selectedProductName = null;
    let checkoutList = [];

    // Filter products based on brand
    function filterProducts() {
        const selectedBrand = document.getElementById('brandFilter').value;
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            if (selectedBrand === "" || card.getAttribute('data-brand-id') === selectedBrand) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Search products based on input
    function searchProducts() {
        const searchQuery = document.getElementById('searchInput').value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const productName = card.getAttribute('data-name');
            if (productName.includes(searchQuery)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Show modal with product details and variations
    function showProductModal(productName) {
    selectedProductName = productName;
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();

    // Fetch product variations
    fetch(`get_variations.php?product_name=${productName}`)
        .then(response => response.json())
        .then(variations => {
            const variationSelect = document.getElementById('variationSelect');
            variationSelect.innerHTML = `<option value="">Select variation</option>`;
            variations.forEach(variation => {
                variationSelect.innerHTML += `<option value="${variation.variation_id}">${variation.variation_value}</option>`;
            });
        })
        .catch(error => {
            console.error('Error fetching variations:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not fetch product variations.'
            });
        });
    }

    // Adjust quantity for the modal
    function adjustQuantity(amount) {
        const quantityDisplay = document.getElementById('quantityDisplay');
        let currentQuantity = parseInt(quantityDisplay.textContent);
        currentQuantity += amount;
        if (currentQuantity < 1) currentQuantity = 1;
        quantityDisplay.textContent = currentQuantity;
    }

    // Add product to checkout list
        function addToList() {
            const variation = document.getElementById('variationSelect').value; // This will still get the variation_id
            const variationText = document.getElementById('variationSelect').options[document.getElementById('variationSelect').selectedIndex].text; // Get the variation_value
            const quantity = parseInt(document.getElementById('quantityDisplay').textContent);

            // Fetch price for selected product
            const selectedProduct = <?php echo json_encode($products); ?>.find(product => product.product_name === selectedProductName);

            if (selectedProduct) {
                const productPrice = parseFloat(selectedProduct.price); // Fetch the actual price of the selected product

                // Check if product already exists in the list
                let productInList = checkoutList.find(item => item.productName === selectedProductName && item.variation === variationText);
                if (!productInList) {
                    checkoutList.push({ productName: selectedProductName, variation: variationText, quantity, price: productPrice });
                    updateCheckoutList();
                }
            }
        }

    // Update checkout list UI
    function updateCheckoutList() {
        const checkoutListDiv = document.getElementById('checkoutList');
        checkoutListDiv.innerHTML = '';

        checkoutList.forEach(item => {
            const productDiv = document.createElement('div');
            productDiv.classList.add('checkout-item');
            productDiv.innerHTML = `
                <span>${item.productName} (Variation: ${item.variation}, Quantity: ${item.quantity}, Price: PHP ${item.price.toFixed(2)})</span>
                <button onclick="removeFromList('${item.productName}')">Remove</button>
            `;
            checkoutListDiv.appendChild(productDiv);
        });
    }

    // Remove product from checkout list
    function removeFromList(productName) {
        checkoutList = checkoutList.filter(item => item.productName !== productName);
        updateCheckoutList();
    }

    // Handle checkout action (Proceed to bill printing)
    function checkout() {
    // Prepare data to send to the server
    const checkoutData = checkoutList.map(item => ({
        product_name: item.productName,
        quantity: item.quantity,
        variation_id: item.variation // Assuming you stored variation_id in the checkoutList
    }));

    // Check if there are items in the checkout list
    if (checkoutData.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No items in checkout',
            text: 'Please add items to the checkout list before proceeding.'
        });
        return; // Exit the function if the list is empty
    }

    // Redirect to checkout.php with the checkout data
    const queryString = new URLSearchParams({ data: JSON.stringify(checkoutData) }).toString();
    window.location.href = `checkout.php?${queryString}`;
}
</script>
</body>
</html>