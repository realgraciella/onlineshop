<?php
session_start(); // Start the session

// Include PDO connection file
include 'database/db_connect.php'; 

// Check if the user is logged in (ensure the username is stored in the session)
if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Send message
if (isset($_POST['send_message'])) {
    $agent_id = $_SESSION['user_id'];
    $admin_id = $_POST['admin_id'];
    $username = $_SESSION['username']; // Use the username from the session
    $message = $_POST['message'];

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO adag_messages (agent_id, admin_id, username, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$agent_id, $admin_id, $username, $message]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Retrieve all messages
try {

    $agent_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM adag_messages WHERE agent_id = :agent_id ORDER BY timestamp ASC");
    $stmt->bindParam(':agent_id', $agent_id, PDO::PARAM_INT); // Bind the agent_id parameter
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Inbox</title>
     <!-- Favicons -->
     <link href="assets/img/logo/2.png" rel="icon">
    

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/agent.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 80px;
            margin-bottom: 30px;
            font-weight: 500;
        }
        #chat-box {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
            margin-bottom: 20px;
            height: 400px;
            border: 1px solid #ddd;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 8px;
            position: relative;
        }
        .message strong {
            color: #007bff;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .message p {
            color: #333;
            margin: 5px 0;
        }
        .message small {
            color: #aaa;
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.8em;
        }
        form {
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        input[type="text"], textarea {
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            outline: none;
        }
        textarea {
            resize: none;
            height: 100px;
        }
        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message.mine {
            background-color: #e0f7fa;
            align-self: flex-end;
        }
        .message.other {
            background-color: #f1f1f1;
            align-self: flex-start;
        }
    </style>
</head>
<body>
<?php include 'agent_header.php'; ?>
    <h1>Message with Admin</h1>

    <div id="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['username'] == 'admin' ? 'other' : 'mine'; ?>">
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo $msg['timestamp']; ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>"> <!-- Example agent ID -->
        <input type="hidden" name="admin_id" value="6"> <!-- Example admin ID -->
        <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" readonly>
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <button type="submit" name="send_message">Send</button>
    </form>
</body>
</html>
