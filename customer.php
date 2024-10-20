<?php
include 'config.php'; // Include the database connection settings

// Fetch categories from the 'category' table
$sql = "SELECT * FROM category";
$stmt = $conn->query($sql);

// Check if the query was successful
if (!$stmt) {
    die("Query failed: " . $conn->errorInfo()[2]);
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
</head>

<body>

  <?php
    include 'cust_header.php';
  ?>



  <main id="main">



    <!-- ======= Portfolio Section ======= -->
    <section id="portfolio" class="portfolio">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2 style="text-align: left; margin-top: 40px; margin-bottom: 30px;">CHECK OUR SERVICES</h2>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="100">
          <div class="col-lg-12 d-flex justify-content-center">
            <ul id="portfolio-flters">
              <li data-filter="*" class="filter-active">All</li>
              <li data-filter=".filter-hair">Hair Treatment</li>
              <li data-filter=".filter-body">Body Spa Treatment</li>
              <li data-filter=".filter-bridal">Bridal Treatment</li>
              <li data-filter=".filter-facial">Facial Treatment</li>
              <li data-filter=".filter-eyelash">Eyelash Treatment</li>
            </ul>
          </div>
        </div>

        <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">

          <div class="col-lg-4 col-md-6 portfolio-item filter-hair">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/rebond.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Hair Rebonding</h4>
                <p>Hair Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/rebond.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 1"><i class="bx bx-plus"></i></a>
                  <a href="cust_service_details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-body">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/massage.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Body Massage</h4>
                <p>Body Spa Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/massage.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 3"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-bridal">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/bridal1.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Hairstyling</h4>
                <p>Bridal Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/bridal1.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 2"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-facial">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/facemas.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Face Massage</h4>
                <p>Facial Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/facemas.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 2"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-eyelash">
            <div class="portfolio-wrap">
              <img src="assets/img//treatment/eyetreat.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Repair Eyelash Extension</h4>
                <p>Eyelash Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/eyetreat.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 2"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-hair">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/curl.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Hair Curl</h4>
                <p>Hair Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/curl.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="App 3"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-body">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/footspa.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Foot Spa</h4>
                <p>Body spa Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/footspa.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 1"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-bridal">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/makeup.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Make-up</h4>
                <p>Bridal Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/makeup.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Card 3"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-facial">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/hydraface.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Anti-acne and Acne scar removal with Hydra Facial</h4>
                <p>Facial Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/hydraface.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 3"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 portfolio-item filter-eyelash">
            <div class="portfolio-wrap">
              <img src="assets/img/treatment/eyelashlift.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Eyelash Lift</h4>
                <p>Eyelash Treatment</p>
                <div class="portfolio-links">
                  <a href="assets/img/treatment/eyelashlift.jpg" data-gallery="portfolioGallery" class="portfolio-lightbox" title="Web 3"><i class="bx bx-plus"></i></a>
                  <a href="portfolio-details.php" title="More Details"><i class="bx bx-link"></i></a>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Portfolio Section -->

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