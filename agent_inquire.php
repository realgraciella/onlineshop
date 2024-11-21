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
        font-family: 'Poppins', sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    .feedback-container {
        width: 85%;
        max-width: 800px;
        margin: 40px auto;
        background-color: #ffffff;
        padding: 30px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    h1, h2 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    h2 {
        font-size: 20px;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 8px;
        display: inline-block;
    }

    form {
        margin-bottom: 30px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    label {
        font-weight: 500;
        color: #333;
        display: block;
        margin-bottom: 6px;
    }

    input, textarea, select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #cccccc;
        border-radius: 5px;
        transition: border-color 0.3s ease-in-out;
    }

    input:focus, textarea:focus, select:focus {
        border-color: #4CAF50;
        outline: none;
        box-shadow: 0 0 4px rgba(76, 175, 80, 0.2);
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 12px 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    button:hover {
        background-color: #45a049;
    }

    .feedback-section {
        margin-top: 40px;
    }

    .feedback {
        background-color: #f3f7f9;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 5px solid #4CAF50;
        border-radius: 5px;
        transition: transform 0.3s ease-in-out;
    }

    .feedback:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .feedback h4 {
        margin-bottom: 6px;
        font-weight: 600;
        color: #34495e;
    }

    .feedback p {
        margin: 5px 0;
        color: #555;
    }

    .feedback p small {
        color: #888;
        font-size: 12px;
    }
</style>

</head>

<body>
<?php include 'agent_header.php'; ?>

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
