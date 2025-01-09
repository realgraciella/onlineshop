<?php
session_start();
include 'database/db_connect.php';

$username = '';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

// Initialize variables
$checkoutData = [];
$successMessage = '';
$errorMessage = '';
$abortMessage = '';

// Fetch active sales agents from the users table
$agents_query = "SELECT user_id, username FROM users WHERE role = 'Sales Agent'";
$agents_stmt = $pdo->prepare($agents_query);
$agents_stmt->execute();
$salesAgents = $agents_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all products with their first variation price and category name
$query = "SELECT p.*, pv.price_per_variation AS variation_price, c.category_name 
          FROM products p 
          LEFT JOIN product_variations pv ON p.product_id = pv.product_id 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          GROUP BY p.product_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if data is received from the previous page
if (isset($_GET['data'])) {
    $checkoutData = json_decode($_GET['data'], true);
} else {
    $checkoutData = []; // Initialize as an empty array if no data is received
}

// Handle the sale if the form is submitted
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $agent_id = $_POST['sales_agent'] ?? null;
//     $customer_name = $_POST['customer_name'] ?? null;

//     // Complete Purchase Logic
//     if (isset($_POST['complete_purchase'])) {
//         $totalAmount = 0;
//         $sale_date = date('Y-m-d H:i:s');
//         $due_date = date('Y-m-d H:i:s', strtotime('+30 days'));

//         $receiptContent = "Receipt\n";
//         $receiptContent .= "Date: $sale_date\n";
//         $receiptContent .= "Sales Agent: $agent_id\n\n";
//         $receiptContent .= "Products:\n";

//         foreach ($checkoutData as $item) {
//             $product_name = $item['product_name'];
//             $quantity = $item['quantity'];
//             $variation_id = $item['variation_id'];
//             $product_price = $item['price'];
//             $total_amount = $product_price * $quantity;

//             $product_query = "SELECT * FROM products WHERE product_name = ?";
//             $product_stmt = $pdo->prepare($product_query);
//             $product_stmt->execute([$product_name]);
//             $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

//             $variation_query = "SELECT * FROM product_variations WHERE variation_id = ?";
//             $variation_stmt = $pdo->prepare($variation_query);
//             $variation_stmt->execute([$variation_id]);
//             $variation = $variation_stmt->fetch(PDO::FETCH_ASSOC);

//             if ($product && $variation && $variation['stock_per_variation'] >= $quantity) {
//                 $sale_query = "INSERT INTO to_return_products 
//                                (agent_id, agent_username, username, product_id, product_value, price_per_variation, quantity, total_amount, sale_date, due_date) 
//                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//                 $sale_stmt = $pdo->prepare($sale_query);
//                 $sale_stmt->execute([
//                     $agent_id, $_POST['sales_agent_username'], $_SESSION['username'], $product['product_id'], $variation['variation_value'], 
//                     $product_price, $quantity, $total_amount, $sale_date, $due_date
//                 ]);

//                 $update_product_query = "UPDATE products SET stock_level = stock_level - ? WHERE product_name = ?";
//                 $update_product_stmt = $pdo->prepare($update_product_query);
//                 $update_product_stmt->execute([$quantity, $product_name]);

//                 $update_variation_query = "UPDATE product_variations SET stock_per_variation = stock_per_variation - ? WHERE variation_id = ?";
//                 $update_variation_stmt = $pdo->prepare($update_variation_query);
//                 $update_variation_stmt->execute([$quantity, $variation_id]);

//                 $totalAmount += $total_amount;
//                 $receiptContent .= "- $product_name x $quantity @ PHP $product_price each = PHP $total_amount\n";
//             } else {
//                 $errorMessage = "Insufficient stock for $product_name.";
//             }
//         }

//         if (empty($errorMessage)) {
//             $receiptContent .= "\nTotal Amount: PHP $totalAmount\n";

//             // Generate Receipt File
//             $receiptFile = 'receipt_' . time() . '.txt';
//             file_put_contents($receiptFile, $receiptContent);

//             // Force Download
//             header('Content-Type: application/octet-stream');
//             header('Content-Disposition: attachment; filename="' . $receiptFile . '"');
//             header('Content-Length: ' . filesize($receiptFile));
//             readfile($receiptFile);

//             // Redirect to Viewing Page
//             header('Location: to_return_pro.php');
//             exit();
//         }
//     }
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_purchase'])) {
    // Example: sale date and due date

    $sales_agent_id = $_POST['sales_agent_id'];
    $sales_agent_username = $_POST['sales_agent_username'];

    $saleDate = date('Y-m-d H:i:s'); // Current date and time
    $dueDate = date('Y-m-d H:i:s', strtotime('+7 days')); // Due date set 7 days after sale date

    // Retrieve the checkout data (or displayData) from the form
    $displayData = json_decode($_POST['checkout_data'], true);

    // Loop through the data and insert each item
    foreach ($displayData as $item) {
        $agentId = $item['agent_id'];
        $agentUsername = $item['agent_username'];
        $productId = $item['product_id'];
        $productValue = $item['product_value'];
        $pricePerVariation = $item['price_per_variation'];
        $quantity = $item['quantity'];
        $totalAmount = $pricePerVariation * $quantity;

        // Prepare SQL statement to insert data into `to_return_products`
        $sql = "INSERT INTO to_return_products 
                (agent_id, agent_username, username, product_id, product_value, price_per_variation, quantity, total_amount, sale_date, due_date)
                VALUES 
                (:agent_id, :agent_username, :username, :product_id, :product_value, :price_per_variation, :quantity, :total_amount, :sale_date, :due_date)";

        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':agent_id', $sales_agent_id);
        $stmt->bindParam(':agent_username', $sales_agent_username);

        $user = $_SESSION['username'];

        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':product_value', $productValue);
        $stmt->bindParam(':price_per_variation', $pricePerVariation);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':total_amount', $totalAmount);
        $stmt->bindParam(':sale_date', $saleDate);
        $stmt->bindParam(':due_date', $dueDate);

        // Execute the statement
        $stmt->execute();
    }

    $display_query = "DELETE FROM product_under_agents WHERE username = ? AND pua_status = 'On Process'";
    $display_stmt = $pdo->prepare($display_query);
    $display_stmt->execute([$_SESSION['username']]);

    header('Location: to_return_pro.php');

    // After inserting the data, you can proceed with any other actions (e.g., redirect or show a success message).
    echo "Purchase completed successfully!";
}



// Fetch data from product_under_agents for display where pua_status is 'On Process'
$display_query = "SELECT * FROM product_under_agents WHERE username = ? AND pua_status = 'On Process'";
$display_stmt = $pdo->prepare($display_query);
$display_stmt->execute([$_SESSION['username']]); // Assuming username is stored in session
$displayData = $display_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Under Agent Checkout</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .checkout-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        h2 {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 30px;
            text-align: center;
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #008a00;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: bold;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #008a00;
            border-color: #008a00;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: bold;
            border-radius: 5px;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .table {
            margin-top: 2rem;
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .alert {
            margin-top: 1rem;
            font-weight: bold;
        }
        .alert .btn-close {
            opacity: 0.8;
        }
        .text-end {
            font-weight: bold;
            color: #333;
            font-size: 1.2rem;
        }
        .select2-container {
            width: 100% !important }
        .card-header {
            background-color: #343a40;
            color: white;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="checkout-container">
        <h2>Product Under Agent Checkout</h2>

        <!-- Select Sales Agent Form -->
        <form method="POST" action="" class="mt-4">
            <div class="mb-3">
                <label for="sales_agent" class="form-label">Select Active Sales Agent</label>
                <select name="sales_agent" id="sales_agent" class="form-select select2">
                    <option value="">-- Select an Agent --</option>
                    <?php foreach ($salesAgents as $agent): ?>
                        <option value="<?= htmlspecialchars($agent['user_id']) ?>" data-username="<?= htmlspecialchars($agent['username']) ?>">
                            <?= htmlspecialchars($agent['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="checkout_data" value='<?= htmlspecialchars(json_encode($displayData)) ?>'>
            <input type="hidden" name="complete_purchase" value="1">
            <input type="hidden" name="sales_agent_id" id="sales_agent_id">
            <input type="hidden" name="sales_agent_username" id="sales_agent_username">


        <!-- Success and Error Messages -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($successMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Product and Agent List Table -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($displayData as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_value']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>PHP <?= number_format(htmlspecialchars($item['price_per_variation']), 2) ?></td>
                    <td>PHP <?= number_format($item['price_per_variation'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>

        
        <button type="submit" class="btn btn-primary w-100">Complete Purchase</button>
        </form>

        <div class="text-end">
            <strong>Total Amount:</strong> PHP <?= number_format(array_sum(array_map(function($item) {
                return $item['price_per_variation'] * $item['quantity'];
            }, $displayData)), 2) ?>
        </div>

        <form method="POST" action="admin_pua_out.php" class="mt-4">
            <input type="hidden" name="abort_checkout" value="1">
            <button type="submit" class="btn btn-danger w-100">Cancel Checkout</button>
        </form>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- <scrip>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select an Agent --",
                allowClear: true
            });

            $('#sales_agent').change(function() {
                var selectedOption = $(this).find('option:selected');
                var agentUsername = selectedOption.data('username');
                $('.agent-username').each(function() {
                    $(this).text(agentUsername);
                    $(this).data('agent-id', selectedOption.val());
                });

                // Update product_under_agents table
                $.ajax({
                    url: 'update_product_under_agents.php',
                    method: 'POST',
                    data: {
                        agent_id: selectedOption.val(),
                        username: '<?= $_SESSION['username'] ?>'
 },
                    success: function(response) {
                        // Handle success response if needed
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating product_under_agents:', error);
                    }
                });
            });
        });

        // Attach an event listener to the sales agent dropdown -->
        <script>
    // Function to update the hidden input fields when a sales agent is selected
    function updateSalesAgentInfo() {
        // Get the selected option from the dropdown
        var selectedOption = document.getElementById('sales_agent').selectedOptions[0];

        // Check if a valid agent is selected
        if (selectedOption.value !== '') {
            var agentId = selectedOption.value; // Get agent ID
            var agentUsername = selectedOption.getAttribute('data-username'); // Get agent username

            // Update the hidden input fields with the selected agent's data
            document.getElementById('sales_agent_id').value = agentId;
            document.getElementById('sales_agent_username').value = agentUsername;
        } else {
            // Clear the hidden inputs if no agent is selected
            document.getElementById('sales_agent_id').value = '';
            document.getElementById('sales_agent_username').value = '';
        }
    }

    // Add an event listener to the select dropdown to update hidden fields when selection changes
    document.getElementById('sales_agent').addEventListener('change', updateSalesAgentInfo);

    // Optional: Ensure the values are updated on page load if the form is already populated
    window.onload = updateSalesAgentInfo;

</script>
</body>
</html>