<?php
include 'database/db_connect.php';

// Function to fetch sales data
// Function to fetch sales data
function fetchSalesData($pdo, $period) {
    $validPeriods = ['7 DAY', '1 MONTH', '3 MONTH', '1 YEAR'];
    if (!in_array($period, $validPeriods)) {
        throw new InvalidArgumentException("Invalid period: $period");
    }
    $query = "SELECT SUM(sale_amount) as total_sales, 
                     COUNT(DISTINCT product_id) as total_products, 
                     SUM(quantity) as total_quantity 
              FROM sales 
              WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL $period)";
    $stmt = $pdo->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Fetch weekly, monthly, quarterly, and annual reports
$weeklyReport = fetchSalesData($pdo, '7 DAY');
$monthlyReport = fetchSalesData($pdo, '1 MONTH');
$quarterlyReport = fetchSalesData($pdo, '3 MONTH');
$annualReport = fetchSalesData($pdo, '1 YEAR');


// Fetch stock levels
$stockLevelsStmt = $pdo->query("SELECT product_name, stock_level FROM products");
$stockLevels = $stockLevelsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch most purchased products
$mostPurchasedStmt = $pdo->query("SELECT product_id, SUM(quantity) as total_quantity 
                                  FROM sales 
                                  GROUP BY product_id 
                                  ORDER BY total_quantity DESC 
                                  LIMIT 5");
$mostPurchased = $mostPurchasedStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Minified CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet"> <!-- Ensure minified CSS -->

    <!-- Deferred JS loading -->
    <script defer src="assets/js/admin.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.11/jspdf.plugin.autotable.min.js"></script>

    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-top: 80px;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        .rep-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .rep-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        p {
            font-size: 1rem;
            color: #555;
            margin: 5px 0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #eaf4e9;
        }

        /* Button Styles */
        button {
            display: block;
            margin: 20px auto;
            padding: 10px 30px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <h1>Sales Reports</h1>

    <div class="rep-container">
        <div class="rep-card">
            <h2>Weekly Report</h2>
            <p>Total Sales: <?php echo $weeklyReport['total_sales']; ?></p>
            <p>Total Products Sold: <?php echo $weeklyReport['total_products']; ?></p>
            <p>Total Quantity Sold: <?php echo $weeklyReport['total_quantity']; ?></p>
        </div>

        <div class="rep-card">
            <h2>Monthly Report</h2>
            <p>Total Sales: <?php echo $monthlyReport['total_sales']; ?></p>
            <p>Total Products Sold: <?php echo $monthlyReport['total_products']; ?></p>
            <p>Total Quantity Sold: <?php echo $monthlyReport['total_quantity']; ?></p>
        </div>

        <div class="rep-card">
            <h2>Quarterly Report</h2>
            <p>Total Sales: <?php echo $quarterlyReport['total_sales']; ?></p>
            <p>Total Products Sold: <?php echo $quarterlyReport['total_products']; ?></p>
            <p>Total Quantity Sold: <?php echo $quarterlyReport['total_quantity']; ?></p>
        </div>

        <div class="rep-card">
            <h2>Annual Report</h2>
            <p>Total Sales: <?php echo $annualReport['total_sales']; ?></p>
            <p>Total Products Sold: <?php echo $annualReport['total_products']; ?></p>
            <p>Total Quantity Sold: <?php echo $annualReport['total_quantity']; ?></p>
        </div>
    </div>

    <h2>Stock Levels</h2>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Stock Level</th>
        </tr>
        <?php foreach ($stockLevels as $row): ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['stock_level']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Most Purchased Products</h2>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Total Quantity Sold</th>
        </tr>
        <?php foreach ($mostPurchased as $row): ?>
        <tr>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo $row['total_quantity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php $pdo = null; ?>

    <button id="downloadBtn">Download Report</button>>

    <script>
document.getElementById('downloadBtn').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add the logo
    const logo = new Image();
    logo.src = 'assets/img/logo/4.2.png'; // Path to your logo
    logo.onload = function() {
        const logoWidth = 40; // Desired width
        const aspectRatio = logo.height / logo.width;
        const logoHeight = logoWidth * aspectRatio; // Maintain aspect ratio

        doc.addImage(logo, 'PNG', 150, 10, logoWidth, logoHeight); // Adjust position and maintain aspect ratio

        // Title
        doc.setFontSize(20);
        doc.text("Sales Reports", 20, 20);

        // Add current date
        const currentDate = new Date().toLocaleDateString();
        doc.setFontSize(12);
        doc.text(`Date: ${currentDate}`, 150, 50); // Position the date at the right corner

        // Weekly Report
        doc.setFontSize(16);
        doc.text("Weekly Report", 20, 70);
        doc.setFontSize(12);
        doc.text(`Total Sales: ${<?php echo json_encode($weeklyReport['total_sales']); ?>}`, 20, 80);
        doc.text(`Total Products Sold: ${<?php echo json_encode($weeklyReport['total_products']); ?>}`, 20, 90);
        doc.text(`Total Quantity Sold: ${<?php echo json_encode($weeklyReport['total_quantity']); ?>}`, 20, 100);

        // Monthly Report
        doc.setFontSize(16);
        doc.text("Monthly Report", 20, 110);
        doc.setFontSize(12);
        doc.text(`Total Sales: ${<?php echo json_encode($monthlyReport['total_sales']); ?>}`, 20, 120);
        doc.text(`Total Products Sold: ${<?php echo json_encode($monthlyReport['total_products']); ?>}`, 20, 130);
        doc.text(`Total Quantity Sold: ${<?php echo json_encode($monthlyReport['total_quantity']); ?>}`, 20, 140);

        // Quarterly Report
        doc.setFontSize(16);
        doc.text("Quarterly Report", 20, 150);
        doc.setFontSize(12);
        doc.text(`Total Sales: ${<?php echo json_encode($quarterlyReport['total_sales']); ?>}`, 20, 160);
        doc.text(`Total Products Sold: ${<?php echo json_encode($quarterlyReport['total_products']); ?>}`, 20, 170);
        doc.text(`Total Quantity Sold: ${<?php echo json_encode($quarterlyReport['total_quantity']); ?>}`, 20, 180);

        // Annual Report
        doc.setFontSize(16);
        doc.text("Annual Report", 20, 190);
        doc.setFontSize(12);
        doc.text(`Total Sales: ${<?php echo json_encode($annualReport['total_sales']); ?>}`, 20, 200);
        doc.text(`Total Products Sold: ${<?php echo json_encode($annualReport['total_products']); ?>}`, 20, 210);
        doc.text(`Total Quantity Sold: ${<?php echo json_encode($annualReport['total_quantity']); ?>}`, 20, 220);

        // Stock Levels
        doc.setFontSize(16);
        doc.text("Stock Levels", 20, 230);
        let stockLevels = <?php echo json_encode($stockLevels); ?>;
        stockLevels.forEach((product, index) => {
            doc.setFontSize(12);
            doc.text(`Product Name: ${product.product_name}, Stock Level: ${product.stock_level}`, 20, 240 + (index * 10));
        });

        // Most Purchased Products
        doc.setFontSize(16);
        doc.text("Most Purchased Products", 20, 240 + (stockLevels.length * 10) + 10);
        let mostPurchased = <?php echo json_encode($mostPurchased); ?>;
        mostPurchased.forEach((product, index) => {
            doc.setFontSize(12);
            doc.text(`Product ID: ${product.product_id}, Total Quantity Sold: ${product.total_quantity}`, 20, 250 + (index * 10) + (stockLevels.length * 10));
        });

        // Save the PDF
        doc.save("Sale_Report.pdf");
    };
});
</script>

</body>
</html>