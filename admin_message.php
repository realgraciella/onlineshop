<?php
session_start();

// Include the database connection
include 'database/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

try {
    // Fetch agents who have sent messages to the admin, ordered by timestamp
    $query = "
        SELECT DISTINCT m.agent_id, m.username 
        FROM adag_messages m
        WHERE m.admin_id = :admin_id
        ORDER BY m.timestamp DESC"; // Order by timestamp

    $stmt = $pdo->prepare($query); // Use the $pdo variable from db_connect.php
    $stmt->bindParam(':admin_id', $_SESSION['user_id'], PDO::PARAM_INT); // Assuming user_id is stored in session
    $stmt->execute();
    
    // Fetch all agents
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inbox</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">

    <!-- Minified CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet"> <!-- Ensure minified CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Deferred JS loading -->
    <script defer src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script defer src="assets/vendor/aos/aos.js"></script>
    <script defer src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script defer src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script defer src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script defer src="assets/js/admin.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .inbox-container {
            max-width: 600px;
            margin: 90px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .agent-list {
            list-style: none;
            padding: 0;
        }
        .agent-list li {
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            transition: background 0.3s;
        }
        .agent-list li:hover {
            background: #f0f0f0;
        }
        .agent-list a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .agent-list a:hover {
            text-decoration: underline;
        }
        .no-agents {
            text-align: center;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <div class="inbox-container">
        <h1>Inbox</h1>
        <ul class="agent-list">
            <?php if (empty($agents)): ?>
                <li class="no-agents">No message found.</li>
            <?php else: ?>
                <?php foreach ($agents as $agent): ?>
                    <li>
                        <a href="admin_inbox.php?agent_id=<?php echo $agent['agent_id']; ?>">
                            <?php echo htmlspecialchars($agent['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>