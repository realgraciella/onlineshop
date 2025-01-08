<?php
session_start();

if (!isset($_SESSION['receipt_data'])) {
    header('Location: admin_pua_receipt.php');
    exit();
}

$receiptData = $_SESSION['receipt_data'];
$checkoutData = $receiptData['checkoutData'];
$totalAmount = $receiptData['totalAmount'];
$sale_date = $receiptData['sale_date'];

unset($_SESSION['receipt_data']); // Clear session data after processing

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <script>
        const { jsPDF } = window.jspdf;

        // Receipt data from PHP
        const data = <?php echo json_encode([
            'customerName' => $_SESSION['username'], // Assuming the customer's name is the logged-in username
            'products' => $checkoutData,
            'totalAmount' => $totalAmount,
            'sale_date' => $sale_date,
        ]); ?>;

        // Create a new PDF document
        const doc = new jsPDF();

        // Title
        doc.setFontSize(16);
        doc.text("Purchase Receipt", 20, 20);

        // Add the receipt data
        let yPosition = 30;
        doc.setFontSize(12);
        doc.text(`Customer: ${data.customerName}`, 20, yPosition);
        yPosition += 10;

        // Table Header
        doc.text("Product Name", 20, yPosition);
        doc.text("Quantity", 120, yPosition);
        doc.text("Price", 160, yPosition);
        doc.text("Total", 200, yPosition);
        yPosition += 10;

        // Loop through the products and add to the PDF
        let totalAmount = 0;
        data.products.forEach(item => {
            doc.text(item.product_name, 20, yPosition);
            doc.text(item.quantity.toString(), 120, yPosition);
            doc.text("PHP " + item.price.toFixed(2), 160, yPosition);
            doc.text("PHP " + (item.quantity * item.price).toFixed(2), 200, yPosition);
            totalAmount += item.quantity * item.price;
            yPosition += 10;
        });

        // Total amount
        yPosition += 10;
        doc.text(`Total Amount: PHP ${totalAmount.toFixed(2)}`, 20, yPosition);

        // Save the PDF
        doc.save("purchase_receipt.pdf");
    </script>
</body>
</html>
