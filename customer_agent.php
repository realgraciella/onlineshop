<?php
session_start(); // Start the session

// Include your database connection file
include 'database/db_connect.php'; // Adjust the path as necessary

// Check if the user is logged in and has an assigned agent
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Prepare and execute the query to get the agent details
    $query = "SELECT a.agent_fname, a.agent_mname, a.agent_lname, a.agent_contact, a.agent_address 
              FROM agents a 
              JOIN clients c ON a.agent_id = c.agent_id 
              WHERE c.client_user = :username";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        // Fetch the result
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $agent = $stmt->fetch();
        
        // Check if agent details were found
        if ($agent) {
            $fname = $agent['agent_fname'];
            $mname = $agent['agent_mname'];
            $lname = $agent['agent_lname'];
            $contact = $agent['agent_contact'];
            $address = $agent['agent_address'];
        } else {
            // Handle case where no agent is found
            $fname = $mname = $lname = $contact = $address = "Not available";
        }
    } catch (PDOException $e) {
        // Handle query execution error
        echo "Error executing query: " . $e->getMessage();
        exit();
    }
} else {
    // Redirect to login or show an error if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Contact</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding-top: 20px;
            margin: 0;
        }
        .agent-info {
            max-width: 500px;
            margin: 200px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .agent-detail {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        p {
            font-size: 16px;
            color: #666;
            margin: 5px 0 0;
        }
    </style>
</head>
<body>
    <?php include 'customer_header.php'; ?>
    <div class="agent-info">
        <h1>Agent Details</h1>
        <div class="agent-detail">
            <label for="name">Agent Name:</label>
            <p id="name"><?php echo htmlspecialchars($fname . ' ' . $mname . ' ' . $lname); ?></p>
        </div>
        <div class="agent-detail">
            <label for="contact">Contact Number:</label>
            <p id="contact"><?php echo htmlspecialchars($contact); ?></p>
        </div>
        <div class="agent-detail">
            <label for="address">Address:</label>
            <p id="address"><?php echo htmlspecialchars($address); ?></p>
        </div>
    </div>
</body>
</html>