<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["feedback"])) {
        // Process feedback form
        $feedback = $_POST["feedback"];

        // Perform validation and sanitation if necessary

        // Insert data into the 'feedbacks' table
        $sql = "INSERT INTO feedbacks (feedback) VALUES (:feedback)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':feedback', $feedback);

        try {
            $stmt->execute();
            echo "Feedback submitted successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["inquiry"])) {
        // Process inquiry form
        $inquiry = $_POST["inquiry"];

        // Perform validation and sanitation if necessary

        // Insert data into the 'inquiries' table
        $sql = "INSERT INTO inquiries (inquiry) VALUES (:inquiry)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':inquiry', $inquiry);

        try {
            $stmt->execute();
            echo "Inquiry submitted successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
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

  <!-- Template Main CSS File -->
  <link href="assets/css/customer.css" rel="stylesheet">

  <style>
      .form-section {
          display: flex;
          justify-content: space-around;
        }

        .form-container {
          background-color: #FFF; /* Set your desired background color */
          border: 2px solid #FFC451;
          border-radius: 10px;
          padding: 20px;
          width: 45%; /* Adjust as needed to leave some space between forms */
          box-sizing: border-box;
        }

        form {
          max-width: 100%;
        }

        h2 {
          text-align: center;
        }

        label {
          display: block;
          margin-bottom: 8px;
        }

        textarea {
          width: 100%;
          padding: 10px;
          margin-bottom: 16px;
          box-sizing: border-box;
        }

        .button-wrapper {
          display: flex;
          justify-content: space-around;
        }

        .btn {
          background-color: #FFC451;
          color: #fff;
          padding: 10px 20px;
          text-decoration: none;
          border-radius: 5px;
          cursor: pointer;
        }

        .btn:hover {
          background-color: #e0a800;
        }
  </style>
</head>

<body>

  <?php
    include 'cust_header.php';
  ?>

  <main id="main">

    <h2 style="text-align: center; margin-top: 150px; margin-bottom: 10px;">FEEDBACKS AND INQUIRIES</h2>


    <section class="form-section">
        <div class="form-container">
            <form action="custMessages.php" method="post">
                <h2>Feedback Form</h2>
                <label for="feedback">Your Feedback:</label>
                <textarea id="feedback" name="feedback" placeholder="Enter your feedback here..."></textarea>
                <br>
                <br>
                <div class="button-wrapper">
                    <br>
                    <br>
                    <button type="submit" class="btn">Send Feedback</button>
                    <button type="button" class="btn" onclick="viewFeedback()">View Feedback</button>
                </div>
            </form>
        </div>


        <div class="form-container">
            <form action="custMessages.php" method="post">
                <h2>Inquiry Form</h2>
                <label for="inquiry">Your Inquiry:</label>
                <textarea id="inquiry" name="inquiry" placeholder="Enter your inquiry here..."></textarea>
                <br>
                <br>
                <div class="button-wrapper">
                    <br>
                    <br>
                    <button type="submit" class="btn">Send Inquiry</button>
                    <button type="button" class="btn" onclick="viewInquiry()">View Inquiry</button>
                </div>
            </form>
        </div>

    </section>

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