<?php
include('config.php');

function fetchCategories($conn) {
    $categories = array();
    $result = $conn->query("SELECT * FROM category");

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }

    return $categories;
}

// Initialize variables
$categoryName = "";
$serviceName = "";
$targetFile = "";
$price = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are set
    if (isset($_POST["category-name"], $_POST["service-name"], $_FILES["service-image"]["name"], $_POST["price"])) {
        $categoryName = $_POST["category-name"];
        $serviceName = $_POST["service-name"];
        $price = $_POST["price"];

        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES["service-image"]["name"]);
        move_uploaded_file($_FILES["service-image"]["tmp_name"], $targetFile);

        $sql = "INSERT INTO services (categoryName, serviceName, Image, Price) VALUES ('$categoryName', '$serviceName', '$targetFile', '$price')";

        if ($conn->query($sql)) {
            // No need for additional notifications here
        } else {
            // No need for additional notifications here
        }
    } else {
        // No need for additional notifications here
    }

    if (isset($_POST["deleteServiceId"])) {
        $deleteServiceId = $_POST["deleteServiceId"];

        try {
            $deleteSql = "DELETE FROM services WHERE id = ?";

            $deleteStmt = $conn->prepare($deleteSql);

            $deleteStmt->bindParam(1, $deleteServiceId);

            $deleteStmt->execute();
        } catch (\Exception $e) {
            // No need for additional notifications here
        }
    }
}

$categories = fetchCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


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

  .admin-container {
        margin-bottom: 10px;
    }

    .container h2 {
        text-align: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }

  #service-form {
    max-width: 500px;
    height: 500px;
    margin: 100px auto 20px;
    padding: 20px;
    border: 2px solid #FFC451;
    border-radius: 10px;
}

    #service,
    #service-name,
    #image,
    #price {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
        border: 1px solid #FFC451;
        border-radius: 5px;
    }

    button[type="submit"] {
        background-color: green;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    button[type="submit"]:hover {
        color: black;
        background-color: #FFC451;
    }

    #service-table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        border: 2px solid #FFC451;
        text-align: center;
    }

    #service-table th,
    #service-table td {
        border: 1px solid #FFC451;
        padding: 10px;
        text-align: center;
    }

    #service-table th {
        background-color: #FFC451;
        color: white;
    }

    .edit-btn {
        background-color: #B5EAD7;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-right: 5px;
        width: 20%;
    }

    .edit-btn:hover {
        color: black;
        background-color: #B5EAD7;
    }

    .delete-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-right: 5px;
        width: 20%;
    }
    .delete-btn:hover {
        color: black;
        background-color: red;
    }
    .button-common {
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 10px; 
    width: 100%;
    }

    @media (min-width: 768px) {
    .button-common {
        width: 48%; 
        margin-right: 2%; }
    }
</style>

<body>

  <?php
    include 'config.php';
    include 'admin_header.php';
  ?>

  <main id="main">
    <div class="staff-container">
    <div class="admin-container">
        <form id="service-form" method="post" action="add_services.php" enctype="multipart/form-data">
            <h2>SERVICES</h2>

            <label for="service">Service Category:</label>
            <select id="service" name="category-name" required>
                <?php
                // Assuming $conn is your database connection object
                $result = $conn->query("SELECT * FROM category");
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['categoryName']}'>{$row['categoryName']}</option>";
                }
                ?>
            </select>

            <label for="service-name">Service Name:</label>
            <input type="text" id="service-name" name="service-name" required>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="service-image" accept="image/*">

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required>

            <button type="submit" name="addService" style="float: right; background-color: #4CAF50; color: #fff; border-radius: 4px; border: 1px solid #45a049;" onclick="return confirm('Are you sure you want to add this service?') && confirmSuccessMessage();">Save</button>
        </form>

        <br>

        <table id="service-table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Assuming $conn is your database connection object
                    $result = $conn->query("SELECT * FROM services");
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['serviceName']}</td>
                                <td><img src='{$row['Image']}' alt='{$row['serviceName']}' width='100'></td>
                                <td>{$row['Price']}</td>
                                <td>
                                    <button class='edit-btn' onmouseover=\"this.style.color='black'\" onmouseout=\"this.style.color='white'\">Edit</button>
                                    <form action='add_services.php' method='post' style='display: inline;'>
                                        <input type='hidden' name='deleteServiceId' value='{$row['id']}'>
                                        <button type='delete-btn' class='delete-btn' style='background-color: red; color: white;' onclick='return confirm(\"Are you sure you want to delete this service?\") && confirmDeleteMessage();'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                    ?>

            </tbody>
        </table>
    </div>
  </main><!-- End #main -->

  <script>

    function confirmDeleteMessage() {
        alert("Service deleted successfully");
        return true; // Continue with form submission
    }

    function confirmSuccessMessage() {
        alert("Service added successfully");
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