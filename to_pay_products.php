<?php
// Include the database connection file
include 'database/db_connect.php';

try {
    // Prepare the SQL query
    $sql = "
        SELECT * FROM to_pay_products
        WHERE payment_status = 'unpaid'
        AND due_date < NOW() - INTERVAL 30 DAY
    ";

    // Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}

// Close the connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Unpaid Products</title>
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
         .inventory-container {
            width: 80%;
            margin: 80px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="inventory-container">
<h1>Overdue Unpaid Products</h1>

<?php if (isset($error_message)): ?>
    <p><?php echo $error_message; ?></p>
<?php else: ?>
    <?php if (count($results) > 0): ?>
        <table>
            <tr>
                <th>To Pay ID</th>
                <th>Username</th>
                <th>Product ID</th>
                <th>Variation Value</th>
                <th>Price Per Variation</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Due Date</th>
                <th>Payment Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['to_pay_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['variation_value']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_per_variation']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No overdue unpaid products found.</p>
    <?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>