<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmshop"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
        $payment_status = "Paid"; // Set payment status to "Paid"

        $update_query = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('si', $payment_status, $order_id);

        if ($stmt->execute()) {
            // Redirect to the receipt page
            header("Location: print_receipt.php?order_id=" . $order_id);
            exit();
        } else {
            echo "<script>alert('Error updating payment status: " . $conn->error . "');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['order_id'])) {
        // Handle order status update
        $order_id = $_POST['order_id'];
        $order_status = $_POST['order_status'];

        $update_status_query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_status_query);
        $stmt->bind_param('si', $order_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch the order details with search functionality
$sql = "
    SELECT 
        o.order_id, 
        o.username, 
        o.name, 
        o.items, 
        o.total_amount, 
        o.order_date, 
        o.order_status, 
        o.payment_method, 
        o.payment_status
    FROM 
        orders o
    WHERE 
        o.username LIKE ? OR 
        o.name LIKE ? OR 
        o.order_id LIKE ?
    ORDER BY 
        o.username, o.order_date";

$stmt = $conn->prepare($sql);
$search_param = '%' . $search_query . '%';
$stmt->bind_param('ssi', $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order List</title>
    <link href="assets/img/logo/2.png" rel="icon">
    
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
        h2 {
            margin-top: 80px auto;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        .status-pending {
            color: #ffe165;
        }
        .status-processing {
            color: #1490a6;
        }
        .status-completed {
            color: #4CAF50;
        }
        .status-cancelled {
            color: #f44336;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
 <div class="container mt-5">
    <h2>Order List</h2>

    <!-- Search Form -->
    <form method="post" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Order ID, Username, or Name" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Items</th>
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
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['items']) . "</td>";
                    echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td class='status-" . strtolower($row['order_status']) . "'>" . htmlspecialchars($row['order_status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
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


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('#modifyPaymentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var orderId = button.data('order-id');
        var paymentStatus = button.data('payment-status');
        var modal = $(this);
        modal.find('#modal-order-id').val(orderId);
        modal.find('#modal-payment-status').val(paymentStatus);
    });
</script>
</body>
</html>