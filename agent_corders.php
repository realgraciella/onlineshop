<?php
include('database/db_connect.php');

// Start session and validate username
session_start();
if (!isset($_SESSION['username'])) {
    die("User not logged in."); // Ensure only logged-in users can access
}

// Retrieve the logged-in username from the session
$client_username = $_SESSION['username'];
var_dump($client_username); // Debugging: Check if the username is set

try {
    // Prepare SQL query to fetch orders for the logged-in customer
    $sql = "
        SELECT 
            order_id,
            product_id,
            variation_value,
            price_per_variation,
            quantity,
            order_status,
            order_date
        FROM orders1
        WHERE client_id = ?
        ORDER BY order_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$client_username]);

    // Fetch all results
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are any orders
    if (!$orders) {
        echo "No orders found.";
    } else {
        foreach ($orders as $order) {
            echo "Order ID: " . htmlspecialchars($order['order_id']) . "<br>";
            echo "Product ID: " . htmlspecialchars($order['product_id']) . "<br>";
            echo "Variation: " . htmlspecialchars($order['variation_value']) . "<br>";
            echo "Price: $" . htmlspecialchars($order['price_per_variation']) . "<br>";
            echo "Quantity: " . htmlspecialchars($order['quantity']) . "<br>";
            echo "Status: " . htmlspecialchars($order['order_status']) . "<br>";
            echo "Date: " . htmlspecialchars($order['order_date']) . "<br><br>";
        }
    }
} catch (PDOException $e) {
    die("Query execution failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Customer Orders</title>
    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/agent.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        h2 {
            margin-top: 20px;
            color: #343a40;
            font-weight: 600;
        }

        .order-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            margin-top: 40px;
            background: #ffffff;
        }

        thead {
            background-color: #007bff;
            color: #fff;
        }

        th, td {
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background-color: #f2f2f2;
        }
    </style>

</head>
<body>
<?php include 'agent_header.php'; ?>

<div class="order-container mt-5">
    <h2 class="text-center">My Customer Orders</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product ID</th>
                    <th>Variation</th>
                    <th>Price per Variation</th>
                    <th>Quantity</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if orders exist and display them
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['order _id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['variation_value']) . "</td>";
                        echo "<td>" . number_format($row['price_per_variation'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['order_status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the statement and connection
$stmt = null;
$pdo = null;
?>