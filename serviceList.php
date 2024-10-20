<?php
include('config.php');

// Fetch the last cust_id from the 'try' table
try {
    $getLastCustIdQuery = "SELECT cust_id FROM logins ORDER BY cust_id DESC LIMIT 1";
    $lastCustIdStmt = $conn->query($getLastCustIdQuery);
    $lastCustId = $lastCustIdStmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error fetching last cust_id: " . $e->getMessage();
}

// Fetch categories directly from the database
$categories = array();
$result = $conn->query("SELECT * FROM category");

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}

// Check if a category is selected
$whereClause = '';
if (isset($_POST['selected-category']) && !empty($_POST['selected-category'])) {
    $selectedCategory = $_POST['selected-category'];
    $whereClause = "WHERE categoryName = :categoryName";
}

// Fetch services based on the selected category
$query = "SELECT * FROM services $whereClause";
$stmt = $conn->prepare($query);

if (!empty($whereClause)) {
    $stmt->bindParam(':categoryName', $selectedCategory, PDO::PARAM_STR);
}

$stmt->execute();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    // Handle the booking form submission
    $customerName = $_POST['name'];
    $serviceList = $_POST['service-list'];
    $totalPrice = $_POST['service-total'];
    $appointmentDate = $_POST['app-date'];
    $appointmentTime = $_POST['app-time'];
    $prefferredStaff = $_POST['staff'];

    try {
        // Insert data into the 'appointments' table with the last cust_id
        $stmt = $conn->prepare("INSERT INTO appointments (cust_id, customerName, service, totalPrice, appointmentDate, appointmentTime, prefferredStaff) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$lastCustId, $customerName, $serviceList, $totalPrice, $appointmentDate, $appointmentTime, $prefferredStaff]);

        // Redirect to customerAppointment.php after successful booking
        header("Location: customerAppointment.php");
        exit; // Ensure that no further code is executed after the redirection
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Client Page</title>
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
  <link rel="stylesheet" type="text/css" href="bootstrap-3/css/bootstrap.min.css">

  <!-- Template Main CSS File -->
  <link href="assets/css/cust.css" rel="stylesheet">

</head>

<body>

  <?php
    include 'cust_header.php';
  ?>
  

  <main id="main">

    <section class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <form method="post" id="filterForm" class="my-3">
                        <div class="form-group">
                            <label for="category">Select Category:</label>
                            <div class="d-flex">
                                <select id="category" name="selected-category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php
                                    foreach ($categories as $category) {
                                        echo "<option value='{$category['categoryName']}'>{$category['categoryName']}</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" class="btn btn-success ml-6">Filter</button>
                            </div>
                        </div>
                    </form>

                    <table id="serviceTable" class="table">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2>SERVICES LIST</h2>
                        </div>
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
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $serviceName = isset($row['serviceName']) ? $row['serviceName'] : 'N/A';
                                $image = isset($row['Image']) ? $row['Image'] : 'N/A';
                                $price = isset($row['Price']) ? $row['Price'] : 'N/A';

                                echo "<tr>
                                        <td>{$serviceName}</td>
                                        <td><img src='{$image}' alt='{$serviceName}' width='100'></td>
                                        <td>{$price}</td>
                                        <td><button type='button' class='add-service btn btn-success' data-name='{$serviceName}' data-image='{$image}' data-price='{$price}'>Add</button></td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <div class="form-container">
                        <div class="title">Service Booking Form</div>
                        <form method="post" action="" id="filterForm" class="my-3">
                            <div class="form-group">
                                <label for="service-list">List of Services:</label>
                                <textarea id="service-list" name="service-list" placeholder="Enter services" rows="4" class="form-control service-list"></textarea>
                            </div>
                            <div class="service-total">
                                Total Price: <span id="total-price">Php 0</span>
                                <input type="hidden" id="total-price-input" name="service-total" value="0">
                            </div>
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" placeholder="Enter your name" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="app-date">Appointment Date:</label>
                                <input type="date" id="app-date" name="app-date" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="app-time">Appointment Time:</label>
                                <input type="time" id="app-time" name="app-time" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="staff">Staff Preferred:</label>
                                <select id="staff" name="staff" required class="form-control">
                                    <option value="">Select Staff</option>
                                    <option value="John">John</option>
                                    <option value="Sarah">Sarah</option>
                                    <option value="David">David</option>
                                </select>
                            </div>
                            <button class="book-now-btn btn btn-success" type="submit">Book Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- End Breadcrumbs -->


  </main><!-- End #main -->

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        var addButtons = document.querySelectorAll('.add-service');
        var serviceListTextArea = document.getElementById('service-list');
        var totalPriceSpan = document.getElementById('total-price');

        var state = {
            total: 0,
            addedServices: [],
        };

        function updateTotal() {
            totalPriceSpan.textContent = 'Php ' + state.total.toFixed(2);

            // Set the total price in a hidden input field
            document.getElementById('total-price-input').value = state.total.toFixed(2);
        }

        addButtons.forEach(function (button) {
            button.addEventListener('mousedown', function (event) {
                event.preventDefault(); // Prevent possible double-click issues

                var serviceName = this.getAttribute('data-name');
                var price = parseFloat(this.getAttribute('data-price'));

                // Check if service is already added
                if (!state.addedServices.includes(serviceName)) {
                    // Append service information to the textarea
                    serviceListTextArea.value += serviceName + " - Php" + price.toFixed(2) + "\n";

                    // Update total price and state
                    state.total += price;
                    state.addedServices.push(serviceName);

                    // Update total display
                    updateTotal();
                }
            });
        });
    });
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
  <script src="assets/js/customer.js"></script>

</body>

</html>