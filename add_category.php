<?php
include('database/db_connect.php');

// Function to fetch categories from the database using PDO
function fetchCategories($pdo) {
    $categories = array();
    $sql = "SELECT c.category_id, c.category_name, b.brand_name 
            FROM categories c 
            JOIN brands b ON c.brand_id = b.brand_id";
    
    try {
        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row;
        }
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }

    return $categories;
}

// Check if the form is submitted for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addCategory"])) {
    $brandId = $_POST["brand_id"];  // New field for brand selection
    $categoryName = $_POST["category_name"];

    // Use prepared statements for insert
    try {
        $sql = "INSERT INTO categories (brand_id, category_name) VALUES (:brandId, :categoryName)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':brandId', $brandId, PDO::PARAM_INT);
        $stmt->bindParam(':categoryName', $categoryName, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            // Optionally, you could add success messages, but we avoid output here for cleaner UI
        } else {
            // Handle unsuccessful insert
        }
    } catch (PDOException $e) {
        // Handle exception for database query failure
        die("Error inserting category: " . $e->getMessage());
    }
}

// Check if a category is being deleted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteCategoryId"])) {
    $deleteCategoryId = $_POST["deleteCategoryId"];

    // Prepare and execute the delete query using PDO
    try {
        $deleteSql = "DELETE FROM categories WHERE category_id = :categoryId";
        $stmt = $pdo->prepare($deleteSql);
        $stmt->bindParam(':categoryId', $deleteCategoryId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch categories again after deletion
        $categories = fetchCategories($pdo);
    } catch (PDOException $e) {
        // Handle any errors during delete
        die("Error deleting category: " . $e->getMessage());
    }
}

// Fetch initial categories using PDO
$categories = fetchCategories($pdo);

// Fetch brands for the dropdown using PDO
$brands_result = $pdo->query("SELECT * FROM brands");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin Page - Add Category</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

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
</head>

<style>
    /* General Container Styling */
    .admin-container {
        margin: 65px auto;
        max-width: 800px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #fdfdfd;
        border-radius: 10px;
    }

    .admin-container h2 {
        text-align: center;
        font-size: 28px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Form Styling */
    #category-form {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f8f8f8;
    }

    label {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
        color: #333;
    }

    input[type="text"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        transition: border-color 0.3s ease-in-out;
    }

    input[type="text"]:focus, select:focus {
        outline: none;
        border-color: #ffc451;
        box-shadow: 0 0 5px rgba(255, 196, 81, 0.4);
    }

    button[type="submit"] {
        display: inline-block;
        background-color: #008a00;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
    }

    .input-wrapper input {
        flex-grow: 1;
    }

    .input-wrapper button {
        margin-left: 10px;
    }

    /* Table Styling */
    #category-table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #category-table th, #category-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
        font-size: 14px;
    }

    #category-table th {
        background-color: #008a00;
        color: #fff;
    }

    #category-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    #category-table tr:hover {
        background-color: #f1f1f1;
    }

    .edit-btn, .delete-btn {
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: background-color 0.3s ease-in-out;
    }

    .edit-btn {
        background-color: #008a00;
        color: white;
    }

    .edit-btn:hover {
        background-color: #31b0d5;
    }

    .delete-btn {
        background-color: #d9534f;
        color: white;
    }

    .delete-btn:hover {
        background-color: #c9302c;
    }
</style>

<body>

  <?php
    include 'admin_header.php';

    // Fetch brands for the dropdown
    $brands_result = $pdo->query("SELECT * FROM brands");
  ?>

<main id="main">  
    <section>
        <div class="admin-container">
            <h2 style="text-align: center; margin-top: 50px; margin-bottom: 10px;">ADD NEW CATEGORY</h2>
            <form id="category-form" method="post" action="add_category.php">
                <!-- Brand Dropdown -->
                <label for="brand-id">Select Brand:</label>
                <select id="brand-id" name="brand_id" required>
                    <option value="">Select Brand</option>
                    <?php while ($brand = $brands_result->fetch(PDO::FETCH_ASSOC)) : ?>
                        <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="category-name">Category Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="category-name" name="category_name" placeholder="Enter category name" required>
                    <button type="submit" name="addCategory" style="float: right; background-color: #008a00; color: #fff; border-radius: 4px; border: 1px solid #45a049;" onclick="return confirm('Are you sure you want to add this category?') && confirmSuccessMessage();">Save</button>
                </div>
            </form>

            <br>

            <!-- Table to display categories -->
            <table id="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Brand Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['category_id']; ?></td>
                            <td><?= $category['category_name']; ?></td>
                            <td><?= $category['brand_name']; ?></td>
                            <td>
                                <!-- Edit button (you can add the edit functionality as needed) -->
                                <button class="edit-btn">Edit</button>

                                <!-- Delete button with a form -->
                                <form action="add_category.php" method="post" style="display: inline;">
                                    <input type="hidden" name="deleteCategoryId" value="<?= $category['category_id']; ?>">
                                    <button type="submit" class="delete-btn" style="background-color: red; color: white;" onclick="return confirm('Are you sure you want to delete this category?') && confirmDeleteMessage();">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </section>
  </main><!-- End #main -->

  <script>
      function confirmDeleteMessage() {
        alert("Category deleted successfully");
        return true; // Continue with form submission
    }

    function confirmSuccessMessage() {
        alert("Category added successfully");
        return true; // Continue with form submission
    }
  </script>

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
