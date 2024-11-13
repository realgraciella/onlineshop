<?php
include 'database/db_connect.php'; // Include database connection

// Fetch client inquiries
$queryClient = "SELECT * FROM client_inquiries ORDER BY date_created DESC";
$stmtClient = $pdo->prepare($queryClient);
$stmtClient->execute();
$clientInquiries = $stmtClient->fetchAll(PDO::FETCH_ASSOC);

// Fetch agent inquiries
$queryAgent = "SELECT agent_inquiries.*, agents.agent_fname, agents.agent_lname 
               FROM agent_inquiries 
               JOIN agents ON agent_inquiries.agent_id = agents.agent_id 
               ORDER BY agent_inquiries.date_created DESC";
$stmtAgent = $pdo->prepare($queryAgent);
$stmtAgent->execute();
$agentInquiries = $stmtAgent->fetchAll(PDO::FETCH_ASSOC);

// Handle admin comments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiryId = $_POST['inquiry_id'];
    $inquiryType = $_POST['inquiry_type'];
    $comment = $_POST['comment'];
    $adminId = 1; // Example admin ID, replace with session-based or dynamic data
    
    $queryComment = "INSERT INTO admin_comments (inquiry_id, inquiry_type, admin_id, comment, date_created) VALUES (?, ?, ?, ?, NOW())";
    $stmtComment = $pdo->prepare($queryComment);
    $stmtComment->execute([$inquiryId, $inquiryType, $adminId, $comment]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries Management</title>
    <!-- Favicons -->
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
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }
        .inquiry-container {
            max-width: 1200px;
            margin: 65px auto;
        }
        .inquiry-section {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .inquiry-section h2 {
            margin-bottom: 15px;
            font-size: 24px;
            color: #333;
        }
        .table th {
            background-color: #4CAF50;
            text-align: center;
            padding: 12px;
            color: white;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }
        .comment-form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            resize: vertical;
        }
        .comment-form button {
            margin-top: 8px;
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        .comment-form button:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 14px;
                padding: 8px;
            }
            .inquiry-section h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
  <?php include 'admin_header.php'; ?>

    <div class="container inquiry-container">
        <h1 class="text-center mb-4">Inquiries Management</h1>

        <!-- Client Inquiries Section -->
        <div class="inquiry-section">
            <h2>Client Inquiries</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($clientInquiries) > 0): ?>
                            <?php foreach ($clientInquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['message']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['date_created']); ?></td>
                                    <td>
                                        <form method="POST" class="comment-form">
                                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                            <input type="hidden" name="inquiry_type" value="client">
                                            <textarea name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No client inquiries found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Agent Inquiries Section -->
        <div class="inquiry-section">
            <h2>Agent Inquiries</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($agentInquiries) > 0): ?>
                            <?php foreach ($agentInquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inquiry['agent_fname']) . ' ' . htmlspecialchars($inquiry['agent_lname']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['message']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['date_created']); ?></td>
                                    <td>
                                        <form method="POST" class="comment-form">
                                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                            <input type="hidden" name="inquiry_type" value="agent">
                                            <textarea name="comment" rows="2" placeholder="Add a comment..." required></textarea>
                                            <button type="submit" class="btn btn-primary btn-sm mt-2">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No agent inquiries found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
