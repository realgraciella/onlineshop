<?php
// Database connection
$host = 'localhost';
$db = 'dmshop1'; 
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products grouped by agent_username
$sql = "SELECT tp.username, tp.product_id, tp.product_value, tp.price_per_variation, tp.quantity, p.product_name 
        FROM to_return_products tp 
        JOIN products p ON tp.product_id = p.product_id 
        GROUP BY tp.username, tp.product_id";
$result = $conn->query($sql);
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#returnModal" data-username="<?php echo $row['username']; ?>" data-product-id="<?php echo $row['product_id']; ?>" data-product-value="<?php echo $row['product_value']; ?>" data-price-per-variation="<?php echo $row['price_per_variation']; ?>" data-quantity="<?php echo $row['quantity']; ?>">
                                <?php echo $row['username']; ?>
                            </a>
                        </td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['product_value']; ?></td>
                        <td><?php echo $row['price_per_variation']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#returnModal">Return</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="returnForm">
                    <div class="form-group">
                        <label for="productValue">Product Value</label>
                        <input type="text" class="form-control" id="productValue" readonly>
                    </div>
                    <div class="form-group">
                        <label for="pricePerVariation">Price per Variation</label>
                        <input type="text" class="form-control" id="pricePerVariation" readonly>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity to Return</label>
                        <input type="number" class="form-control" id="quantity" min="1" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="totalAmount">Total Amount</label>
                        <input type="text" class="form-control" id="totalAmount" readonly>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="proceedReturn()">Proceed & Print Receipt</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#returnModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var username = button.data('username');
        var productId = button.data('product-id');
        var productValue = button.data('product-value');
        var pricePerVariation = button.data('price-per-variation');
        var quantity = button.data('quantity');

        var modal = $(this);
        modal.find('#productValue').val(productValue);
        modal.find('#pricePerVariation').val(pricePerVariation);
        modal.find('#quantity').val(quantity);
        calculateTotal();
    });

    function calculateTotal() {
        var price = parseFloat($('#pricePerVariation').val());
        var quantity = parseInt($('#quantity').val()) || 0;
        var total = price * quantity;
        $('#totalAmount').val(total.toFixed(2));
    }

    function proceedReturn() {
        // Logic to update stock_level in products table and insert into sales table
        // This will require an AJAX call or form submission to handle the database update
        alert('Proceeding with return and printing receipt...');
    }
</script>

</body>
</html>

<?php
$conn->close();
?>