<?php
session_start();
include 'database/db_connect.php';

// Initialize variables
$salesData = [];
$errorMessage = '';
$successMessage = '';
$sale_date = date('Y-m-d H:i:s'); // Set sale_date to current time
$customer_name = ''; // Initialize customer name to an empty string
$due_date = date('Y-m-d H:i:s', strtotime($sale_date . ' +30 days')); // Initialize due_date

// Check if the user is logged in
$username = $_SESSION['username'] ?? null;
if (!$username) {
    $errorMessage = "You must be logged in to view this page.";
} else {
    // Fetch sales data from the pos table based on the logged-in user
    $pos_query = "SELECT product_name, variation_value, quantity, price, total_amount 
                  FROM pos 
                  WHERE username = ? AND pos_status != 'Completed'";
    $pos_stmt = $pdo->prepare($pos_query);
    $pos_stmt->execute([$username]);
    $salesData = $pos_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are sales data
    if (empty($salesData)) {
        $errorMessage = "No sales data found for the user.";
    } else {
        // Store sales data into store_sales table
        foreach ($salesData as $item) {
            $product_name = $item['product_name'];
            $quantity = $item['quantity'];
            $product_price = $item['price'];
            $total_amount = $item['total_amount'];

            // Insert sale into store_sales table
            $sale_query = "INSERT INTO store_sales (product_id, product_name, product_value, price_per_variation, quantity, total_amount, sale_date) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sale_stmt = $pdo->prepare($sale_query);
            if (!$sale_stmt->execute([null, $product_name, $item['variation_value'], $product_price, $quantity, $total_amount, $sale_date])) {
                $errorInfo = $sale_stmt->errorInfo();
                $errorMessage = "Error inserting sale: " . $errorInfo[2];
            }
        }

        // Update pos_status to "Completed"
        $update_pos_query = "UPDATE pos SET pos_status = 'Completed' WHERE username = ?";
        $update_pos_stmt = $pdo->prepare($update_pos_query);
        $update_pos_stmt->execute([$username]);

        // Store recent purchases
        foreach ($salesData as $item) {
            $purchase_query = "INSERT INTO purchases (rcprod_name, purchase_date, quantity, created_at) 
                               VALUES (?, NOW(), ?, NOW())";
            $purchase_stmt = $pdo->prepare($purchase_query);
            $purchase_stmt->execute([$item['product_name'], $item['quantity']]);
        }

        // If you have a way to get the customer name, set it here
        // For example, if it's passed from a previous page, you can do:
        // $customer_name = $_POST['customer_name'] ?? ''; // Uncomment if applicable
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt</title>
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .receipt-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            width: 80%;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 1rem;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .total {
            font-weight: bold;
        }
        .due-date {
            margin-top: 1rem;
            font-weight: bold;
            text-align : right;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 150px; /* Adjust width as needed */
            height: auto; /* Maintain aspect ratio */
        }
        .customer-name {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
    <div class="receipt-container">
        <img src="assets/img/logo/6.png" alt="Logo" class="logo">
        <h2>Receipt</h2>

        <div class="customer-name">
            <strong>Customer Name / Agent:</strong>
            <p><?= htmlspecialchars($customer_name) ?></p>
        </div>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Variation</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesData as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['variation_value']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>PHP <?= number_format(htmlspecialchars($item['price']), 2) ?></td>
                        <td>PHP <?= number_format(htmlspecialchars($item['total_amount']), 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <strong>Total Amount:</strong> PHP <?= number_format(array_sum(array_column($salesData, 'total_amount')), 2) ?>
        </div>

        <div class="due-date">
            <strong>Due Date:</strong> <?= date('Y-m-d', strtotime($due_date)) ?>
        </div>

        <button class="btn btn-success mt-3" id="downloadBtn">Download Receipt</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('downloadBtn').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const customerName = document.getElementById('customerName').value;

            // Add logo
            const logoUrl = 'assets/img/logo/4.2.png'; // Update with the correct path to your logo image
            const img = new Image();
            img.src = logoUrl;

            img.onload = function () {
                const imgWidth = 50;
                const imgHeight = (img.height / img.width) * imgWidth;
                const imgX = (doc.internal.pageSize.width - imgWidth) / 2;
                const imgY = 10;

                doc.addImage(img, 'PNG', imgX, imgY, imgWidth, imgHeight);
                doc.setFontSize(16);
                doc.text("Receipt", doc.internal.pageSize.width / 2, imgY + imgHeight + 10, { align: 'center' });

                // Prepare table data
                const tableHeaders = ["Product Name", "Variation", "Quantity", "Price", "Total"];
                const rows = [];

                document.querySelectorAll('table tbody tr').forEach(row => {
                    const rowData = [];
                    row.querySelectorAll('td').forEach(cell => {
                        rowData.push(cell.innerText);
                    });
                    rows.push(rowData);
                });

                // Add customer name
                doc.text(`Customer Name: ${customerName}`, 10, imgY + imgHeight + 30);

                // Add table to PDF
                doc.autoTable({
                    head: [tableHeaders],
                    body: rows,
                    startY: imgY + imgHeight + 40, // Adjust start position below the logo and title
                    theme: 'grid',
                    headStyles: { fillColor : [22, 160, 133] }, // Customize header color
                });

                // Add total amount and due date
                const totalAmount = <?= json_encode(number_format(array_sum(array_column($salesData, 'total_amount')), 2)) ?>;
                const dueDate = '<?= date('Y-m-d', strtotime($due_date)) ?>';
                doc.text(`Total Amount: PHP ${totalAmount}`, 10, doc.lastAutoTable.finalY + 10);
                doc.text(`Due Date: ${dueDate}`, 10, doc.lastAutoTable.finalY + 20);

                // Save the PDF
                doc.save('receipt.pdf');
            };
        });
    </script>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>