<?php
// Include your database connection configuration
include 'config.php';

// Fetch data from the 'appointments' table
$sql = "SELECT customerName, service, totalPrice, appointmentDate, appointmentTime, status FROM appointments";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
       section {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
        }

        table {
            border-collapse: collapse;
            width: 80%; /* Adjusted width */
            margin: 20px auto; /* Centered the table with some top and bottom margin */
            border: 2px solid #FFC451;
            text-align: center;
        }

        table th,
        table td {
            border: 1px solid #FFC451;
            padding: 10px;
        }

        table th {
            background-color: #FFC451;
            color: white;
        }
  </style>
</head>

<body>

  <?php
    include 'staff_header.php';
  ?>

   <section>
        <h2>APPOINTMENTS</h2>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Service</th>
                    <th>Total Price</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo $row['customerName']; ?></td>
                        <td><?php echo $row['service']; ?></td>
                        <td><?php echo $row['totalPrice']; ?></td>
                        <td><?php echo $row['appointmentDate']; ?></td>
                        <td><?php echo $row['appointmentTime']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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