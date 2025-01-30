<?php
include 'database/db_connect.php';

// Query to fetch product details including stock levels and variations
$query = "
    SELECT p.product_name, p.product_desc, p.price, p.stock_level, 
           b.brand_name, pv.stock_per_variation, pv.price_per_variation, pv.updated_at
    FROM products p
    JOIN brands b ON p.brand_id = b.brand_id
    JOIN product_variations pv ON p.product_id = pv.product_id
    ORDER BY b.brand_name, p.product_name
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .inventory-container {
            width: 80%;
            margin: 80px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            flex: 1;
            margin-right: 10px; /* Space between search bar and buttons */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        tr:hover td {
            background-color: #ddd;
        }

        .stock-level {
            font-weight: bold;
        }

        .low-stock {
            color: red;
        }

        .medium-stock {
            color: orange;
        }

        .high-stock {
            color: green;
        }
    </style>
</head>

<body>
<?php include 'admin_header.php'; ?>
    <div class="inventory-container">
        <h1>Product List</h1>

        <!-- Search Bar and Buttons Container -->
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search for products...">
            <div>
                <a href="download_inventory_excel.php" class="btn btn-primary">Download Inventory List</a>
                <a href="#" id="downloadPdf" class="btn btn-primary">Download Inventory PDF</a>
            </div>
        </div>

        <table id="productTable">
    <thead>
        <tr>
            <th>Brand</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stocks</th>
            <th>Stock per Variation</th>
            <th>Last Update</th>
            <th>Action</th> <!-- New Action Column -->
        </tr>
    </thead>
    <tbody>
        <?php 
        $currentBrand = '';
        foreach ($products as $product): 
            if ($currentBrand !== $product['brand_name']): 
                $currentBrand = $product['brand_name'];
        ?>
            <tr>
                <td rowspan="<?php echo count(array_filter($products, fn($p) => $p['brand_name'] === $currentBrand)); ?>">
                    <?php echo htmlspecialchars($currentBrand); ?>
                </td>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['product_desc']); ?></td>
                <td><?php echo "PHP " . number_format($product['price'], 2); ?></td>
                <td class="stock-level <?php echo ($product['stock_level'] <= 5) ? 'low-stock' : (($product['stock_level'] <= 20) ? 'medium-stock' : 'high-stock'); ?>">
                    <?php echo $product['stock_level']; ?>
                </td>
                <td class="stock-level <?php echo ($product['stock_level'] <= 5) ? 'low-stock' : (($product['stock_level'] <= 20) ? 'medium-stock' : 'high-stock'); ?>">
                    <?php echo $product['stock_level']; ?>
                </td>
                <td><?php echo (new DateTime($product['updated_at']))->format('F j, Y'); ?></td>
                <td>
                    <?php if ($product['stock_level'] <= 20): // Show button if stock is low or medium ?>
                        <button class="btn btn-warning" onclick="alert('Restock requested for <?php echo htmlspecialchars($product['product_name']); ?>')">Restock</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Restock</button> <!-- Disabled button for high stock -->
                    <?php endif; ?>
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['product_desc']); ?></td>
                <td><?php echo "PHP " . number_format($product['price_per_variation'], 2); ?></td>
                <td class="stock-level <?php echo ($product['stock_level'] <= 5) ? 'low-stock' : (($product['stock_level'] <= 20) ? 'medium-stock' : 'high-stock'); ?>">
                    <?php echo $product['stock_level']; ?>
                </td>
                <td class="stock_per_variation <?php echo ($product['stock_per_variation'] <= 5) ? 'low-stock' : (($product['stock_per_variation'] <= 20) ? 'medium-stock' : 'high-stock'); ?>">
                    <?php echo $product['stock_per_variation']; ?>
                </td>
                <td><?php echo (new DateTime($product['updated_at']))->format('F j, Y'); ?></td>
                <td>
                    <?php if ($product['stock_level'] <= 20): ?>
                        <button class="btn btn-warning" onclick="alert('Restock requested for <?php echo htmlspecialchars($product['product_name']); ?>')">Restock</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Restock</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; endforeach; ?>
    </tbody>
</table>
    </div>
    
<!-- Add this modal structure just before the closing </body> tag -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restockModalLabel">Restock Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="restockForm">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="restockQuantity" class="form-label">Restock Quantity</label>
                        <input type="number" class="form-control" id="restockQuantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="restockDate" class="form-label">Restock Date</label>
                        <input type="date" class="form-control" id="restockDate" value="2025-01-30" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Restock</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Update the Restock button to open the modal
    function openRestockModal(productName) {
        document.getElementById('productName').value = productName;
        $('#restockModal').modal('show');
    }

    // Add event listener to the restock buttons
    document.querySelectorAll('.btn-warning').forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.getAttribute('data-product-name');
            openRestockModal(productName);
        });
    });

    // Handle the form submission
    document.getElementById('restockForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const productName = document.getElementById('productName').value;
        const quantity = document.getElementById('restockQuantity').value;
        const date = document.getElementById('restockDate').value;

        // Here you can handle the restock logic, e.g., send an AJAX request to the server
        alert(`Restock request submitted for ${productName} with quantity ${quantity} on ${date}.`);
        $('#restockModal').modal('hide');
    });
</script>
    <!-- Vendor JS Files -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTable tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const brand = cells[0].innerText.toLowerCase();
            const productName = cells[1].innerText.toLowerCase();
            const description = cells[2].innerText.toLowerCase();

            if (brand.includes(filter) || productName.includes(filter) || description.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.getElementById('downloadPdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Add logo
        const logo = new Image();
        logo.src = 'assets/img/logo/4.2.png';
        logo.onload = function() {
            doc.addImage(logo, 'PNG', 10, 10, 50, 20); // Adjust the position and size as needed
            doc.text(`Printed on: ${new Date().toLocaleDateString()}`, 150, 20);
            doc.text('Product Inventory', 105, 40, { align: 'center' });

            // Add table
            const tableColumn = ["Brand", "Product Name", "Description", "Price", "Stocks", "Stock per Variation", "Last Update"];
            const tableRows = [];

            // Get data from the table
            const rows = document.querySelectorAll('#productTable tbody tr');
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                const rowData = [];
                cols.forEach(col => {
                    rowData.push(col.innerText);
                });
                tableRows.push(rowData);
            });

            doc.autoTable(tableColumn, tableRows, { startY: 60 });
            doc.save('inventory.pdf');
        };
    });
</script>
</body>

</html>