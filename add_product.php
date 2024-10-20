<?php
include('config.php'); // Make sure to include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST["productName"];
    $brandName = $_POST["brandName"];
    $productCategory = $_POST["productCategory"];
    $price = $_POST["price"];

    // Handling file upload
    $target_dir = "uploads/";
    
    // Create the "uploads" directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["productImage"]["name"]);

    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
        // Insert data into the products table
        $sql = "INSERT INTO products (productName, brandName, productCategory, productPrice, productImage) VALUES ('$productName', '$brandName', '$productCategory', $price, '$target_file')";

        if ($conn->query($sql)) {
            // Success message
            echo "Product added successfully!";
        } else {
            // Error message
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Error handling for file upload
        echo "Failed to upload the file.";
    }

    // Close the database connection
    $conn = null;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin Page</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
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
</head>

<style>
        .staff-container {
            margin-bottom: 10px;
        }

        .container h2 {
            text-align: center;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        #productForm {
            max-width: 500px;
            margin: 100px auto 20px;
            padding: 20px;
            border: 2px solid #FFC451;
            border-radius: 10px;
        }

        label,
        input,
        button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #FFC451;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            color: black;
            background-color: #FFC451;
        }

        #brandTable {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            border: 2px solid #FFC451;
            text-align: center;
        }

        #brandTable th,
        #brandTable td {
            border: 1px solid #FFC451;
            padding: 10px;
            text-align: center;
        }

        #brandTable th {
            background-color: #FFC451;
            color: white;
        }

        .edit-btn {
            background-color: #B5EAD7;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
        }

        .delete-btn {
            background-color: #FF7F7F;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
        }

        .delete-btn:hover {
            color: black;
            background-color: #FF7F7F;
        }

        .edit-btn:hover {
            color: black;
            background-color: #B5EAD7;
        }
    </style>>


<body>

  <?php
    include 'config.php';
    include 'admin_header.php';
  ?>

  <main id="main">
    <div class="staff-container">
        <form id="productForm" method="post" action="add_product.php" enctype="multipart/form-data">
                    <h2>PRODUCTS</h2>

            <label for="productName">Product Name:</label>
            <input type="text" id="productName" name="productName" required>

            <label for="brandName">Brand Name:</label>
            <input type="text" id="brandName" name="brandName" required>

            <label for="productCategory">Product Category:</label>
            <input type="text" id="productCategory" name="productCategory" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <label for="productImage">Product Image:</label>
            <input type="file" id="productImage" name="productImage" required>

            <button type="submit">Save</button>
        </form>

        <br>

        <table id="brandTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Brand Name</th>
                    <th>Product Category</th>
                    <th>Product Price</th>
                    <th>Product Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Assuming $conn is your database connection object
                $result = $conn->query("SELECT * FROM products");
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['productName']}</td>
                            <td>{$row['brandName']}</td>
                            <td>{$row['productCategory']}</td>
                            <td>{$row['productPrice']}</td>
                            <td><img src='{$row['productImage']}' alt='Product Image' width='100'></td>
                            <td class='action-btn-container'>
                                <button class='edit-btn'>Edit</button>
                                <br>
                                <button class='delete-btn'>Delete</button>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

  </main><!-- End #main -->

  <?php
    include 'footer.php';
  ?>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/admin.js"></script>

</body>

</html>