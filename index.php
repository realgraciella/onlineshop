<?php
    include 'database/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>DM Boutique</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo/3.png" rel="icon">
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
  <link href="assets/css/index.css" rel="stylesheet">

  <style>
    .custom-btn {
      border-color: #008a00;
      color: #008a00;
      font-family: 'Poppins', sans-serif; /* You can change this to any font you prefer */
      font-size: 16px; /* Adjust font size as needed */
      padding: 10px 20px; /* Adjust padding as needed */
      transition: all 0.3s ease-in-out;
      }

      .custom-btn:hover {
       background-color: #008a00;
       color: #fff;
       }
   </style>
</head>

<body>

  <?php
    include 'index_header.php';
  ?>

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center justify-content-center">
  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">

        <div class="row">
          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-delay="100">
            <img src="assets/img/logo/dm.png" class="img-fluid" alt="">
          </div>
          <div class="col-lg-6 pt-4 pt-lg-0 order-2 order-lg-1 content" data-aos="fade-right" data-aos-delay="100">
            <h3>ABOUT</h3>
            <p style="text-align: justify;">
              The business has been thriving for more than 30 years. They still implement manual filing, analyzing,
              recording, computing, and storing business data. They have sales agents which are also known as dealers who are the main customers of the boutique.
            </p>  
            <p style="text-align: justify;">
              Despite competition, Dho and Myrna’s boutique thrives. Their success comes from selling well-known 
              products and building strong connections with customers. By consistently offering quality, they’ve 
              built lasting loyalty and set industry standards, making them a standout in fashion retail.
            </p>

          </div>
        </div>

      </div>
    </section><!-- End About Section -->

    <!-- ======= Portfolio Section ======= -->
    <section id="portfolio" class="portfolio">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Products</h2>
          <p>Check our Products</p>
        </div>

        <!-- Portfolio Filters -->
        <div class="row" data-aos="fade-up" data-aos-delay="100">
          <div class="col-lg-12 d-flex justify-content-center">
            <ul id="portfolio-flters">
              <li data-filter="*" class="filter-active">All</li>

              <?php
              // Fetch distinct brands for filters using PDO
              $query = "SELECT DISTINCT brand_name FROM brands";
              $stmt = $pdo->prepare($query);
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<li data-filter=".filter-' . strtolower($row['brand_name']) . '">' . strtoupper($row['brand_name']) . '</li>';
              }
              ?>
            </ul>
          </div>
        </div>

        <!-- Portfolio Items -->
        <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">

          <?php
          // Query to fetch products with joined brands
          $query = "SELECT p.*, b.brand_name FROM products p 
                    JOIN brands b ON p.brand_id = b.brand_id
                    LIMIT 10";
          $stmt = $pdo->prepare($query);
          $stmt->execute();

          // Loop through products and display them
          while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $brandClass = strtolower($product['brand_name']);
            echo '
            <div class="col-lg-4 col-md-6 portfolio-item filter-' . $brandClass . '">
              <div class="portfolio-wrap">
                <img src="' . $product['product_image_url'] . '" class="img-fluid" alt="Product Image">
                <div class="portfolio-info">
                  <h4>' . $product['brand_name'] . '</h4>
                  <p>' . $product['product_desc'] . '</p>
                  <p><strong>Price: $' . $product['price'] . '</strong></p>
                  <div class="btn-group mt-3">
                    <button class="btn btn-primary add-to-cart" data-product-id="' . $product['product_id'] . '">Add to Cart</button>
                    <button class="btn btn-success buy-now" data-product-id="' . $product['product_id'] . '">Buy Now</button>
                  </div>
                </div>
              </div>
            </div>';
          }

          // Close the PDO connection
          $pdo = null;
          ?>

        </div>

         <!-- See More Products Button -->
         <div class="text-center mt-5">
          <a href="login.php" class="btn btn-outline-primary custom-btn">See More Products</a>
        </div>

      </div>
    </section><!-- End Portfolio Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Contact</h2>
          <p>Contact Us</p>
        </div>

        <div class="google-map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3870.0867779997902!2d120.62893217612292!3d14.072052786354144!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd976a9791332d%3A0xb37e1b6e3c1d6557!2sDho%20and%20Myrna%20Fashion%20Boutique!5e0!3m2!1sen!2sph!4v1729481040534!5m2!1sen!2sph" 
                    width="100%" height="300" 
                    style="border:0;" allowfullscreen="" 
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->

  <?php
    include 'footer.php';
  ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>
