<?php
// Include your database connection configuration
include 'config.php';

// Fetch data from the 'staff_reg' table
$sql = "SELECT * FROM staff_reg";
$stmt = $conn->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
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
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
    form {
      display: flex;
    }

    section {
      width: 45%;
      margin: 20px;
    }

    .staff-container {
      border: 2px solid #FFC451;
      border-radius: 10px;
      padding: 20px;
    }

    .profile-picture {
      text-align: center;
    }

    .profile-picture img {
      border-radius: 50%;
      border: 4px solid #FFC451;
      max-width: 150px;
    }

    .profile-info {
      display: flex;
      margin-bottom: 10px;
    }

    label {
      flex: 1;
      font-weight: bold;
    }

    span {
      flex: 2;
    }

    .edit-button {
      display: inline-block;
      background-color: #FFC451;
      color: #fff;
      padding: 8px 15px;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 10px;
    }

    .edit-button:hover {
      background-color: #e0a800;
    }
  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  

  <main id="main">
    <h2 style="text-align: center; margin-top: 90px; margin-bottom: 0px;">PROFILE DETAILS</h2>
    <form action="#" method="post">
      <section>
        <div class="staff-container">
          <div class="profile-picture">
            <label for="image">Image:</label>
            <img src="<?= $row['pic']; ?>" alt="Profile Image">
          </div>

          <div class="profile-info">
            <label for="firstName">First Name:</label>
            <span id="firstName"><?= $row['fname']; ?></span>
          </div>

          <div class="profile-info">
            <label for="lastName">Last Name:</label>
            <span id="lastName"><?= $row['lname']; ?></span>
          </div>

          <div class="profile-info">
            <label for="pronoun">Preferred Pronoun:</label>
            <span id="pronoun"><?= $row['pNoun']; ?></span>
          </div>

          <div class="profile-info">
            <label for="sex">Sex:</label>
            <span id="sex"><?= $row['sex']; ?></span>
          </div>

          <div class="profile-info">
            <label for="birthday">Birthday:</label>
            <span id="birthday"><?= $row['bday']; ?></span>
          </div>

          <div class="profile-info">
            <label for="address">Address:</label>
            <span id="address"><?= $row['address']; ?></span>
          </div>
        </div>
      </section>

      <section>
        <div class="staff-container">
          <div class="profile-info">
            <label for="mobileNumber">Mobile Number:</label>
            <span id="mobileNumber"><?= $row['mnum']; ?></span>
          </div>
          <div class="profile-info">
            <label for="email">Email Address:</label>
            <span id="email"><?= $row['email']; ?></span>
          </div>

          <div class="profile-info">
            <label for="education">Educational Background (Degree):</label>
            <span id="education"><?= $row['edbg']; ?></span>
          </div>

          <div class="profile-info">
            <label for="specialization">Specialization:</label>
            <span id="specialization"><?= $row['special']; ?></span>
          </div>

          <div class="profile-info">
            <label for="experience">Previous Work Experience:</label>
            <span id="experience"><?= $row['exp']; ?></span>
          </div>

          <div>
            <a href="#" class="edit-button">Edit</a>
          </div>
        </div>
      </section>
    </form>

  </main><!-- End #main -->

  <script>
    
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