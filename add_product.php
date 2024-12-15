<?php
// Include PDO database connection
include 'database/db_connect.php';

// Fetch brands from the database using PDO
$brands_query = "SELECT * FROM brands ORDER BY brand_name ASC";
$stmt = $pdo->query($brands_query);

// Handle form submission for product upload
if (isset($_POST['upload'])) {
    // Other form data
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $brand_id = $_POST['brand_id'];
    $category_id = $_POST['category_id'];
    $stock_level = $_POST['stock_level'];

    // Fetch variation data
    $variation_values = $_POST['variation_value'] ?? [];
    $stock_per_variation = $_POST['stock_per_variation'] ?? [];
    $price_per_variation = $_POST['price_per_variation'] ?? [];

    // Ensure that all arrays are non-empty and have matching lengths
    if (count($variation_values) !== count($stock_per_variation) || count($variation_values) !== count($price_per_variation)) {
        echo "Error: Mismatched variation data.";
        exit;
    }

    // Calculate minimum and maximum prices
    $min_price = null;
    $max_price = null;

    foreach ($price_per_variation as $price) {
        $price = floatval($price); // Ensure price is treated as a float
        if ($min_price === null || $price < $min_price) {
            $min_price = $price;
        }
        if ($max_price === null || $price > $max_price) {
            $max_price = $price;
        }
    }

    // Format the price range for storage as VARCHAR
    if ($min_price !== null) {
        if ($max_price !== null && $min_price != $max_price) {
            $price_range = "PHP " . number_format($min_price, 2) . " - PHP " . number_format($max_price, 2); // If there is a range
        } else {
            $price_range = "PHP " . number_format($min_price, 2); // If both prices are the same
        }
    } else {
        $price_range = "N/A"; // Handle case where no prices are provided
    }

    // Handle image upload logic
    $target_dir = "uploads/products/";
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate and process the image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["product_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1 && move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        try {
            $pdo->beginTransaction();

            // Insert product details with calculated price range
            $sql = "INSERT INTO products (product_name, product_desc, brand_id, category_id, price, product_image_url, stock_level)
                    VALUES (:product_name, :product_desc, :brand_id, :category_id, :price, :product_image_url, :stock_level)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_name' => $product_name,
                ':product_desc' => $product_desc,
                ':brand_id' => $brand_id,
                ':category_id' => $category_id,
                ':price' => $price_range, // Store the price range as VARCHAR
                ':product_image_url' => $target_file,
                ':stock_level' => $stock_level,
            ]);

            $product_id = $pdo->lastInsertId();

            // Insert variations into the database
            if (!empty($variation_values) && !empty($stock_per_variation) && !empty($price_per_variation)) {
                // Prepare SQL for inserting variations
                $variation_sql = "INSERT INTO product_variations (product_id, variation_value, stock_per_variation, price_per_variation)
                                VALUES (:product_id, :variation_value, :stock_per_variation, :price_per_variation)";
                $variation_stmt = $pdo->prepare($variation_sql);

                // Loop through the variations and insert them
                foreach ($variation_values as $index => $variation_value) {
                    $stock = $stock_per_variation[$index] ?? null;
                    $price = $price_per_variation[$index] ?? null;

                    if ($variation_value && $stock !== null && $price !== null) {
                        // Ensure price is treated as a float for decimal storage
                        $price = floatval($price); // Convert to float

                        // Insert variation into the database
                        $variation_stmt->execute([
                            ':product_id' => $product_id,
                            ':variation_value' => $variation_value,
                            ':stock_per_variation' => $stock,
                            ':price_per_variation' => $price, // Store variation price as a decimal
                        ]);
                    }
                }
            } else {
                echo "No variations provided.";
            }

            $pdo->commit();
            echo "Product uploaded successfully.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Product</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css"> 
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    
    <style>
        .admin-container {
            background-color: #ffffff;
            margin: 80px auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: 600;
        }
        .form-control {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 12px 18px;
            width: 100%;
            border: 1px solid #008a00;
            border-radius: 5px;
            font-size: 15px;
        }
        .btn-primary:hover {
            background-color: #008a00;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <div class="admin-container mt-5">
        <h2 style="text-align: center">Add Product</h2>
        <form action="add_product.php" method="post" enctype="multipart/form-data">

            <!-- Brand Dropdown -->
            <div class="form-group">
                <label for="brand_id">Brand:</label>
                <select name="brand_id" id="brand_id" class="form-control" onchange="fetchCategories(this.value)" required>
                    <option value="">Select Brand</option>
                    <?php while ($brand = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Category Dropdown -->
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                </select>
            </div>

            <!-- Product Name -->
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>

            <!-- Product Description -->
            <div class="form-group">
                <label for="product_desc">Product Description:</label>
                <textarea name="product_desc" class="form-control" rows="4" required></textarea>
            </div>

            <!-- Product Variation -->
            <div id="variation-container">
                <label>Variations:</label>
                <div class="form-group variation-group">
                    <button type="button" class="btn btn-primary add-variation">Add Variation</button>
                </div>
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" name="price" class="form-control" placeholder="PHP" readonly>
            </div>

            <!-- Stock Level -->
            <div class="form-group">
                <label for="stock_level">Stock Level:</label>
                <input type="number" name="stock_level" class="form-control" required min="1">
            </div>

            <!-- Product Image -->
            <div class="form-group">
                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" class="form-control" accept="image/*" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="upload" class="btn btn-primary">Upload Product</button>
        </form>
    </div>

    <script>
        // JavaScript to fetch categories based on the selected brand
        function fetchCategories(brandId) {
            if (brandId === "") {
                document.getElementById('category_id').innerHTML = "<option value=''>Select Category</option>";
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_categories.php?brand_id=" + brandId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('category_id').innerHTML = xhr.responseText;
                } else {
                    console.log('Error fetching categories:', xhr.status);
                }
            };
            xhr.send();
        }

        // JavaScript for handling variations and calculating price range
        document.querySelector('.add-variation').addEventListener('click', function () {
            const variationContainer = document.getElementById('variation-container');

            // Create a new variation group
            const newVariationGroup = document.createElement('div');
            newVariationGroup.classList.add('form-group', 'variation-group');
            newVariationGroup.innerHTML = `
                <div class="variation-values">
                    <input type="text" name="variation_value[]" class="form-control" placeholder="Value (e.g., Large)" required>
                    <input type="number" name="stock_per_variation[]" class="form-control" placeholder="Stock" required>
                    <input type="number" name="price_per_variation[]" class="form-control" placeholder="Price" required>
                </div>
                <button type="button" class="btn btn-danger remove-variation">Remove Variation</button>
            `;

            // Append the new group to the container
            variationContainer.appendChild(newVariationGroup);

            // Bind event listeners to new inputs
            bindEventsToVariation(newVariationGroup);
        });

        // Function to bind necessary events to variation inputs
        function bindEventsToVariation(variationGroup) {
            const stockInput = variationGroup.querySelector('input[name="stock_per_variation[]"]');
            if (stockInput) {
                stockInput.addEventListener('input', calculateStockLevel);
            }

            const priceInput = variationGroup.querySelector('input[name="price_per_variation[]"]');
            if (priceInput) {
                priceInput.addEventListener('input', calculatePriceRange);
            }

            const removeVariationButton = variationGroup.querySelector('.remove-variation');
            if (removeVariationButton) {
                removeVariationButton.addEventListener('click', function () {
                    variationGroup.remove();
                    calculateStockLevel();
                    calculatePriceRange();
                });
            }
        }

        // Calculate and update the price range
        function calculatePriceRange() {
            const priceInputs = document.querySelectorAll('input[name="price_per_variation[]"]');
            let minPrice = Infinity;
            let maxPrice = -Infinity;
            let priceCount = 0;

            priceInputs.forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value)) {
                    priceCount++;
                    if (value < minPrice) minPrice = value;
                    if (value > maxPrice) maxPrice = value;
                }
            });

            const priceInput = document.querySelector('input[name="price"]');
            if (priceCount === 1) {
                priceInput.value = `PHP ${minPrice}`;
            } else if (minPrice !== Infinity && maxPrice !== -Infinity) {
                priceInput.value = `PHP ${minPrice} - ${maxPrice}`;
            } else {
                price
                priceInput.value = "Price: N/A";
            }
        }

        // Calculate and update the total stock level
        function calculateStockLevel() {
            const variationStocks = document.querySelectorAll('input[name="stock_per_variation[]"]');
            const stockLevelInput = document.querySelector('input[name="stock_level"]');

            let totalStock = 0;
            variationStocks.forEach(stockInput => {
                const value = parseInt(stockInput.value, 10);
                if (!isNaN(value) && value > 0) {
                    totalStock += value;
                }
            });

            stockLevelInput.value = totalStock;
        }

        // Initial setup: Bind stock calculation to pre-existing variations
        document.querySelectorAll('.variation-group').forEach(bindEventsToVariation);
    </script>
</body>
</html>