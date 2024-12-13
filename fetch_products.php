<!-- <?php
include 'database/db_connect.php';

if (isset($_POST['brand_id'])) {
    $brand_id = $_POST['brand_id'];

    $query = $brand_id == "all" ? 
        "SELECT p.*, b.brand_name FROM products p 
         JOIN brands b ON p.brand_id = b.brand_id" : 
        "SELECT p.*, b.brand_name FROM products p 
         JOIN brands b ON p.brand_id = b.brand_id WHERE b.brand_id = :brand_id";

    $stmt = $pdo->prepare($query);
    if ($brand_id != "all") {
        $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
    }
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $imagePath = 'uploads/products/' . $product['product_image_url'];
        if (!file_exists($imagePath)) {
            $imagePath = 'assets/img/default-image.png';
        }
        echo '
        <div class="col-lg-4 col-md-6 portfolio-item">
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
}
?> -->
