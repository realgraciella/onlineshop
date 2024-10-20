<?php
include('config.php'); // Make sure to include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brandName = $_POST["brandName"];

    // Insert data into the brand table
    $sqlInsert = "INSERT INTO brand (brandName) VALUES (:brandName)";
    $stmtInsert = $conn->prepare($sqlInsert);

    if ($stmtInsert) {
        $stmtInsert->bindParam(':brandName', $brandName);

        if ($stmtInsert->execute()) {
            // If the insertion is successful
            echo "Brand added successfully!";
        } else {
            // Error message
            echo "Error executing insert query: " . $stmtInsert->errorInfo()[2];
        }

        // Close the statement
        $stmtInsert->closeCursor();
    } else {
        // Error message
        echo "Error preparing insert statement";
    }

    // Prevent further execution and output
    exit();
}

// Fetch data from the brand table
$sqlSelect = "SELECT brandName FROM brand";
$resultSelect = $conn->query($sqlSelect);
$brandData = array();

if ($resultSelect) {
    while ($row = $resultSelect->fetch(PDO::FETCH_ASSOC)) {
        $brandData[] = $row;
    }
} else {
    echo "Error fetching data: " . $conn->error;
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
    margin-top: 90px;
    margin-bottom: 10px;
}

#brandForm {
    max-width: 500px;
    margin: 0 auto 20px;
    padding: 20px;
    border: 2px solid #FFC451;
    border-radius: 10px;
}

.form-group {
    margin-bottom: 20px;
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
    height: 40px;
}

.edit-btn:hover {
    color: black;
    background-color: #B5EAD7;
}

.delete-btn:hover {
    color: black;
    background-color: #FF7F7F;
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
    <h2 style="text-align: center; margin-top: 90px; margin-bottom: 10px;">PRODUCTS BRAND</h2>

    <form id="brandForm" method="post" action="add_brand.php">
        <div class="form-group">
            <label for="brandName">Brand Name:</label>
            <input type="text" id="brandName" name="brandName" required>
        </div>

        <div class="form-group">
            <button type="submit">Save</button>
        </div>
    </form>

    <table id="brandTable">
        <thead>
            <tr>
                <th>Brand Name</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brandData as $brandRow) : ?>
                <tr>
                    <td><?php echo $brandRow['brandName']; ?></td>
                    <td>
                        <button class="edit-btn" style="background-color: #B5EAD7;">Edit</button>
                        <button class="delete-btn" style="background-color: #FF7F7F;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
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