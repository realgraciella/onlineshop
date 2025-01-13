<?php
// Start the session to use session variables for success/error messages
session_start();

// Include your database connection configuration
include 'database/db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle form submission when POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $client_fname = $_POST['client_fname'];
    $client_mname = $_POST['client_mname'];
    $client_lname = $_POST['client_lname'];
    $client_sex = $_POST['client_sex'];
    $client_age = $_POST['client_age'];
    $client_birthdate = $_POST['client_birthdate'];
    $client_contact = $_POST['client_contact'];
    $client_address = $_POST['client_address'];
    $client_email = $_POST['client_email'];

    // Check for duplicate contact number
    $stmt = $pdo->prepare("SELECT client_contact FROM clients WHERE client_contact = :client_contact");
    $stmt->bindParam(':client_contact', $client_contact);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Contact number already exists
        $_SESSION['error'] = 'This contact number is already in use.';
    } else {
        try {
            // Generate unique username and random password
            $client_user = "CLT-" . substr($client_fname, 0, 2) . rand(1000, 9999);
            $password = bin2hex(random_bytes(5)); // Generate a random password

            // Store the plain text password (not hashed)
            $plain_password = $password;

            // Insert client details into clients table
            $sql = "INSERT INTO clients (client_fname, client_mname, client_lname, client_sex, client_age, client_birthdate, client_contact, client_address, client_email, role, client_creationDate, agent_id, client_user)
                    VALUES (:client_fname, :client_mname, :client_lname, :client_sex, :client_age, :client_birthdate, :client_contact, :client_address, :client_email, 'client', NOW(), :agent_id, :client_user)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':client_fname', $client_fname);
            $stmt->bindParam(':client_mname', $client_mname);
            $stmt->bindParam(':client_lname', $client_lname);
            $stmt->bindParam(':client_sex', $client_sex);
            $stmt->bindParam(':client_age', $client_age);
            $stmt->bindParam(':client_birthdate', $client_birthdate);
            $stmt->bindParam(':client_contact', $client_contact);
            $stmt->bindParam(':client_address', $client_address);
            $stmt->bindParam(':client_email', $client_email);
            $stmt->bindParam(':agent_id', $_SESSION['agent_id']); // Agent's ID from session
            $stmt->bindParam(':client_user', $client_user); // Store generated username

            if ($stmt->execute()) {
                // Insert login credentials into users table
                $sql_users = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'client')";
                $stmt_users = $pdo->prepare($sql_users);

                // Bind parameters
                $stmt_users->bindParam(':username', $client_user);
                $stmt_users->bindParam(':password', $plain_password);

                if ($stmt_users->execute()) {
                    // Redirect to client_view.php after success
                    $_SESSION['success'] = ' Client registered successfully!';
                    header("Location: agent_viewClient.php");
                    exit();
                } else {
                    // Handle error for users table insertion
                    $_SESSION['error'] = 'Failed to register user credentials.';
                }
            } else {
                // Handle error for clients table insertion
                $errorInfo = $stmt->errorInfo();
                $_SESSION['error'] = 'Failed to register client: ' . $errorInfo[2];
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Client Registration</title>
  <link href="assets/img/logo/2.png" rel="icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/agent.css" rel="stylesheet">
</head>
<body>
  <?php include 'agent_header.php'; ?>
  <main id="main">
    <h2 style="text-align: center; margin-top: 90px; margin-bottom: 10px;">CUSTOMER REGISTRATION</h2>
    <div class="add-agent-form">
        <?php if (isset($_SESSION['error'])) { echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); } ?>
        <?php if (isset($_SESSION['success'])) { echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>'; unset($_SESSION['success']); } ?>
        <form action="agent_registerClient.php" method="POST">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="client_fname" required>

            <label for="mname">Middle Name:</label>
            <input type="text" id="mname" name="client_mname">

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="client_lname" required>

            <label for="sex">Sex:</label>
            <select id="sex" name="client_sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="age">Age:</label>
            <input type="number" id="age" name="client_age" min="18" max="100" required>

            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="client_birthdate" required>

            <label for="contact">Contact Number:</label>
            <input type="tel" id="contact" name="client_contact" pattern="^\d{10,15}$" maxlength="15" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="client_address" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="client_email" required>

            <button type="submit">Register Client</button>
        </form>
    </div>
  </main>
</body>
</html>