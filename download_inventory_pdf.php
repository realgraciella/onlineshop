<?php
require 'database/db_connect.php';
require 'vendor/autoload.php'; // Make sure to include the autoload file for the library

use Spipu\Html2Pdf\Html2Pdf;

// Fetch product details
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

// Create PDF content
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory PDF</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<img src="assets/img/logo/4.2.png" alt="Logo" style="display: block; margin: 0 auto;">

<div class="date">Printed on: <?php echo date('l, F j, Y'); ?></div>

<h1>Product Inventory</h1>

<table>
    <thead>
        <tr>
            <th>Brand</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stocks</th>
            <th>Stock per Variation</th>
            <th>Last Update</th>
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
                <td><?php echo $product['stock_level']; ?></td>
                <td><?php echo $product['stock_per_variation']; ?></td>
                <td><?php echo htmlspecialchars($product['updated_at']); ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo htmlspecialchars($product['product_desc']); ?></td>
                <td><?php echo "PHP " . number_format($product['price_per_variation'], 2); ?></td>
                <td><?php echo $product['stock_level']; ?></td>
                <td><?php echo $product['stock_per_variation']; ?></td>
                <td><?php echo htmlspecialchars($product['updated_at']); ?></td>
            </tr>
        <?php endif; endforeach; ?>
    </tbody>
</table>

</body>
</html>

<?php
$content = ob_get_clean();

// Create PDF
$html2pdf = new Html2Pdf();
$html2pdf->writeHTML($content);
$html2pdf->output('inventory.pdf');
?>