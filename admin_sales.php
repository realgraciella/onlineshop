<?php
include 'database/db_connect.php';

// Combined queries to fetch sales data from both the `sales` and `store_sales` tables
$queries = [
    'weekly' => "
        SELECT SUM(total_sales) AS total_sales, week FROM (
            SELECT SUM(sale_amount) AS total_sales, WEEK(sale_date) AS week FROM sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 WEEK 
            GROUP BY week
            UNION ALL
            SELECT SUM(total_amount) AS total_sales, WEEK(sale_date) AS week FROM store_sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 WEEK 
            GROUP BY week
        ) AS combined_sales
        GROUP BY week
        ORDER BY week DESC",
        
    'monthly' => "
        SELECT SUM(total_sales) AS total_sales, month FROM (
            SELECT SUM(sale_amount) AS total_sales, MONTH(sale_date) AS month FROM sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH 
            GROUP BY month
            UNION ALL
            SELECT SUM(total_amount) AS total_sales, MONTH(sale_date) AS month FROM store_sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH 
            GROUP BY month
        ) AS combined_sales
        GROUP BY month
        ORDER BY month DESC",
        
    'annually' => "
        SELECT SUM(total_sales) AS total_sales, year FROM (
            SELECT SUM(sale_amount) AS total_sales, YEAR(sale_date) AS year FROM sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR 
            GROUP BY year
            UNION ALL
            SELECT SUM(total_amount) AS total_sales, YEAR(sale_date) AS year FROM store_sales 
            WHERE sale_date >= CURDATE() - INTERVAL 1 YEAR 
            GROUP BY year
        ) AS combined_sales
        GROUP BY year
        ORDER BY year DESC"
];

// Prepare and execute queries
$sales_data = [];
foreach ($queries as $period => $query) {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $sales_data[$period] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Data</title>
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
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            padding: 0;
            margin: 0;
        }
        .sales-container {
            width: 90%;
            margin: 80px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            margin-top: 40px;
            font-size: 1.5rem;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        td {
            background-color: #f9f9f9;
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        tr:hover td {
            background-color: #ddd;
        }
        .print-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 1rem;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .print-btn:hover {
            background-color: #008a00;
        }
        @media print {
            body {
                background-color: white;
            }
            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
<?php include 'admin_header.php'; ?>
    <div class="sales-container">
        <h1>Sales Data</h1>
        <button class="print-btn" onclick="window.print()">Print Sales Data</button>

        <!-- Weekly Combined Sales Data -->
        <h2>Sales This Week</h2>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data['weekly'] as $data): ?>
                    <tr>
                        <td><?php echo "Week " . $data['week']; ?></td>
                        <td><?php echo number_format($data['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Monthly Combined Sales Data -->
        <h2>Sales This Month</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data['monthly'] as $data): ?>
                    <tr>
                        <td><?php echo "Month " . $data['month']; ?></td>
                        <td><?php echo number_format($data['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Annual Combined Sales Data -->
        <h2>Sales This Year</h2>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_data['annually'] as $data): ?>
                    <tr>
                        <td><?php echo $data['year']; ?></td>
                        <td><?php echo number_format($data['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

