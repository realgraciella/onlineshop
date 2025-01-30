<?php
// Include the database connection
include 'database/db_connect.php'; 

// Check for products that have passed the due date and insert them into to_pay_products
$checkDueProductsSql = "SELECT tp.product_id, tp.agent_username, tp.product_value, tp.price_per_variation, tp.quantity, tp.total_amount, tp.due_date 
                         FROM to_return_products tp 
                         WHERE tp.due_date < NOW() - INTERVAL 1 DAY";

$checkStmt = $pdo->prepare($checkDueProductsSql);
$checkStmt->execute();
$dueProducts = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

if ($dueProducts) {
    $insertSql = "INSERT INTO to_pay_products (username, product_id, variation_value, price_per_variation, quantity, total_amount, due_date, payment_status, created_at, updated_at) 
                  VALUES (:username, :product_id, :variation_value, :price_per_variation, :quantity, :total_amount, :due_date, 'pending', NOW(), NOW())";

    $insertStmt = $pdo->prepare($insertSql);

    foreach ($dueProducts as $product) {
        $insertStmt->execute([
            ':username' => $product['agent_username'],
            ':product_id' => $product['product_id'],
            ':variation_value' => $product['product_value'],
            ':price_per_variation' => $product['price_per_variation'],
            ':quantity' => $product['quantity'],
            ':total_amount' => $product['total_amount'],
            ':due_date' => $product['due_date']
        ]);
    }
}
// ...

// Fetch products grouped by agent_username
$sql = "SELECT tp.agent_username, tp.product_id, tp.product_value, tp.price_per_variation, tp.quantity, tp.total_amount, tp.sale_date, tp.due_date, p.product_name 
        FROM to_return_products tp 
        JOIN products p ON tp.product_id = p.product_id 
        GROUP BY tp.agent_username, tp.product_id";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        .return-container{
            margin-top: 150px;
            padding: 20px;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="return-container mt-5">
    <h2>Return Products</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Agent Username</th>
                <th>Product Name</th>
                <th>Product Value</th>
                <th>Price per Variation</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Sale Date</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#returnModal" data-username="<?php echo htmlspecialchars($row['agent_username']); ?>" data-product-id="<?php echo htmlspecialchars($row['product_id']); ?>" data-product-value="<?php echo htmlspecialchars($row['product_value']); ?>" data-price-per-variation="<?php echo htmlspecialchars($row['price_per_variation']); ?>" data-quantity="<?php echo htmlspecialchars($row['quantity']); ?>">
                                <?php echo htmlspecialchars($row['agent_username']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_value']); ?></td>
                        <td><?php echo htmlspecialchars($row['price_per_variation']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td><?php echo date('F j, Y', strtotime($row['sale_date'])); ?></td>
                        <td><?php echo date('F j, Y', strtotime($row['due_date'])); ?></td>

                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#toPayModal" data-product-id="<?php echo htmlspecialchars($row['product_id']); ?>" data-product-value="<?php echo htmlspecialchars($row['product_value']); ?>" data-price-per-variation="<?php echo htmlspecialchars($row['price_per_variation']); ?>" data-quantity="<?php echo htmlspecialchars($row['quantity']); ?>" data-due-date="<?php echo htmlspecialchars($row['due_date']); ?>">
                                To Pay
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- To Pay Modal -->
<div class="modal fade" id="toPayModal" tabindex="-1" role="dialog" aria-labelledby="toPayModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toPayModalLabel">Confirm To Pay</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="toPayMessage"></p>
                <p id="dueDate"></p>
                <p id="daysOverdue"></p>
                <button type="button" class="btn btn-primary" id="confirmToPay">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#toPayModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('product-id');
        var productValue = button.data('product-value');
        var pricePerVariation = button.data('price-per-variation');
        var quantity = button.data('quantity');
        var dueDate = button.data('due-date');

        var modal = $(this);
        modal.find('#toPayMessage').text('Are you sure you want to mark this product as "To Pay"?');
        modal.find('#dueDate').text('Due Date: ' + dueDate);
        var daysOverdue = calculateDaysOverdue(dueDate);
        modal.find('#daysOverdue').text('Days Overdue: ' + daysOverdue);

        $('#confirmToPay').off('click');
        $('#confirmToPay').on('click', function() {
            toPay(productId, productValue, pricePerVariation, quantity, dueDate);
        });
    });

    function calculateDaysOverdue(dueDate) {
        var dueDateObj = new Date(dueDate);
        var today = new Date();
        var timeDiff = today.getTime() - dueDateObj.getTime();
        var daysOverdue = Math.ceil(timeDiff / (1000 * 3600 * 24));
        return daysOverdue;
    }

    function toPay(productId, productValue, pricePerVariation, quantity, dueDate) {
        $.ajax({
            type: 'POST',
            url: 'insert_to_pay.php',
            data: {
                product_id: productId,
                product_value: productValue,
                price_per_variation: pricePerVariation,
                quantity: quantity,
                due_date: dueDate
            },
            success: function(data) {
                console.log('Response:', data);
                if (data == 'success') {
                    alert('Product marked as "To Pay" successfully!');
                    location.reload();
                } else {
                    alert('Error marking product as "To Pay"!');
                }
            }
        });
    }
</script>

</body>
</html>

<?php
// Close the PDO connection
$pdo = null;
?>