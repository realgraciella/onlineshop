<?php
include 'database/db_connect.php';

// Initialize search variable
$search_query = '';

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Check if the form is submitted for printing receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['print_receipt'])) {
        $order_id = $_POST['order_id'];
        $payment_status = "Paid";

        $update_query = "UPDATE orders SET payment_status = :payment_status WHERE order_id = :order_id";
        $stmt = $pdo->prepare($update_query);
        
        if ($stmt->execute([':payment_status' => $payment_status, ':order_id' => $order_id])) {
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename=receipt_$order_id.pdf");
            echo "Receipt for Order ID: $order_id\nPayment Status: $payment_status";
            exit();
        } else {
            echo "<script>alert('Error updating payment status.');</script>";
        }
    } elseif (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];
        $order_status = $_POST['order_status'];
        $payment_method = $_POST['payment_method'];

        $update_status_query = "UPDATE orders SET order_status = :order_status, payment_method = :payment_method WHERE order_id = :order_id";
        $stmt = $pdo->prepare($update_status_query);
        $stmt->execute([
            ':order_status' => $order_status,
            ':payment_method' => $payment_method,
            ':order_id' => $order_id
        ]);
    }
}

// Fetch the order details with search functionality
$sql = "
    SELECT 
        o.order_id, 
        o.username, 
        CONCAT(a.agent_fname, ' ', a.agent_mname, ' ', a.agent_lname) AS agent_full_name,
        o.total_amount, 
        DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date, 
        o.order_status, 
        o.payment_method, 
        o.payment_status,
        GROUP_CONCAT(o.variation_value SEPARATOR ', ') AS products
    FROM 
        orders o
    LEFT JOIN 
        agents a ON o.username = a.agent_user
    WHERE 
        o.username LIKE :search OR 
        o.order_id LIKE :search
    GROUP BY 
        o.order_id
    ORDER BY 
        o.username, o.order_date";

$stmt = $pdo->prepare($sql);
$search_param = '%' . $search_query . '%';
$stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order List</title>
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css"> 
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .container h2 {
            margin-top: 100px;
 }
        .status-pending { color: #ffe165; }
        .status-processing { color: #1490a6; }
        .status-completed { color: #4CAF50; }
        .status-cancelled { color: #f44336; }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="container mt-5">
    <h2>Order List</h2>

    <form method="post" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Order ID or Username" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Agent Full Name</th>
                <th>Products</th>
                <th>Total Amount</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($result) > 0) {
                foreach ($result as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['agent_full_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['products']) . "</td>";
                    echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "' />";
                    echo "<select name='order_status' class='form-control' onchange='this.form.submit()'>";
                    echo "<option value='pending'" . ($row['order_status'] == 'pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='processing'" . ($row['order_status'] == 'processing' ? ' selected' : '') . ">Processing</option>";
                    echo "<option value='completed'" . ($row['order_status'] == 'completed' ? ' selected' : '') . ">Completed</option>";
                    echo "<option value='cancelled'" . ($row['order_status'] == 'cancelled' ? ' selected' : '') . ">Cancelled</option>";
                    echo "</select>";
                    echo "</form></td>";
                    echo "<td>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "' />";
                    echo "<select name='payment_method' class='form-control' onchange='this.form.submit()'>";
                    echo "<option value='gcash'" . ($row['payment_method'] == 'gcash' ? ' selected' : '') . ">GCash</option>";
                    echo "<option value='cash'" . ($row['payment_method'] == 'cash' ? ' selected' : '') . ">Cash</option>";
                    echo "</select>";
                    echo "</form></td>";
                    echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
                    echo "<td>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "' />";
                    echo "<button class='btn btn-success' type='submit' name='print_receipt'>Print Receipt</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>