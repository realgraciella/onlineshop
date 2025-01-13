<?php
session_start();

$host = 'localhost';
$db = 'dmshop1'; 
$user = 'root'; 
$pass = ''; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get agent_id and username from the URL
$agent_id = isset($_GET['agent_id']) ? intval($_GET['agent_id']) : 0;
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Send message
if (isset($_POST['send_message'])) {
    $admin_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO adag_messages (agent_id, admin_id, username, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $agent_id, $admin_id, $_SESSION['username'], $message);
    $stmt->execute();
    $stmt->close();
}

// Retrieve messages for the specific agent
$sql = "SELECT * FROM adag_messages WHERE agent_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial ```php
-width, initial-scale=1.0">
    <title>Messages with <?php echo htmlspecialchars($username); ?></title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <h1>Messages with <?php echo htmlspecialchars($username); ?></h1>

    <div id="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['username'] == $_SESSION['username'] ? 'mine' : 'other'; ?>">
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo $msg['timestamp']; ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <input type="hidden" name="agent_id" value="<?php echo $agent_id; ?>">
        <input type="hidden" name="admin_id" value="<?php echo $_SESSION['user_id']; ?>">
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <button type="submit" name="send_message">Send</button>
    </form>
</body>
</html>