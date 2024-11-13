<?php
include 'database/db_connect.php'; // Include database connection

// Fetch product feedback
$queryProduct = "SELECT * FROM product_feedback ORDER BY feedback_date DESC";
$stmtProduct = $pdo->prepare($queryProduct);
$stmtProduct->execute();
$productFeedbacks = $stmtProduct->fetchAll(PDO::FETCH_ASSOC);

// Fetch system feedback
$querySystem = "SELECT * FROM system_feedback ORDER BY feedback_date DESC";
$stmtSystem = $pdo->prepare($querySystem);
$stmtSystem->execute();
$systemFeedbacks = $stmtSystem->fetchAll(PDO::FETCH_ASSOC);

// Handle admin comments (Optional feature)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbackId = $_POST['feedback_id'];
    $feedbackType = $_POST['feedback_type'];
    $comment = $_POST['comment'];
    $adminId = 1; // Example admin ID, replace with session-based or dynamic data
    
    // Store the admin response if needed
    // If you wish to store the admin's response, you would insert this into another table (admin_responses) or extend the current table with a response column.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Feedback Management</title>
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Minified CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet"> <!-- Ensure minified CSS -->

    <!-- Deferred JS loading -->
    <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script defer src="assets/vendor/aos/aos.js"></script>
    <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script defer src="assets/js/admin.js"></script>

    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Arial', sans-serif;
            padding: 30px;
        }
        .feedback-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 15px;
        }
        .feedback-section {
            background: #ffffff;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .feedback-section:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .feedback-section h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .table-responsive {
            margin-bottom: 20px;
        }
        .table th {
            background-color: #4CAF50;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #dee2e6;
            color: white;
            font-size: 16px;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            color: #5a5a5a;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
            font-size: 14px;
        }
        .comment-form button {
            margin-top: 12px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .comment-form button:hover {
            background-color: #008a00;
        }
        .no-feedback {
            text-align: center;
            color: #666;
            font-size: 18px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2c3e50;
        }
        .header-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
        }
        .header-logo img {
            max-width: 100px;
        }
        @media (max-width: 768px) {
            .feedback-section {
                padding: 15px;
            }
            .feedback-section h2 {
                font-size: 24px;
            }
            .table th, .table td {
                font-size: 14px;
                padding: 8px;
            }
            .comment-form button {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
        @media (max-width: 576px) {
            h1 {
                font-size: 28px;
            }
            .comment-form textarea {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="feedback-container">
        <h1 class="text-center mb-4">Admin Feedback Management</h1>

        <!-- Product Feedback Section -->
        <div class="feedback-section">
            <h2>Product Feedback</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Product ID</th>
                            <th>Customer Name</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($productFeedbacks) > 0): ?>
                            <?php foreach ($productFeedbacks as $feedback): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($feedback['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['feedback_date']); ?></td>
                                    <td>
                                        <form method="POST" class="comment-form">
                                            <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedback_id']; ?>">
                                            <input type="hidden" name="feedback_type" value="product">
                                            <textarea name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No product feedback found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Feedback Section -->
        <div class="feedback-section">
            <h2>System Feedback</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Customer Name</th>
                            <th>Feedback Type</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($systemFeedbacks) > 0): ?>
                            <?php foreach ($systemFeedbacks as $feedback): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($feedback['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['feedback_type']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['feedback_date']); ?></td>
                                    <td>
                                        <form method="POST" class="comment-form">
                                            <input type="hidden" name="feedback_id" value="<?php echo $feedback['feedback_id']; ?>">
                                            <input type="hidden" name="feedback_type" value="system">
                                            <textarea name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No system feedback found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

