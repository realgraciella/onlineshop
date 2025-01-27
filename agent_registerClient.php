<?php
// Start the session to use session variables for success/error messages
session_start();

// Include your database connection configuration
include 'database/db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Use output buffering to prevent any output before the header redirect
ob_start();

$registration_success = false;
$duplicate_contact = false;

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

// Error logging function for better debugging
function logError($message) {
    error_log(date("[Y-m-d H:i:s]") . " $message\n", 3, 'error_log.txt');
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
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
            throw new Exception("This contact number is already in use.");
        }

        // Generate unique username and password
        $client_user = "CLT-" . substr($client_fname, 0, 2) . rand(1000, 9999);
        $password = bin2hex(random_bytes(5));
        $plain_password = $password;

        // Insert client details
        $sql = "INSERT INTO clients (client_fname, client_mname, client_lname, client_sex, client_age, client_birthdate, client_contact, client_address, client_email, role, client_creationDate, agent_id, client_user)
                VALUES (:client_fname, :client_mname, :client_lname, :client_sex, :client_age, :client_birthdate, :client_contact, :client_address, :client_email, 'client', NOW(), :agent_id, :client_user)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':client_fname', $client_fname);
        $stmt->bindParam(':client_mname', $client_mname);
        $stmt->bindParam(':client_lname', $client_lname);
        $stmt->bindParam(':client_sex', $client_sex);
        $stmt->bindParam(':client_age', $client_age);
        $stmt->bindParam(':client_birthdate', $client_birthdate);
        $stmt->bindParam(':client_contact', $client_contact);
        $stmt->bindParam(':client_address', $client_address);
        $stmt->bindParam(':client_email', $client_email);
        $stmt->bindParam(':agent_id', $_SESSION['agent_id']);
        $stmt->bindParam(':client_user', $client_user);

        if (!$stmt->execute()) {
            throw new Exception("Failed to register client.");
        }

        // Insert login credentials
        $sql_users = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'client')";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->bindParam(':username', $client_user);
        $stmt_users->bindParam(':password', $plain_password);

        if (!$stmt_users->execute()) {
            throw new Exception("Failed to create user credentials.");
        }

        // Send email to the agent
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dhomyrna@dmfashion.site';
        $mail->Password = 'dmFashion310,';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('dhomyrna@dmfashion.site', 'Dho & Myrna');
        $mail->addAddress($agent_email);
        $mail->isHTML(true);
        $mail->Subject = 'Agent Registration Successful';
        // $mail->Body = "Hello $client_fname,<br><br>Your registration as a client was successful. Your username is: $client_user and your password is: $password.<br><br>Best regards,<br>Dho & Myrna Fashion Boutique";

        $mail->Body = "
                      <html>
                      <head>
                          <style>
                              body {
                                  font-family: Arial, sans-serif;
                                  color: #333333;
                                  line-height: 1.6;
                                  margin: 0;
                                  padding: 0;
                              }
                              .email-container {
                                  width: 100%;
                                  max-width: 600px;
                                  margin: 0 auto;
                                  border: 1px solid #e0e0e0;
                                  border-radius: 8px;
                                  background-color: #f9f9f9;
                                  overflow: hidden;
                              }
                              .email-header {
                                  background-color: #007BFF;
                                  color: white;
                                  padding: 20px;
                                  text-align: center;
                              }
                              .email-header h1 {
                                  margin: 0;
                              }
                              .email-body {
                                  padding: 20px;
                                  background-color: white;
                              }
                              .email-body h2 {
                                  color: #333333;
                              }
                              .email-body p {
                                  font-size: 14px;
                                  line-height: 1.5;
                              }
                              .email-footer {
                                  background-color: #007BFF;
                                  color: white;
                                  text-align: center;
                                  padding: 10px;
                                  font-size: 12px;
                              }
                              .email-footer a {
                                  color: white;
                                  text-decoration: none;
                              }
                              .email-footer a:hover {
                                  text-decoration: underline;
                              }
                          </style>
                      </head>
                      <body>
                          <div class='email-container'>
                              <div class='email-header'>
                                  <h1>Dho & Myrna Fashion Boutique</h1>
                              </div>
                              <div class='email-body'>
                                  <h2>Hello $agent_fname,</h2>
                                  <p>Your registration as a sales agent was successful. Here are your details:</p>
                                  <ul>
                                      <li><strong>Username:</strong> $agent_user</li>
                                      <li><strong>Password:</strong> $password</li>
                                  </ul>
                                  <p>Best regards,<br>Dho & Myrna Fashion Boutique</p>
                              </div>
                              <div class='email-footer'>
                                  <p>&copy; 2025 Dho & Myrna Fashion Boutique. All rights reserved.</p>
                                  <p>For support, contact us at <a href='mailto:support@dmfashion.site'>support@dmfashion.site</a></p>
                              </div>
                          </div>
                      </body>
                      </html>
                  ";

        if (!$mail->send()) {
            logError("Email error: " . $mail->ErrorInfo);
            throw new Exception("Client registered but email not sent.");
        }

        $_SESSION['success'] = 'Client registered successfully!';
        header("Location: agent_viewClient.php");
        exit();

    } catch (Exception $e) {
        logError($e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header("Location: agent_registerClient.php");
        exit();
    }
}

// Clear output buffer
ob_end_flush();
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
            <input type="tel" id="contact" name="client_contact" pattern="^\d{10,15}$" maxlength="11" required>

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