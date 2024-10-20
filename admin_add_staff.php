<?php
// Include your database connection configuration
include 'config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $pNoun = $_POST['pronoun'];
    $sex = $_POST['sex'];
    $bday = $_POST['birthday'];
    $address = $_POST['address'];
    $mnum = $_POST['mobileNumber'];
    $email = $_POST['email'];
    $edbg = $_POST['education'];
    $special = $_POST['specialization'];
    $exp = $_POST['experience'];
    $pass = $_POST['password'];
    $conpass = $_POST['confirmPassword'];

    // File upload handling
    $imageDir = 'uploads/'; // Create a directory named 'uploads' in your project folder
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = $imageDir . $imageName;

    // Move the uploaded file to the destination directory
    move_uploaded_file($imageTmp, $imagePath);

    // You may want to add additional validation and sanitization here

    // Insert data into the database
    $sql = "INSERT INTO staff_reg (fname, lname, pNoun, sex, bday, address, mnum, email, edbg, special, exp, pass, conpass, pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$fname, $lname, $pNoun, $sex, $bday, $address, $mnum, $email, $edbg, $special, $exp, $pass, $conpass, $imagePath]);
        echo 'success';
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }
}
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Template Main CSS File -->
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
    .registration-form {
      background-color: #fff;
      border: 2px solid #FFC451;
      border-radius: 10px;
      padding: 20px;
      margin: 40px auto;
      max-width: 800px;
    }

    .form-row {
      display: flex;
      justify-content: space-between;
    }

    .form-column {
      flex: 1;
      margin-right: 10px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #333;
    }

    input,
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
      background-color: green;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: green;
      color: #000;
    }
  </style>
</head>

<body>

  <?php
    include 'admin_header.php';
  ?>

  

  <main id="main">
        <h2 style="text-align: center; margin-top: 90px; margin-bottom: 10px;">STAFF REGISTRATION</h2>

    <section class="registration-form">
      <div class="container">
        <form method="post" action="" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-column">
              <label for="firstName">First Name:</label>
              <input type="text" id="firstName" name="firstName" required>

              <label for="lastName">Last Name:</label>
              <input type="text" id="lastName" name="lastName" required>

              <label for="pronoun">Preferred Pronoun:</label>
              <select id="pronoun" name="pronoun" required>
                <option value="he">Select</option>
                <option value="he">He</option>
                <option value="she">She</option>
              </select>

              <label for="sex">Sex:</label>
              <select id="sex" name="sex" required>
                <option value="he">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>

              <label for="birthday">Birthday:</label>
              <input type="date" id="birthday" name="birthday" required>

              <label for="address">Address:</label>
              <textarea id="address" name="address" required></textarea>

              <label for="mobileNumber">Mobile Number:</label>
              <input type="tel" id="mobileNumber" name="mobileNumber" required>
            </div>

            <div class="form-column">
              <label for="email">Email Address:</label>
              <input type="email" id="email" name="email" required>

              <label for="education">Educational Background (Degree):</label>
              <input type="text" id="education" name="education" required>

              <label for="specialization">Specialization:</label>
              <select id="specialization" name="specialization" required>
                <option value="he">Select</option>
                <option value="hairStyle">Hair Style</option>
                <option value="makeup">Makeup</option>
              </select>

              <label for="experience">Previous Work Experience:</label>
              <textarea id="experience" name="experience" required></textarea>

              <label for="password">Password:</label>
              <input type="password" id="password" name="password" required>

              <label for="confirmPassword">Confirm Password:</label>
              <input type="password" id="confirmPassword" name="confirmPassword" required>

              <label for="image">Image:</label>
              <input type="file" id="image" name="image" accept="image/*" required>
            </div>
          </div>

          <div class="form-row">
            <input type="submit" value="Save">
          </div>
        </form>
      </div>
    </section>

  </main><!-- End #main -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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