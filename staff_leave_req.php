<?php
// Your existing database connection code
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];

    // Prepare and execute the SQL query to insert data into the table
    $sql = "INSERT INTO staff_leave_req (date, time, reason) VALUES (:date, :time, :reason)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':time', $time);
    $stmt->bindParam(':reason', $reason);

    // Execute the query
    $stmt->execute();

    // Close the database connection
    $conn = null;

    // Redirect back to the form or any other page
    header('Location: staff_leave_req.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Staff Page</title>
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
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
    }

    section {
      margin: 40px 0;
    }

    form {
      border: 2px solid #FFC451;
      padding: 20px;
      border-radius: 10px;
      background-color: #fff;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #333;
    }

    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="submit"] {
      background-color: #FFC451;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #1e87cc;
    }

    h1, h2 {
      color: #333;
    }
  </style>
</head>

<body>

  <?php
    include 'staff_header.php';
  ?>

  

  <main id="main">

    <section>
        <div class="container">
            <h1>Leave Request Form</h1>
            <form action="staff_leave_req.php" method="post">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required><br><br>

                <label for="time">Time:</label>
                <select id="time" name="time" required>
                    <option value="" disabled selected>Select</option>
                    <option value="7:00 am - 12:00 pm">7:00 am - 12:00 pm</option>
                    <option value="1:00 pm - 6:00 pm">1:00 pm - 6:00 pm</option>
                    <option value="FULL DAY">Full Day</option>
                </select><br><br>

                <label for="reason">Reason:</label>
                <textarea id="reason" name="reason" required></textarea><br><br>

                <input type="submit" value="Apply">
            </form>
    </section>


  </main><!-- End #main -->

  <script>
  </script>

<footer id="footer">
    <div class="footer-top">
      <div class="foot-container">
        <div class="row"> 

          <div class="col-lg-3 col-md-6">
            <div class="footer-info ">
              <h3>Gp<span>.</span></h3>
              <p>
                Barangay 3 <br>
                Nasugbu, Batangas<br><br>
                <strong>Phone:</strong> 0997-199-4671<br>
                <strong>Email:</strong> Emilyabut@gmail.com<br>
              </p>
              <div class="social-links mt-3">
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Hair Treatment</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Body Spa Treatments</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Bridal Treatments</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Nail Treatments</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">EyelashTreatments</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Eyebrow Treatments</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Lip Treatments</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Face Treatments</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Email Us!</h4>
            <p>For inquiries contact us via email.</p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>

          </div>

        </div>
      </div>
    </div>
  </footer>

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