<?php
// Include PDO database connection
include 'database/db_connect.php';

// Fetch brands from the database using PDO
$brands_query = "SELECT * FROM brands";
$stmt = $pdo->query($brands_query);

// Handle form submission for product upload
if (isset($_POST['upload'])) {
    // Get form data
    $product_name = $_POST['product_name'];
    $brand_id = $_POST['brand_id'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock_level = $_POST['stock_level']; // Get stock level from form

    // Handle image upload
    $target_dir = "uploads/products/";
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 500KB)
    if ($_FILES["product_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain image file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Upload file and insert product info into database if no errors
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            try {
                // Prepare SQL query using PDO to insert product info
                $sql = "INSERT INTO products (product_name, brand_id, category_id, price, product_image_url, stock_level)
                        VALUES (:product_name, :brand_id, :category_id, :price, :product_image_url, :stock_level)";
                $stmt = $pdo->prepare($sql);

                // Bind parameters to the query
                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
                $stmt->bindParam(':price', $price, PDO::PARAM_STR);
                $stmt->bindParam(':product_image_url', $target_file);
                $stmt->bindParam(':stock_level', $stock_level, PDO::PARAM_INT); // Bind stock level

                // Execute the query
                if ($stmt->execute()) {
                    echo "Product uploaded successfully.";
                } else {
                    echo "Error: Could not insert product into the database.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Product</title>
     <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
        .btn-primary:hover{
            background-color: #008a00;
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
                        <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>
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

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
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
                // If no brand is selected, clear the category dropdown
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
    </script>
</body>
</html>
