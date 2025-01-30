<!-- ======= Footer ======= -->
<footer id="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-lg-3 col-md-6">
          <div class="footer-info">
            <h3>DM<span></span></h3>
            <p>
              Brias Street, 208 <br>
              Baranggay 3, Nasugbu,<br>
              4231 Batangas<br><br>
              <strong>Phone:</strong> 09658914686<br>
              <strong>Email:</strong> dhomyrna474@gmail.com<br>
            </p>
            <div class="social-links mt-3">
              <a href="https://www.facebook.com/dmatrading2013" class="facebook"><i class="bx bxl-facebook"></i></a>
              <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
            </div>
          </div>
        </div>

        <?php
          // Include database connection
          include 'database/db_connect.php';

          // Query to fetch brands from the database
          $query = "SELECT brand_name FROM brands ORDER BY brand_name ASC";
          $stmt = $pdo->query($query);

          // Fetch all brands into an array
          $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

          // Split the brands into two arrays for separate lists
          $half = ceil(count($brands) / 2);
          $brand_partners1 = array_slice($brands, 0, $half);
          $brand_partners2 = array_slice($brands, $half);
        ?>

        <div class="col-lg-2 col-md-6 footer-links">
          <h4>Brand Partners</h4>
          <ul>
            <?php if (count($brand_partners1) > 0): ?>
              <?php foreach ($brand_partners1 as $brand): ?>
                <li><i class="bx bx-chevron-right"></i> <a href="#"><?php echo htmlspecialchars($brand['brand_name']); ?></a></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li>No brand partners available.</li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>More Brand Partners</h4>
          <ul>
            <?php if (count($brand_partners2) > 0): ?>
              <?php foreach ($brand_partners2 as $brand): ?>
                <li><i class="bx bx-chevron-right"></i> <a href="#"><?php echo htmlspecialchars($brand['brand_name']); ?></a></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li>No additional brand partners available.</li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="col-lg-4 col-md-6 footer-newsletter">
          <h4>Email Us!</h4>
          <p>For inquiries contact us via email.</p>
          <form action="" method="post">
            <input type="email" name="email"><input type="submit" value="Inquire">
          </form>
        </div>

      </div>
    </div>
  </div>
</footer><!-- End Footer -->
