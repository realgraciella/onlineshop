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

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="inventory_list.xls"');

// Output the data
echo "Brand\tProduct Name\tDescription\tPrice\tStocks\tStock per Variation\tLast Update\n";

$currentBrand = '';
foreach ($products as $product) {
    if ($currentBrand !== $product['brand_name']) {
        $currentBrand = $product['brand_name'];
    }

    // Determine which price to use
    $priceToDisplay = !empty($product['price_per_variation']) ? $product['price_per_variation'] : $product['price'];

    echo "{$currentBrand}\t{$product['product_name']}\t{$product['product_desc']}\t" . number_format($priceToDisplay, 2) . "\t{$product['stock_level']}\t{$product['stock_per_variation']}\t{$product['updated_at']}\n";
}
?>