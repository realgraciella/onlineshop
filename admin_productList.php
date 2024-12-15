<?php
include 'database/db_connect.php';

// Handle search query if the form is submitted
$search_term = '';
if (isset($_GET['search'])) {
   $search_term = $_GET['search'];
}

// Fetch products and their variations from the database
$query = "
   SELECT 
       p.product_id,
       pv.variation_id,
       p.product_name,
       p.product_desc,
       pv.variation_value,
       pv.stock_per_variation,
       pv.price_per_variation,
       pv.old_price_variation,
       p.price,
       p.old_price,
       p.stock_level,
       p.on_sale
   FROM products p
   LEFT JOIN product_variations pv ON p.product_id = pv.product_id
   WHERE p.product_name LIKE :search_term OR p.product_desc LIKE :search_term
";
$stmt = $pdo->prepare($query);
$stmt->execute(['search_term' => '%' . $search_term . '%']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handling form submissions for price updates and stock changes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   if (isset($_POST['update_product'])) {
       // Update product details
       $product_id = $_POST['edit_product_id'];
       $product_name = $_POST['edit_product_name'];
       $product_desc = $_POST['edit_product_desc'];
       $variation_value = $_POST['edit_variation_value'];
   
       // Update the product details
       $update_query = "
           UPDATE products p
           JOIN product_variations pv ON p.product_id = pv.product_id
           SET p.product_name = :product_name,
               p.product_desc = :product_desc,
               pv.variation_value = :variation_value,
               p.updated_at = NOW()
           WHERE p.product_id = :product_id
       ";
       $stmt = $pdo->prepare($update_query);
       $stmt->execute([
           'product_name' => $product_name,
           'product_desc' => $product_desc,
           'variation_value' => $variation_value,
           'product_id' => $product_id
       ]);
   
       echo "<script>showMessage('Success', 'Product details updated successfully');</script>";
   }

   if (isset($_POST['update_price'])) {
    // Update price_per_variation and old_price_variation
    $variation_id = $_POST['variation_id'];
    $new_price_per_variation = $_POST['new_price_per_variation'];
    $on_sale = isset($_POST['on_sale']) ? 1 : 0;
    $discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : 0;

    // Fetch the current price_per_variation before updating
    $current_price_query = "
        SELECT price_per_variation 
        FROM product_variations 
        WHERE variation_id = :variation_id
    ";
    $stmt = $pdo->prepare($current_price_query);
    $stmt->execute(['variation_id' => $variation_id]);
    $current_price_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($current_price_data) {
        $current_price_per_variation = $current_price_data['price_per_variation'];

        // Calculate the discounted price if on sale
        if ($on_sale && $discount_percentage > 0) {
            $discount_amount = ($new_price_per_variation * $discount_percentage) / 100;
            $new_price_per_variation -= $discount_amount;
        }

        // Round off the new price before updating
        $new_price_per_variation = round($new_price_per_variation, 2);

        // Update the price_per_variation and set old_price_variation
        $update_query = "
            UPDATE product_variations 
            SET price_per_variation = :new_price_per_variation, 
                old_price_variation = :current_price_per_variation, 
                updated_at = NOW() 
            WHERE variation_id = :variation_id
        ";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            'new_price_per_variation' => $new_price_per_variation,
            'current_price_per_variation' => $current_price_per_variation,
            'variation_id' => $variation_id
        ]);

        // Update the product price based on the new price range
        $price_query = "
            SELECT MIN(price_per_variation) AS min_price, MAX(price_per_variation) AS max_price 
            FROM product_variations 
            WHERE product_id = (SELECT product_id FROM product_variations WHERE variation_id = :variation_id)
        ";
        $stmt = $pdo->prepare($price_query);
        $stmt->execute(['variation_id' => $variation_id]);
        $price_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debugging: Log the fetched price data
        error_log("Fetched price data: " . print_r($price_data, true));

        // Check if price data is valid
        if ($price_data && isset($price_data['min_price']) && isset($price_data['max_price'])) {
            $new_price = round($price_data['min_price'], 2) . ' - ' . round($price_data['max_price'], 2);
            $update_product_query = "UPDATE products SET price = :new_price, on_sale = :on_sale, updated_at = NOW() WHERE product_id = (SELECT product_id FROM product_variations WHERE variation_id = :variation_id)";
            $stmt = $pdo->prepare($update_product_query);
            $stmt->execute(['new_price' => $new_price, 'on_sale' => $on_sale, 'variation_id' => $variation_id]);

            echo "<script>showMessage('Success', 'Successfully updated the price per variation');</script>";
        } else {
            echo "<script>showMessage('Error', 'Failed to fetch price range.');</script>";
        }
    } else {
        echo "<script>showMessage('Error', 'Failed to fetch current price.');</script>";
    }
}

   if (isset($_POST['update_stock'])) {
       // Update stock level
       $variation_id = $_POST['variation_id'];
       $new_stock_per_variation = $_POST['new_stock_per_variation'];

       $update_query = "UPDATE product_variations SET stock_per_variation = :new_stock_per_variation WHERE variation_id = :variation_id";
       $stmt = $pdo->prepare($update_query);
       $stmt->execute(['new_stock_per_variation' => $new_stock_per_variation, ' variation_id' => $variation_id]);

       // Update stock level in products table
       $stock_query = "SELECT SUM(stock_per_variation) FROM product_variations WHERE product_id = (SELECT product_id FROM product_variations WHERE variation_id = :variation_id)";
       $stmt = $pdo->prepare($stock_query);
       $stmt->execute(['variation_id' => $variation_id]);
       $total_stock = $stmt->fetchColumn();

       $update_product_stock_query = "UPDATE products SET stock_level = :total_stock, updated_at = NOW() WHERE product_id = (SELECT product_id FROM product_variations WHERE variation_id = :variation_id)";
       $stmt = $pdo->prepare($update_product_stock_query);
       $stmt->execute(['total_stock' => $total_stock, 'variation_id' => $variation_id]);

       echo "<script>showMessage('Success', 'Successfully updated the stock level');</script>";
   }

   if (isset($_POST['delete_product'])) {
       $product_id = $_POST['delete_product_id'];
       $variation_id = $_POST['delete_variation_id'];
   
       // First, delete the variation
       if ($variation_id) {
           $delete_variation_query = "DELETE FROM product_variations WHERE variation_id = :variation_id"; 
           $stmt = $pdo->prepare($delete_variation_query);
           $stmt->execute(['variation_id' => $variation_id]);
           echo "<script>showMessage('Deleted', 'Product variation deleted successfully');</script>";
       }
   
       // Check if there are no more variations for the product
       $check_variations_query = "SELECT COUNT(*) FROM product_variations WHERE product_id = :product_id";
       $stmt = $pdo->prepare($check_variations_query);
       $stmt->execute(['product_id' => $product_id]);
       $variation_count = $stmt->fetchColumn();
   
       // If no variations left, delete the product
       if ($variation_count == 0) {
           $delete_product_query = "DELETE FROM products WHERE product_id = :product_id"; 
           $stmt = $pdo->prepare($delete_product_query);
           $stmt->execute(['product_id' => $product_id]);
           echo "<script>showMessage('Deleted', 'Product deleted successfully');</script>";
       }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin - Product List</title>
   <link href="assets/img/logo/2.png" rel="icon">

   <!-- Google Fonts -->
   <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

   <!-- Vendor CSS Files -->
   <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link href="assets/css/admin.css" rel="stylesheet">

   <style>
       body {
           background-color: #f8f9fa;
       }
       h1 {
           margin: 90px auto;
           text-align: center;
           color: #343a40;
       }
       .search-container {
           margin-top: 5px auto;
           text-align: center;
       }
       #searchInput {
           padding: 10px;
           font-size: 16px;
           width: 100%; 
           max-width: 800px;
           border-radius: 5px;
           border: 1px solid #ced4da;
           transition: all 0.3s ease;
       }
       #searchInput:focus {
           outline: none;
           border-color: #28a745;
       }
       .table {
           margin: 20px auto;
           width: 90%;
           background-color: #fff;
           border-radius: 8px;
           overflow: hidden;
           box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
       }
       .table th {
           background-color: #343a40;
           color: white;
           text-align: center;
       }
       .table tbody tr:hover {
           background-color: #f1f1f1;
           color: black;
       }
       .btn {
           transition: background-color 0.3s ease; 
       }
       .btn:hover {
           background-color:rgba(255, 255, 255, 0.48);
           color: black;
       }
       .modal {
           display: none;
           position: fixed;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%); 
           border-radius: 8px;
           box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
           z-index: 1000; 
           background-color: #ffffff; 
           max-width: 500px; 
           width: 90%; 
           max-height: 50%; 
           overflow-y: auto; 
           transition: all 0.3s ease; 
       }
       .price-modal {
           display: none; 
           position: fixed;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%); 
           border-radius: 8px;
           box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
           z-index: 1000; 
           background-color: #ffffff; 
           max-width: 500px;
           width: 90%; 
           max-height: 50%; 
           overflow-y: auto; 
           transition: all 0.3s ease; 
       }
       .stock-modal {
           display: none; 
           position: fixed;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%); 
           border-radius: 8px;
           box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
           z-index: 1000; 
           background-color: #ffffff; 
           max-width: 500px; 
           width: 90%; 
           max-height: 50%;
           overflow-y: auto; 
           transition: all 0.3s ease; 
       }
       .delete-modal {
           display: none; 
           position: fixed;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%); 
           border-radius: 8px;
           box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
           z-index: 1000; 
           background-color: #ffffff; 
           max-width: 500px; 
           width: 90%; 
           max-height: 50%; 
           overflow-y: auto; 
           transition: all 0.3s ease; 
       }

       .modal-header {
           background-color: #343a40; 
           color: white;
           padding: 15px;
           border-top-left-radius: 8px;
           border-top-right-radius: 8px;
       }

       .modal-body {
           padding: 20px;
       }

       .modal-footer {
           justify-content: space-evenly;
           padding: 15px;
       }

       .modal-overlay {
           display: none; 
           position: fixed;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           background-color: rgba(0, 0, 0, 0.4); 
           z-index: 999; 
       }
       .close {
           background: none;
           border: none;
           color: white;
           font-size: 20px;
           cursor: pointer;
       }
       /* Responsive Modifications */
       @media (max-width: 768px) {
           .table {
               display: block;
               overflow-x: auto;
               white-space: nowrap;
           }
           #searchInput {
               width: 80%;
           }
       }
   </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="list-container">
   <h1>Product List</h1>

   <!-- Search Bar -->
   <div class="search-container">
       <input type="text" id="searchInput" placeholder="Search for products..." onkeyup="searchProducts()">
   </div>

   <table class="table table-striped">
       <thead>
           <tr>
               <th>Product Name</th>
               <th>Description</th>
               <th>Variation Value</th>
               <th>Stock per Variation</th>
               <th>Price per Variation</th>
               <th>Price</th>
               <th>Old Price</th>
               <th>Stock Level</th>
               <th>On Sale</th>
               <th>Actions</th>
           </tr>
       </thead>
       <tbody>
           <?php foreach ($products as $product) : ?>
               <tr class="<?= $product['on_sale'] ? 'on-sale' : '' ?>">
               <td><?= htmlspecialchars($product['product_name']) ?></td>
               <td><?= htmlspecialchars($product['product_desc']) ?></td>
               <td><?= htmlspecialchars($product['variation_value']) ?></td>
               <td><?= htmlspecialchars($product['stock_per_variation']) ?></td>
               <td><?= number_format($product['price_per_variation'], 2) ?></td>
               <td>
                   <?php 
                   // Extract numeric values from price and old_price
                   $current_price = floatval(str_replace(['PHP ', ','], '', $product['price']));
                   $old_price = floatval(str_replace(['PHP ', ','], '', $product['old_price']));

                   if ($product['on_sale']) : ?>
                       <span class="old-price"><?= number_format($old_price, 2) ?></span><br>
                       <?= number_format($current_price, 2) ?>
                   <?php else : ?>
                       <?= number_format($current_price, 2) ?>
                   <?php endif; ?>
               </td>
               <td>
                    <?php 
                    $old_price_variation = floatval(str_replace(['PHP ', ','], '', $product['old_price_variation']));
                    
                    if ($old_price_variation) : ?>
                        <?= number_format($old_price_variation, 2) ?>
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </td>
               <td><?= htmlspecialchars($product['stock_level']) ?></td>
               <td><?= $product['on_sale'] ? 'Yes' : 'No' ?></td>
               <td>
                   <button class="btn btn-warning" onclick="showEditForm(<?= $product['product_id'] ?>, '<?= htmlspecialchars($product['product_name']) ?>', '<?= htmlspecialchars($product['product_desc']) ?>', '<?= htmlspecialchars($product['variation_value']) ?>')"><i class="fas fa-edit"></i> Edit</button>
                   <button class="btn btn-info" onclick="showPriceForm(<?= $product['variation_id'] ?>, <?= $product['price_per_variation'] ?>, <?= $product['on_sale'] ?>)"><i class="fas fa-tag"></i> Modify Price</button>
                   <button class="btn btn-success" onclick="showStockForm(<?= $product['variation_id'] ?>, <?= $product['stock_per_variation'] ?>)"><i class="fas fa-box"></i> Re-stock</button>
                   <button class="btn btn-danger" onclick="showDeleteConfirmation(<?= $product['product_id'] ?>, <?= $product['variation_id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
               </td>
           </tr>
       <?php endforeach; ?>
       </tbody>
   </table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
   <div class="modal-header">
       <h3>Edit Product Details</h3>
       <button type="button" class="btn btn-danger" onclick="closeEditForm()">&times;</button>
   </div>
   <form method="POST">
       <div class="modal-body">
           <input type="hidden" name="edit_product_id" id="edit_product_id">
           <div class="form-group">
               <label for="edit_product_name">Product Name:</label>
               <input type="text" class="form-control" name="edit_product_name" id="edit_product_name" required>
           </div>
           <div class="form-group">
               <label for="edit_product_desc">Product Description:</label>
               <textarea class="form-control" name="edit_product_desc" id="edit_product_desc" required></textarea>
           </div>
           <div class="form-group">
               <label for="edit_variation_value">Variation Value:</label>
               <input type="text" class="form-control" name="edit_variation_value" id="edit_variation_value" required>
           </div>
       </div>
       <div class="modal-footer">
           <button type="submit" name="update_product" class="btn btn-success">Save Changes</button>
           <button type="button" class="btn btn-danger" onclick="closeEditForm()">Cancel</button>
       </div>
   </form>
</div>

<!-- Price Modal -->
<div id="priceModal" class="price-modal">
   <div class="modal-header">
       <h3>Modify Price per Variation</h3>
       <button type="button" class="btn btn-danger" onclick="closePriceForm()">&times;</button>
   </div>
   <form method="POST">
       <div class="modal-body">
           <input type="hidden" name="variation_id" id="variation_id">
           <div class="form-group">
               <label for="new_price_per_variation">New Price per Variation:</label>
               <input type="number" class="form-control" name="new_price_per_variation" id="new_price_per_variation" required step="0.01">
           </div>
           <div class="form-group">
               <label for="on_sale">On Sale:</label>
               <input type="checkbox" name="on_sale" id="on_sale" onclick="toggleDiscountInput()">
           </div>
           <div class="form-group" id="discountInput" style="display: none;">
               <label for="discount_percentage">Discount Percentage:</label>
               <input type="number" class="form-control" name="discount_percentage" id="discount_percentage" min="0" max="100" step="0.01" oninput="calculateSavings()">
               <div id="savingsMessage" style="text-align: center; margin-top: 10px; display: none;"></div>
           </div>
       </div>
       <div class="modal-footer">
           <button type="submit" name="update_price" class="btn btn-success">Save Changes</button>
           <button type="button" class="btn btn-danger" onclick="closePriceForm()">Cancel</button>
       </div>
   </form>
</div>

<!-- Stock Modal -->
<div id="stockModal" class="stock-modal">
   <div class="modal-header">
       <h3>Modify Stock Level</h3>
       <button type="button" class="btn btn-danger" onclick="closeStockForm()">&times;</button>
   </div>
   <form method="POST">
       <div class="modal-body">
           <input type="hidden" name="variation_id" id="stock_variation_id">
           <div class="form-group">
               <label for="new_stock_per_variation">New Stock per Variation:</label>
               <input type="number" class="form-control" name="new_stock_per_variation" id="new_stock_per_variation" required min="0">
           </div>
       </div>
       <div class="modal-footer">
           <button type="submit" name="update_stock" class="btn btn-success">Save Changes</button>
           <button type="button" class="btn btn-danger" onclick="closeStockForm()">Cancel</button>
       </div>
   </form>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
   <div class="modal-header">
       <h3>Delete Confirmation</h3>
       <button type="button" class="btn btn-danger" onclick="closeDeleteConfirmation()">&times;</button>
   </div>
   <form method="POST">
       <div class="modal-body">
           <input type="hidden" name="delete_product_id" id="delete_product_id">
           <input type="hidden" name="delete_variation_id" id="delete_variation_id">
           <p>Are you sure you want to delete this product variation?</p>
       </div>
       <div class="modal-footer">
           <button type="submit" name="delete_product" class="btn btn-success">Yes</button>
           <button type="button" class="btn btn-danger" onclick="closeDeleteConfirmation()">No</button>
       </div>
   </form>
</div>

<!-- Message Modal -->
<div id="messageModal" class="modal">
   <div class="modal-header">
       <h3 id="messageTitle">Message</h3>
       <button type="button" class="btn btn-danger" onclick="closeMessageModal()">&times;</button>
   </div>
   <div class="modal-body">
       <p id="messageContent"></p>
   </div>
   <div class="modal-footer">
       <button type="button" class="btn btn-success" onclick="closeMessageModal()">OK</button>
   </div>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" class="modal-overlay" onclick="closePriceForm(); closeStockForm(); closeDeleteConfirmation(); closeEditForm()"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   function showEditForm(productId, productName, productDesc, variationValue) {
       document.getElementById('edit_product_id').value = productId;
       document.getElementById('edit_product_name').value = productName;
       document.getElementById('edit_product_desc').value = productDesc;
       document.getElementById('edit_variation_value').value = variationValue;
       document.getElementById('editModal').style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   }

   function closeEditForm() {
       document.getElementById('editModal').style.display = 'none';
       document.getElementById('modalOverlay').style.display = 'none';
   }

   function showPriceForm(variationId, currentPricePerVariation, onSale) {
       document.getElementById('variation_id').value = variationId;
       document.getElementById('new_price_per_variation').value = currentPricePerVariation;
       document.getElementById('on_sale').checked = onSale == 1;
       document.getElementById('priceModal').style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   }

   function toggleDiscountInput() {
        const discountInput = document.getElementById('discountInput');
        const onSaleCheckbox = document.getElementById('on_sale');
        discountInput.style.display = onSaleCheckbox.checked ? 'block' : 'none';
    }

    function calculateSavings() {
        const newPrice = parseFloat(document.getElementById('new_price_per_variation').value);
        const discountPercentage = parseFloat(document.getElementById('discount_percentage').value);
        const savingsMessage = document.getElementById('savingsMessage');

        if (!isNaN(newPrice) && !isNaN(discountPercentage) && discountPercentage > 0) {
            const discountAmount = (newPrice * discountPercentage) / 100;
            const roundedSavings = Math.round(discountAmount * 100) / 100;
            savingsMessage.innerText = `You save PHP ${roundedSavings.toFixed(2)}`;
            savingsMessage.style.display = 'block';
        } else {
            savingsMessage.style.display = 'none';
        }
    }

   function closePriceForm() {
       document.getElementById('priceModal').style.display = 'none';
       document.getElementById('modalOverlay').style.display = 'none';
   }

   function showStockForm(variationId, stockPerVariation) {
       document.getElementById('stock_variation_id').value = variationId;
       document.getElementById('new_stock_per_variation').value = stockPerVariation;
       document.getElementById('stockModal').style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   }

   function closeStockForm() {
       document.getElementById('stockModal').style.display = 'none';
       document.getElementById('modalOverlay').style.display = 'none';
   }

   function showDeleteConfirmation(productId, variationId) {
       document.getElementById('delete_product_id').value = productId;
       document.getElementById('delete_variation_id').value = variationId;
       document.getElementById('deleteModal').style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   }

   function closeDeleteConfirmation() {
       document.getElementById('deleteModal').style.display = 'none';
       document.getElementById('modalOverlay').style.display = 'none';
   }

   function searchProducts() {
       const input = document.getElementById('searchInput');
       const filter = input.value.toLowerCase();
       const rows = document.querySelectorAll('table tbody tr');

       rows.forEach(row => {
           const productName = row.cells[0].textContent.toLowerCase();
           if (productName.indexOf(filter) > -1) {
               row.style.display = "";
           } else {
               row.style.display = "none";
           }
       });
   }

   function showMessage(title, content) {
       document.getElementById('messageTitle').innerText = title;
       document.getElementById('messageContent').innerText = content;
       document.getElementById('messageModal').style.display = 'block';
       document.getElementById('modalOverlay').style.display = 'block';
   }

   function closeMessageModal() {
       document.getElementById('messageModal').style.display = 'none';
       document.getElementById('modalOverlay').style.display = 'none';
   }
</script>
</body>
</html>