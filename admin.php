<?php
    include 'config.php';

    // Count appointments with pending status
    $sqlPendingAppointments = "SELECT COUNT(*) as pendingAppointments FROM appointments WHERE status = 'pending'";
    $stmtPendingAppointments = $conn->prepare($sqlPendingAppointments);
    $stmtPendingAppointments->execute();
    $resultPendingAppointments = $stmtPendingAppointments->fetch(PDO::FETCH_ASSOC);
    $pendingAppointmentsCount = $resultPendingAppointments['pendingAppointments'];

    // Count total number of users
    $sqlTotalUsers = "SELECT COUNT(*) as totalUsers FROM users";
    $stmtTotalUsers = $conn->prepare($sqlTotalUsers);
    $stmtTotalUsers->execute();
    $resultTotalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC);
    $totalUsersCount = $resultTotalUsers['totalUsers'];

    // Count total number of services
    $sqlTotalServices = "SELECT COUNT(*) as totalServices FROM services";
    $stmtTotalServices = $conn->prepare($sqlTotalServices);
    $stmtTotalServices->execute();
    $resultTotalServices = $stmtTotalServices->fetch(PDO::FETCH_ASSOC);
    $totalServicesCount = $resultTotalServices['totalServices'];

    // Count total number of staff members
    $sqlTotalStaff = "SELECT COUNT(*) as totalStaff FROM staff_reg";
    $stmtTotalStaff = $conn->prepare($sqlTotalStaff);
    $stmtTotalStaff->execute();
    $resultTotalStaff = $stmtTotalStaff->fetch(PDO::FETCH_ASSOC);
    $totalStaffCount = $resultTotalStaff['totalStaff'];

    // Count total number of feedbacks
    $sqlTotalFeedbacks = "SELECT COUNT(*) as totalFeedbacks FROM feedbacks";
    $stmtTotalFeedbacks = $conn->prepare($sqlTotalFeedbacks);
    $stmtTotalFeedbacks->execute();
    $resultTotalFeedbacks = $stmtTotalFeedbacks->fetch(PDO::FETCH_ASSOC);
    $totalFeedbacksCount = $resultTotalFeedbacks['totalFeedbacks'];

    // Count total number of inquiries
    $sqlTotalInquiries = "SELECT COUNT(*) as totalInquiries FROM inquiries";
    $stmtTotalInquiries = $conn->prepare($sqlTotalInquiries);
    $stmtTotalInquiries->execute();
    $resultTotalInquiries = $stmtTotalInquiries->fetch(PDO::FETCH_ASSOC);
    $totalInquiriesCount = $resultTotalInquiries['totalInquiries'];
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
  #dashboard {
    text-align: center;
    padding: 20px;
    margin-top: 50px;
  }

  .grid-container {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
  }

  .grid-item {
      border-radius: 10px;
      padding: 20px;
      text-align: left;
  }

  .appointments {
      background-color: #E8A4D9;
  }

  .customers {
      background-color: #F6D6AD;
  }

  .services {
      background-color: #C7CEEA;
  }

  .staff {
      background-color: #FFD3B6;
  }

  .feedbacks {
    background-color: #B5EAD7;
  }

  .inquiries {
      background-color: #F3B6D7;
  }

  button {
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
  }
</style>

<body>

  <?php
    include 'admin_header.php';
  ?>

  <main id="main">

    <section id="dashboard">
    <h2 style="text-align: center; margin-top: 20px; margin-bottom: 30px;">DASHBOARD</h2>
    <div class="grid-container">
        
        <div class="grid-item appointments">
            <h3>Appointments</h3>
            <p><?php echo $pendingAppointmentsCount; ?> upcoming appointments</p>
            <a href="adminAppointments.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

        <div class="grid-item customers">
            <h3>Customers</h3>
            <p><?php echo $totalUsersCount; ?> total customers</p>
            <a href="admin_view_cust.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

        <div class="grid-item services">
            <h3>Services</h3>
            <p><?php echo $totalServicesCount; ?> available services</p>
            <a href="add_services.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

        <div class="grid-item staff">
            <h3>Staff</h3>
            <p><?php echo $totalStaffCount; ?> staff members</p>
            <a href="admin_view_staff.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

        <div class="grid-item feedbacks">
            <h3>Feedbacks</h3>
            <p><?php echo $totalFeedbacksCount; ?> feedbacks received</p>
            <a href="admin_view_feedbacks.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

        <div class="grid-item inquiries">
            <h3>Inquiries</h3>
            <p><?php echo $totalInquiriesCount; ?> inquiries</p>
            <a href="admin_view_inquiries.php" style="float: right;">
              <button style="background-color: #333333; color: #fff; border-radius: 4px; border: 1px solid #fff;">View</button>
            </a>
        </div>

    </div>
</section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
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
  </footer>End Footer

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

</body>

</html>