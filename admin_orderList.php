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

// Update payment status when modified by admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_status'], $_POST['order_id'])) {
    $payment_status = $_POST['payment_status'];
    $order_id = $_POST['order_id'];

    $update_query = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $payment_status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating payment status: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Fetch the order details
$sql = "
    SELECT 
        o.order_id, 
        o.username, 
        o.name, 
        o.total_amount, 
        o.order_date, 
        o.order_status, 
        o.shipping_address, 
        o.payment_method, 
        o.payment_status
    FROM 
        orders o
    ORDER BY 
        o.username, o.order_date";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order List</title>
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
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="container mt-5">
    <h2>Order List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Total Amount</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Shipping Address</th>
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
                    echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<select name='order_status' class='form-control' onchange='this.form.submit()'>";
                    echo "<option value='Pending'" . ($row['order_status'] === 'Pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='Processing'" . ($row['order_status'] === 'Processing' ? ' selected' : '') . ">Processing</option>";
                    echo "<option value='Completed'" . ($row['order_status'] === 'Completed' ? ' selected' : '') . ">Completed</option>";
                    echo "<option value='Cancelled'" . ($row['order_status'] === 'Cancelled' ? ' selected' : '') . ">Cancelled</option>";
                    echo "</select>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "' />";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['shipping_address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
                    echo "<td><button class='btn btn-primary' data-toggle='modal' data-target='#modifyPaymentModal' data-order-id='" . $row['order_id'] . "' data-payment-status='" . $row['payment_status'] . "'>Modify</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modify Payment Status Modal -->
<div class="modal fade" id="modifyPaymentModal" tabindex="-1" aria-labelledby="modifyPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyPaymentModalLabel">Modify Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="modal-order-id">
                    <div class="form-group">
                        <label for="payment-status">Payment Status</label>
                        <select name="payment_status" id="modal-payment-status" class="form-control">
                            <option value="Unpaid">Unpaid</option>
                            <option value="Pending">Pending</option>
                            <option value="Partial Payment">Partial Payment</option>
                            <option value="Complete Payment">Complete Payment</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
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

<?php
$conn->close();
?>
