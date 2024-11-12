<?php
include 'database/db_connect.php';

// Insert product feedback
if (isset($_POST['product_feedback_submit'])) {
    $product_id = $_POST['product_id'];
    $customer_name = $_POST['customer_name'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $query = "INSERT INTO product_feedback (product_id, customer_name, rating, comments) 
              VALUES (:product_id, :customer_name, :rating, :comments)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':product_id' => $product_id,
        ':customer_name' => $customer_name,
        ':rating' => $rating,
        ':comments' => $comments
    ]);
}

// Insert system feedback
if (isset($_POST['system_feedback_submit'])) {
    $customer_name = $_POST['customer_name'];
    $feedback_type = $_POST['feedback_type'];
    $comments = $_POST['comments'];

    $query = "INSERT INTO system_feedback (customer_name, feedback_type, comments) 
              VALUES (:customer_name, :feedback_type, :comments)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':customer_name' => $customer_name,
        ':feedback_type' => $feedback_type,
        ':comments' => $comments
    ]);
}

// Fetch product feedbacks
$product_feedback_query = "SELECT * FROM product_feedback WHERE product_id = :product_id ORDER BY feedback_date DESC";
$product_feedback_stmt = $pdo->prepare($product_feedback_query);
$product_feedback_stmt->execute([':product_id' => 1]);  // Example product ID
$product_feedbacks = $product_feedback_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch system feedbacks
$system_feedback_query = "SELECT * FROM system_feedback ORDER BY feedback_date DESC";
$system_feedback_stmt = $pdo->prepare($system_feedback_query);
$system_feedback_stmt->execute();
$system_feedbacks = $system_feedback_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback System</title>
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .feedback-container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 40px;
            color: #4CAF50;
        }

        form {
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        textarea {
            height: 100px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .feedback-section {
            margin-bottom: 30px;
        }

        .feedback {
            background-color: #f2f2f2;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 5px solid #4CAF50;
        }

        .feedback h4 {
            margin: 0;
            font-weight: bold;
        }

        .feedback p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="feedback-container">
        <h1>Product and System Feedback</h1>

        <!-- Product Feedback Form -->
        <h2>Product Feedback</h2>
        <form method="POST">
            <label for="customer_name">Your Name</label>
            <input type="text" name="customer_name" required>

            <label for="rating">Rating (1-5)</label>
            <input type="number" name="rating" min="1" max="5" required>

            <label for="comments">Comments</label>
            <textarea name="comments" required></textarea>

            <input type="hidden" name="product_id" value="1"> <!-- Example product ID -->

            <button type="submit" name="product_feedback_submit">Submit Feedback</button>
        </form>

        <!-- Display Product Feedback -->
        <div class="feedback-section">
            <h2>Product Feedbacks</h2>
            <?php foreach ($product_feedbacks as $feedback): ?>
                <div class="feedback">
                    <h4><?php echo htmlspecialchars($feedback['customer_name']); ?> (Rating: <?php echo $feedback['rating']; ?>/5)</h4>
                    <p><?php echo nl2br(htmlspecialchars($feedback['comments'])); ?></p>
                    <p><small>Posted on <?php echo $feedback['feedback_date']; ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- System Feedback Form -->
        <h2>System Feedback</h2>
        <form method="POST">
            <label for="customer_name">Your Name</label>
            <input type="text" name="customer_name" required>

            <label for="feedback_type">Feedback Type</label>
            <select name="feedback_type" required>
                <option value="bug">Bug</option>
                <option value="suggestion">Suggestion</option>
                <option value="complaint">Complaint</option>
                <option value="other">Other</option>
            </select>

            <label for="comments">Comments</label>
            <textarea name="comments" required></textarea>

            <button type="submit" name="system_feedback_submit">Submit Feedback</button>
        </form>

        <!-- Display System Feedback -->
        <div class="feedback-section">
            <h2>System Feedbacks</h2>
            <?php foreach ($system_feedbacks as $feedback): ?>
                <div class="feedback">
                    <h4><?php echo htmlspecialchars($feedback['customer_name']); ?> (<?php echo ucfirst($feedback['feedback_type']); ?>)</h4>
                    <p><?php echo nl2br(htmlspecialchars($feedback['comments'])); ?></p>
                    <p><small>Posted on <?php echo $feedback['feedback_date']; ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>
