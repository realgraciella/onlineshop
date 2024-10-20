<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $_POST["categoryName"];
    $categoryDescription = $_POST["categoryDescription"];

    $sql = "INSERT INTO productcategory (categoryName, categoryDesc) VALUES (:categoryName, :categoryDescription)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bindParam(':categoryName', $categoryName);
        $stmt->bindParam(':categoryDescription', $categoryDescription);
        
        try {
            $stmt->execute();
            echo "Category added successfully!";
        } catch (PDOException $e) {
            echo "Error executing query: " . $e->getMessage();
        }

        $stmt->closeCursor(); // Close the cursor to allow for re-execution of the statement
    } else {
        echo "Error preparing statement";
    }
}

// Fetch data from the productcategory table
try {
    $sql = "SELECT categoryName, categoryDesc FROM productcategory";
    $result = $conn->query($sql);
    $data = array();

    if ($result) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    }
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}

// Close the PDO connection
$conn = null;
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

  <style>
    .admin-container {
    margin-bottom: 10px;
}

.container h2 {
    text-align: center;
    margin-top: 0px;
    margin-bottom: 10px;
}

#categoryForm {
    max-width: 500px;
    margin: 100px auto 20px;
    padding: 20px;
    border: 2px solid #FFC451;
    border-radius: 10px;
}

.form-group {
    margin-bottom: 20px;
}

label,
input,
textarea,
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

#categoryTable {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    border: 2px solid #FFC451;
    text-align: center;
}

#categoryTable th,
#categoryTable td {
    border: 1px solid #FFC451;
    padding: 10px;
    text-align: center;
}

#categoryTable th {
    background-color: #FFC451;
    color: white;
}

.edit-btn,
.delete-btn {
    border: none;
    padding: 5px 8px; /* Adjusted padding for smaller size */
    font-size: 12px; /* Adjusted font size for smaller size */
    border-radius: 5px;
    cursor: pointer;
    margin-right: 5px;
    display: inline-block; /* Added to prevent stretching */
    width: 100px; /* Adjusted width */
    height: 40px; /* Adjusted height */
}

.edit-btn {
    background-color: #B5EAD7;
    color: white;
}

.delete-btn {
    background-color: #FF7F7F;
    color: white;
}

.edit-btn:hover {
    color: black;
    background-color: #8AC6D1;
}

.delete-btn:hover {
    color: black;
    background-color: #D86161;
}


  </style>
</head>

<body>

  <?php
    include 'config.php';
    include 'admin_header.php';
  ?>

  <main id="main">

   <div class="admin-container">
    <form id="categoryForm" method="post" action="add_product_category.php">
        <h2>PRODUCTS CATEGORY</h2>

        <div class="form-container">
            <div class="form-group">
                <label for="categoryName">Category Name:</label>
                <input type="text" id="categoryName" name="categoryName" required>
            </div>

            <div class="form-group">
                <label for="categoryDescription">Category Description:</label>
                <textarea id="categoryDescription" name="categoryDescription" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <button type="submit">Save</button>
            </div>
        </div>
    </form>
    
    <div class="table-container">
        <table id="categoryTable">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Category Description</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <td><?php echo $row['categoryName']; ?></td>
                        <td><?php echo $row['categoryDesc']; ?></td>
                        <td>
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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