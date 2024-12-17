<?php
session_start(); // Start the session to access session variables
include 'database/db_connect.php';

// Assuming you have stored the logged-in agent's username in the session
$loggedInUsername = $_SESSION['username']; // Make sure this session variable is set when the agent logs in

// Updated SQL query to select the correct columns and filter by username
$sql = "SELECT client_id, client_user, client_fname, client_mname, client_lname, client_contact 
        FROM clients 
        WHERE username = :username"; 

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $loggedInUsername, PDO::PARAM_STR); // Bind the username parameter
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin - Client List</title>

    <!-- Favicons -->
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/admin.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background-color: #f5f5f5;
        }

        main {
            margin-top: 80px;
        }

        .client-list-form {
            width: 90%;
            max-width: 900px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .client-list-form h2 {
            color: #333;
            text-align: center;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .client-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .client-row:last-child {
            border-bottom: none;
        }

        .client-info {
            flex-grow: 2;
        }

        .client-actions {
            display: flex;
            gap: 10px;
        }

        .client-actions button {
            border: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #5bc0de;
        }

        .btn-delete {
            background-color: #d9534f;
        }
    </style>
</head>

<body>

<?php include 'agent_header.php'; ?>

<main id="main">
    <div class="client-list-form">
        <h2>Client List</h2>

        <?php
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $client_id = htmlspecialchars($row["client_id"]);
                $client_name = htmlspecialchars($row["client_fname"] . " " . $row["client_mname"] . " " . $row["client_lname"]);
                $client_user = htmlspecialchars($row["client_user"]);
                $client_contact = htmlspecialchars($row["client_contact"]);
                ?>

                <div class="client-row">
                    <div class="client-info">
                        <strong><?php echo $client_id; ?>.</strong> <?php echo $client_name; ?> (<?php echo $client_user; ?>)
                    </div>
                    <div class="client-actions">
                        <button class="btn-edit" onclick="promptForPin('edit', '<?php echo $client_id; ?>')">Edit</button>
 <button class="btn-delete" onclick="promptForPin('delete', '<?php echo $client_id; ?>')">Delete</button>
                    </div>
                </div>

                <?php
            }
        } else {
            echo "<p style='text-align : center; color: red;'>No clients found.</p>";
        }

        $pdo = null;
        ?>
    </div>
</main>

<script>
    // Prompt for admin PIN and redirect based on action (edit or delete)
    function promptForPin(action, clientId) {
        const pin = prompt("Please enter the admin PIN to continue:");
        if (pin) {
            window.location.href = `admin_${action}_client.php?id=${clientId}&pin=${pin}`;
        }
    }
</script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>