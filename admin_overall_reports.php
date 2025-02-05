<?php
include 'database/db_connect.php';

// Function to fetch sales data
function fetchSalesData($pdo, $period) {
    $validPeriods = ['7 DAY', '1 MONTH', '3 MONTH', '1 YEAR'];
    if (!in_array($period, $validPeriods)) {
        throw new InvalidArgumentException("Invalid period: $period");
    }
    $query = "SELECT SUM(total_amount) as total_sales, 
                     COUNT(DISTINCT product_id) as total_products, 
                     SUM(quantity) as total_quantity 
              FROM store_sales 
              WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL $period)";
    $stmt = $pdo->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch weekly, monthly, quarterly, and annual reports
$weeklyReport = fetchSalesData($pdo, '7 DAY');
$monthlyReport = fetchSalesData($pdo, '1 MONTH');
$quarterlyReport = fetchSalesData($pdo, '3 MONTH');
$annualReport = fetchSalesData($pdo, '1 YEAR');

// Fetch most purchased products with product name and variation value
$mostPurchasedStmt = $pdo->query("
    SELECT p.product_id, p.product_name, pv.variation_value, SUM(s.quantity) as total_quantity 
    FROM store_sales s
    JOIN products p ON s.product_id = p.product_id
    JOIN product_variations pv ON p.product_id = pv.product_id
    GROUP BY p.product_id, pv.variation_value 
    ORDER BY total_quantity DESC 
    LIMIT 5
");
$mostPurchased = $mostPurchasedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch least purchased products with product name and variation value
$leastPurchasedStmt = $pdo->query("
    SELECT p.product_id, p.product_name, pv.variation_value, SUM(s.quantity) as total_quantity 
    FROM store_sales s
    JOIN products p ON s.product_id = p.product_id
    JOIN product_variations pv ON p.product_id = pv.product_id
    GROUP BY p.product_id, pv.variation_value 
    ORDER BY total_quantity ASC 
    LIMIT 5
");
$leastPurchased = $leastPurchasedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sales report per brand
$salesPerBrandStmt = $pdo->query("SELECT b.brand_name, SUM(s.total_amount) as total_sales 
                                   FROM store_sales s
                                   JOIN products p ON s.product_id = p.product_id
                                   JOIN brands b ON p.brand_id = b.brand_id
                                   GROUP BY b.brand_name 
                                   ORDER BY total_sales DESC");
$salesPerBrand = $salesPerBrandStmt->fetchAll(PDO::FETCH_ASSOC);
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
        <p>Total Sales: PHP<?php echo number_format($weeklyReport['total_sales'], 2); ?></p>
        <p>Total Products Sold: <?php echo number_format($weeklyReport['total_products']); ?> brands</p>
        <p>Total Quantity Sold: <?php echo number_format($weeklyReport['total_quantity']); ?> pcs.</p>
    </div>

    <div class="rep-card">
        <h2>Monthly Report</h2>
        <p>Total Sales: PHP<?php echo number_format($monthlyReport['total_sales'], 2); ?></p>
        <p>Total Products Sold: <?php echo number_format($monthlyReport['total_products']); ?> brands</p>
        <p>Total Quantity Sold: <?php echo number_format($monthlyReport['total_quantity']); ?> pcs.</p>
    </div>

    <div class="rep-card">
        <h2>Quarterly Report</h2>
        <p>Total Sales: PHP<?php echo number_format($quarterlyReport['total_sales'], 2); ?></p>
        <p>Total Products Sold: <?php echo number_format($quarterlyReport['total_products']); ?> brands</p>
        <p>Total Quantity Sold: <?php echo number_format($quarterlyReport['total_quantity']); ?> pcs.</p>
    </div>

    <div class="rep-card">
        <h2>Annual Report</h2>
        <p>Total Sales: PHP<?php echo number_format($annualReport['total_sales'], 2); ?></p>
        <p>Total Products Sold: <?php echo number_format($annualReport['total_products']); ?> brands</p>
        <p>Total Quantity Sold: <?php echo number_format($annualReport['total_quantity']); ?> pcs.</p>
    </div>
</div>

    <h2>Most Purchased Products</h2>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Variation Value</th>
            <th>Total Quantity Sold</th>
        </tr>
        <?php foreach ($mostPurchased as $row): ?>
        <tr>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['variation_value']); ?></td>
            <td><?php echo $row['total_quantity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Least Purchased Products</h2>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Variation Value</th>
            <th>Total Quantity Sold</th>
        </tr>
        <?php foreach ($leastPurchased as $row): ?>
        <tr>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['variation_value']); ?></td>
            <td><?php echo $row['total_quantity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Sales Report Per Brand</h2>
        <table>
            <tr>
                <th>Brand</th>
                <th>Total Sales</th>
            </tr>
            <?php foreach ($salesPerBrand as $row): ?>
            <tr>
                <td><?php echo $row['brand_name']; ?></td>
                <td>PHP<?php echo number_format($row['total_sales'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php $pdo = null; ?>

    <button id="downloadBtn">Download Report</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.11/jspdf.plugin.autotable.min.js"></script>

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
        window.jspdf.autoTable(doc, {
            startY: 80,
            head: [['Metric', 'Value']],
            body: [
                ['Total Sales', `PHP ${<?php echo json_encode($weeklyReport['total_sales']); ?>}`],
                ['Total Products Sold', `<?php echo json_encode($weeklyReport['total_products']); ?>`],
                ['Total Quantity Sold', `<?php echo json_encode($weeklyReport['total_quantity']); ?>`]
            ]
        });

        // Monthly Report
        doc.setFontSize(16);
        doc.text("Monthly Report", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Metric', 'Value']],
            body: [
                ['Total Sales', `PHP ${<?php echo json_encode($monthlyReport['total_sales']); ?>}`],
                ['Total Products Sold', `<?php echo json_encode($monthlyReport['total_products']); ?>`],
                ['Total Quantity Sold', `<?php echo json_encode($monthlyReport['total_quantity']); ?>`]
            ]
        });

        // Quarterly Report
        doc.setFontSize(16);
        doc.text("Quarterly Report", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Metric', 'Value']],
            body: [
                ['Total Sales', `PHP ${<?php echo json_encode($quarterlyReport['total_sales']); ?>}`],
                ['Total Products Sold', `<?php echo json_encode($quarterlyReport['total_products']); ?>`],
                ['Total Quantity Sold', `<?php echo json_encode($quarterlyReport['total_quantity']); ?>`]
            ]
        });

        // Annual Report
        doc.setFontSize(16);
        doc.text("Annual Report", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Metric', 'Value']],
            body: [
                ['Total Sales', `PHP ${<?php echo json_encode($annualReport['total_sales']); ?>}`],
                ['Total Products Sold', `<?php echo json_encode($annualReport['total_products']); ?>`],
                ['Total Quantity Sold', `<?php echo json_encode($annualReport['total_quantity']); ?>`]
            ]
        });

        // Most Purchased Products
        doc.setFontSize(16);
        doc.text("Most Purchased Products", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Product ID', 'Product Name', 'Variation Value', 'Total Quantity Sold']],
            body: <?php echo json_encode(array_map(function($product) {
                return [$product['product_id'], $product['product_name'], $product['variation_value'], $product['total_quantity']];
            }, $mostPurchased)); ?>
        });

        // Least Purchased Products
        doc.setFontSize(16);
        doc.text("Least Purchased Products", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Product ID', 'Product Name', 'Variation Value', 'Total Quantity Sold']],
            body: <?php echo json_encode(array_map(function($product) {
                return [$product['product_id'], $product['product_name'], $product['variation_value'], $product['total_quantity']];
            }, $leastPurchased)); ?>
        });

        // Sales Report Per Brand
        doc.setFontSize(16);
        doc.text("Sales Report Per Brand", 20, doc.lastAutoTable.finalY + 10);
        window.jspdf.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 20,
            head: [['Brand', 'Total Sales']],
            body: <?php echo json_encode(array_map(function($brand) {
                return [$brand['brand_name'], "PHP " . number_format($brand['total_sales'], 2)];
            }, $salesPerBrand)); ?>
        });

        // Save the PDF
        doc.save("Sale_Report.pdf");
    };
});
</script>

</body>
</html>