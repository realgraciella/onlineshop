

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
          justify-content: center; /* Center the forms horizontally */
          align-items: flex-start; /* Align the forms at the top */
          margin-top: 50px; /* Optional: Add some top margin */
      }

      .form-container {
          max-width: 400px;
          margin: 0 20px; /* Optional: Add some space on the sides */
          padding: 20px;
          flex: 1;
      }

      .form-title {
          text-align: center;
          font-size: 24px;
          margin-bottom: 20px;
      }

      @media (max-width: 768px) {
          .form-section {
              flex-direction: column;
              align-items: center; /* Center the forms vertically on smaller screens */
          }
      }

      .btn {
          display: inline-block;
          padding: 6px 12px;
          background-color: #333;
          color: #fff;
          text-decoration: none;
          border-radius: 4px;
          transition: background-color 0.3s ease;
        }

        .btn:hover {
          background-color: #555;
          color: #ffc451;
        }

        .submit-btn-container {
            text-align: right;
            margin-top: 20px;
        }
  </style>
</head>

<body>

  <?php
    include 'cust_header.php';
  ?>

  

  <main id="main">

    <!-- ======= Breadcrumbs ======= -->
    <section class="breadcrumbs">
      <div class="container">

        <div class="d-flex justify-content-between align-items-center">
          <h2>RECEIPT</h2>
        </div>

      </div>
    </section><!-- End Breadcrumbs -->

    <section class="form-section">
      <div class="form-container">
                    <div class="title">Receipt</div>
                    <form method="post" action="" id="filterForm">
                        <div class="form-group">
                            <label for="service-list">List of Services:</label>
                            <textarea id="service-list" name="service-list" placeholder="Enter services" rows="4" class="service-list"></textarea>
                        </div>
                        <div class="service-total">
                            Total Price: <span id="total-price">$0</span>
                            <input type="hidden" id="total-price-input" name="service-total" value="0">
                        </div>
                    </form>
                </div>

        <div class="form-container">
            <h2 class="form-title">Payment Form</h2>
            <form id="payment-form">
                <div class="form-control">
                    <label for="payment-type">Payment Type:</label>
                    <select id="payment-type">
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>
                <div id="cash-form" class="form-control">
                    <label for="total-amount">Total Amount of Services:</label>
                    <input type="text" id="total-amount" name="total-amount" required>
                </div>
                <div id="change-form" class="form-control">
                    <label for="amount-paid">Amount Paid:</label>
                    <input type="text" id="amount-paid" name="amount-paid" required>
                </div>
                <div id="account-number-form" class="form-control">
                    <label for="account-number">Account Number:</label>
                    <input type="text" id="account-number" name="account-number" required>
                </div>
                <div class="form-control gcash-controls">
                    <label for="gcash-amount-paid">Amount Paid:</label>
                    <input type="text" id="gcash-amount-paid" name="gcash-amount-paid" required>
                </div>
                <div class="form-control submit-btn-container">
                  <input type="submit" value="Submit" class="btn">
                </div>
            </form>
        </div>
    </section>
  </main><!-- End #main -->

  <script>
    const receiptForm = document.getElementById("receipt-form");
    const paymentForm = document.getElementById("payment-form");
    const paymentTypeSelect = document.getElementById("payment-type");
    const cashForm = document.getElementById("cash-form");
    const changeForm = document.getElementById("change-form");
    const accountNumberForm = document.getElementById("account-number-form");
    const gcashControls = document.querySelector('.gcash-controls');

    paymentTypeSelect.addEventListener("change", () => {
        if (paymentTypeSelect.value === "cash") {
            cashForm.style.display = "block";
            changeForm.style.display = "block";
            gcashControls.style.display = "none";
            accountNumberForm.style.display = "none";
        } else if (paymentTypeSelect.value === "gcash") {
            cashForm.style.display = "none";
            changeForm.style.display = "none";
            gcashControls.style.display = "block";
            accountNumberForm.style.display = "block";
        }
    });

    receiptForm.addEventListener("submit", (e) => {
        e.preventDefault();
        // Add code here to handle receipt form submission
    });

    paymentForm.addEventListener("submit", (e) => {
        e.preventDefault();
        // Add code here to handle payment form submission
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