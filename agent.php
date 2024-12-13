
<?php
    include 'database/db_connect.php';

    $query = "SELECT products.*, categories.category_name FROM products JOIN categories ON products.category_id = categories.category_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/agent.css" rel="stylesheet">

  <style>
    .custom-btn {
      border-color: #008a00;
      color: #008a00;
      font-family: 'Poppins', sans-serif; 
      font-size: 16px; 
      padding: 10px 20px;
      transition: all 0.3s ease-in-out;
    }

    .custom-btn:hover {
      background-color: #008a00;
      color: #fff;
    }
  </style>
</head>

<body>

  <?php include 'agent_header.php'; ?>

  <!-- Hero Section -->
  <section id="hero" class="d-flex align-items-center justify-content-center"></section>

  <main id="main">
    <!-- About Section -->
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">
        <div class="row">
          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-delay="100">
            <img src="assets/img/logo/dm.png" class="img-fluid" alt="Logo">
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
    </section>

    <!-- Portfolio Section -->
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
              $query = "SELECT brand_id, brand_name FROM brands";
              $stmt = $pdo->prepare($query);
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<li data-filter=".filter-' . $row['brand_id'] . '">' . htmlspecialchars($row['brand_name']) . '</li>';
              }
              ?>
            </ul>
          </div>
        </div>

        <!-- Portfolio Items -->
        <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
          <?php
          $query = "SELECT p.*, b.brand_id, b.brand_name FROM products p 
                    JOIN brands b ON p.brand_id = b.brand_id
                    LIMIT 10";
          $stmt = $pdo->prepare($query);
          $stmt->execute();

          while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $brandClass = 'filter-' . $product['brand_id'];
            $imagePath = 'uploads/products/' . $product['product_image_url'];

            if (!file_exists($imagePath)) {
              $imagePath = 'assets/img/default-image.png';
            }

            echo '
            <div class="col-lg-4 col-md-6 portfolio-item ' . $brandClass . '">
              <div class="portfolio-wrap">
                <img src="' . $imagePath . '" class="img-fluid" alt="Product Image">
                <div class="portfolio-info">
                  <h4>' . htmlspecialchars($product['brand_name']) . '</h4>
                  <p>' . htmlspecialchars($product['product_desc']) . '</p>
                  <p><strong>Price: PHP ' . number_format(htmlspecialchars($product['price']), 2) . '</strong></p>
                  <div class="btn-group mt-3">
                    <button class="btn btn-primary add-to-cart" data-product-id="' . $product['product_id'] . '">Add to Cart</button>
                    <button class="btn btn-success buy-now" data-product-id="' . $product['product_id'] . '">Buy Now</button>
                  </div>
                </div>
              </div>
            </div>';
          }

          $pdo = null;
          ?>
        </div>

        <div class="text-center mt-5">
          <a href="agent_products.php" class="btn btn-outline-primary custom-btn">See More Products</a>
        </div>

      </div>
    </section>

    <!-- Contact Section -->
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
    </section>

  </main>

  <?php include 'footer.php'; ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var portfolioContainer = document.querySelector('.portfolio-container');
      if (portfolioContainer) {
        var iso = new Isotope(portfolioContainer, {
          itemSelector: '.portfolio-item',
          layoutMode: 'fitRows'
        });

        var filters = document.querySelectorAll('#portfolio-flters li');
        filters.forEach(function (filter) {
          filter.addEventListener('click', function () {
            filters.forEach(function (f) { f.classList.remove('filter-active'); });
            this.classList.add('filter-active');
            var filterValue = this.getAttribute('data-filter');
            iso.arrange({ filter: filterValue });
          });
        });
      }
    });
  </script>

</body>
</html>