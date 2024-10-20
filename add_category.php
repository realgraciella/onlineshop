<?php
include('config.php');

// Function to fetch categories from the database
function fetchCategories($conn) {
    $categories = array();
    $result = $conn->query("SELECT * FROM category");

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }

    return $categories;
}

// Check if the form is submitted for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addCategory"])) {
    $categoryName = $_POST["category-name"];

    $sql = "INSERT INTO category (categoryName) VALUES ('$categoryName')";
    
    if ($conn->query($sql)) {
        // No need for success or error messages here, as they will be shown in the button notification
    } else {
        // No need for error messages here, as they will be shown in the button notification
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteCategoryId"])) {
    $deleteCategoryId = $_POST["deleteCategoryId"];

    try {
        $deleteSql = "DELETE FROM category WHERE id = ?";
        
        $deleteStmt = $conn->prepare($deleteSql);

        $deleteStmt->bindParam(1, $deleteCategoryId);

        $deleteStmt->execute();

        $categories = fetchCategories($conn);
    } catch (\Exception $e) {
        // No need for error messages here, as they will be shown in the button notification
    }
}

// Fetch initial categories
$categories = fetchCategories($conn);
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
        margin: 0 auto;
    }

    .staff-container h2 {
        text-align: center;
    }

    #category-form {
        max-width: 500px;
        height: 160px;
        margin: 0 auto;
        padding: 20px;
        border: 2px solid #FFC451;
        border-radius: 10px;
    }

    #category-name {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
        border: 1px solid #FFF;
        border-radius: 5px;
    }

    button[type="submit"] {
        background-color: #FFC451;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        color: black;
        background-color: #FFC451;
    }

    #category-table {
        border-collapse: collapse;
        overflow: hidden;
        margin-top: 20px;
        width: 100%;
    }

    #category-table th,
    #category-table td {
        border: 1px solid #FFC451;
        padding: 10px;
        text-align: center;
    }

    #category-table th {
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

    .edit-btn:hover {
        color: black;
        background-color: #B5EAD7;
    }
</style>

<body>

  <?php
    include 'config.php';
    include 'admin_header.php';
  ?>

  <main id="main">  
    <section>
        <div class="staff-container">
            <h2 style="text-align: center; margin-top: 50px; margin-bottom: 10px;">SERVICE CATEGORY</h2>
            <form id="category-form" method="post" action="add_category.php">
                <label for="category-name">Category Name:</label>
                <div class="input-wrapper">
                    <input type="text" id="category-name" name="category-name" placeholder="Enter category name" required>
                    <button type="submit" name="addCategory" style="float: right; background-color: #4CAF50; color: #fff; border-radius: 4px; border: 1px solid #45a049;" onclick="return confirm('Are you sure you want to add this category?') && confirmSuccessMessage();">Save</button>
                </div>
            </form>

            <br>

            <!-- Table to display categories -->
            <table id="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['id']; ?></td>
                            <td><?= $category['categoryName']; ?></td>
                            <td>
                                <!-- Edit button (you can add the edit functionality as needed) -->
                                <button class="edit-btn">Edit</button>

                                <!-- Delete button with a form -->
                                <form action="add_category.php" method="post" style="display: inline;">
                                    <input type="hidden" name="deleteCategoryId" value="<?= $category['id']; ?>">
                                    <button type="submit" class="delete-btn" style="background-color: red; color: white;" onclick="return confirm('Are you sure you want to delete this category?') && confirmSuccessMessage();">Delete</button>

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