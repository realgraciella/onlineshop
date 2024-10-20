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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Template Main CSS File -->
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
    /* styles.css */

body {
    margin: 0;
}

header {
    background-color: #333;
    color: white;
    padding: 10px;
    text-align: center;
}

form {
    width: 100%;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border: 2px solid #FFC451;
    border-radius: 10px;
    box-sizing: border-box;
    margin-top: 80px;
}

.customer-number {
    color: #FFC451;
}

.customer-name a {
    color: black;
    text-decoration: none; /* Remove underlines from links */
}

.customer-name a:hover {
    color: #FFC451;
    /* Add any additional hover styles as needed */
}

  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  

  <main id="main">
    <form>
        <h2 style="text-align: center; margin-top: 10px; margin-bottom: 50px;">CUSTOMER LIST</h2>
        <?php
        include('config.php'); // Include your database connection file

        $sql = "SELECT first_name, last_name FROM users";
        $result = $conn->query($sql);

        if ($result) {
            $count = 1;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $fname = $row["first_name"];
                $lname = $row["last_name"];
                echo "<p><span class='customer-number'>$count.</span> <span class='customer-name'><a href='customer_details.php?fname=" . urlencode($fname) . "&lname=" . urlencode($lname) . "'>$fname $lname</a></span></p>";
                $count++;
            }
        } else {
            echo "Error fetching customer data: " . $conn->errorInfo()[2];
        }

        $conn = null; // Close the PDO connection
        ?>
    </form>
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
  <script src="assets/js/customer.js"></script>

</body>

</html>