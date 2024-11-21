<?php
// Start session and validate username
session_start();
if (!isset($_SESSION['username'])) {
    die("User not logged in."); // Ensure only logged-in users can access
}

// Retrieve the logged-in username from the session
$agent_username = $_SESSION['username'];
var_dump($agent_username); // Debugging: Check if the username is set

// Database connection
$conn = new mysqli("localhost", "root", "", "dmshop");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Query to fetch orders for the logged-in user
$sql = "
    SELECT 
        total_amount,
        order_date,
        order_status,
        payment_method,
        payment_status
    FROM orders
    WHERE username = ? 
    ORDER BY order_date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param('s', $agent_username);
$stmt->execute();
$result = $stmt->get_result();

// Check if there were any errors while executing the query
if ($result === false) {
    die("Query execution failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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
    <h2 class="text-center">My Orders</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Order Status</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if orders exist and display them
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['order_status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No orders found.</td></tr>";
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
$stmt->close();
$conn->close();
?>
