<?php
// Start the session to use session variables for success/error messages
session_start();

// Include your database connection configuration
include 'database/db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$registration_success = false;
$duplicate_contact = false;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $agent_fname = $_POST['agent_fname'];
    $agent_mname = $_POST['agent_mname'];
    $agent_lname = $_POST['agent_lname'];
    $agent_sex = $_POST['agent_sex'];
    $agent_age = $_POST['agent_age'];
    $agent_birthdate = $_POST['agent_birthdate'];
    $agent_contact = $_POST['agent_contact'];
    $agent_address = $_POST['agent_address'];
    $agent_validID = $_POST['agent_validID'];
    $agent_email = $_POST['agent_email'];

    // File uploads for ID pictures
    $id_front_data = file_get_contents($_FILES['agent_id_front']['tmp_name']);
    $id_back_data = file_get_contents($_FILES['agent_id_back']['tmp_name']);

    // Check for duplicate contact number
    $stmt = $pdo->prepare("SELECT agent_contact FROM agents WHERE agent_contact = :agent_contact");
    $stmt->bindParam(':agent_contact', $agent_contact);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Contact number already exists
        $duplicate_contact = true;
        $_SESSION['error'] = 'This contact number is already in use.';
    } else {
        try {
            // Generate unique username and random password
            $agent_user = "AGT-" . substr($agent_fname, 0, 2) . rand(1000, 9999);
            $password = bin2hex(random_bytes(5)); // Generate a random password

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert agent details
            $sql = "INSERT INTO agents (agent_fname, agent_mname, agent_lname, agent_sex, agent_age, agent_birthdate, agent_contact, agent_address, agent_validID, agent_email, id_front_image, id_back_image, role, agent_status, agent_creationDate) 
                    VALUES (:agent_fname, :agent_mname, :agent_lname, :agent_sex, :agent_age, :agent_birthdate, :agent_contact, :agent_address, :agent_validID, :agent_email, :id_front_image, :id_back_image, 'Sales Agent', 'Active', NOW())";
            $stmt = $pdo->prepare($sql);

            // Bind parameters for the agents table
            $stmt->bindParam(':agent_fname', $agent_fname);
            $stmt->bindParam(':agent_mname', $agent_mname);
            $stmt->bindParam(':agent_lname', $agent_lname);
            $stmt->bindParam(':agent_sex', $agent_sex);
            $stmt->bindParam(':agent_age', $agent_age);
            $stmt->bindParam(':agent_birthdate', $agent_birthdate);
            $stmt->bindParam(':agent_contact', $agent_contact);
            $stmt->bindParam(':agent_address', $agent_address);
            $stmt->bindParam(':agent_validID', $agent_validID);
            $stmt->bindParam(':agent_email', $agent_email);
            $stmt->bindParam(':id_front_image', $id_front_data, PDO::PARAM_LOB);
            $stmt->bindParam(':id_back_image', $id_back_data, PDO::PARAM_LOB);

            if ($stmt->execute()) {
                // Insert login credentials into users table
                $sql_users = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'Sales Agent')";
                $stmt_users = $pdo->prepare($sql_users);

                // Bind parameters for the users table
                $stmt_users->bindParam(':username', $agent_user);
                $stmt_users->bindParam(':password', $hashed_password);

                $stmt_users->execute();
                
                // Send email to the agent
                $mail = new PHPMailer(true);
                try {
                    // Enable SMTP debugging
                    $mail->SMTPDebug = 2; // 0 = off (for production use), 1 = client messages, 2 = client and server messages
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dhomyrna474@gmail.com'; // Use your Gmail address
                    $mail->Password = 'dmfashion'; // Use an app-specific password or your real password for testing
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('dhomyrna474@gmail.com', 'Dho and Myrna Fashion Boutique');
                    $mail->addAddress($agent_email); // Send to the agent's provided email

                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to the Team!';
                    $mail->Body = "Dear $agent_fname,<br><br>Welcome! Your username is <b>$agent_user</b> and your temporary password is <b>$password</b>.<br>Please change your password upon first login.<br><br>Best regards,<br>Your Company";

                    $mail->send();
                    echo 'Email has been sent';
                } catch (Exception $e) {
                    // Log and display error
                    error_log("PHPMailer Exception: {$mail->ErrorInfo}");
                    echo "Error in sending email: {$mail->ErrorInfo}";
                }

                // Redirect to admin_viewAgent.php after success
                header("Location: admin_viewAgent.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Page - Agent Registration</title>
  <link href="assets/img/logo/2.png" rel="icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <?php include 'admin_header.php'; ?>
  <main id="main">
    <h2 style="text-align: center; margin-top: 90px; margin-bottom: 10px;">AGENT REGISTRATION</h2>
    <div class="add-agent-form">
        <form action="admin_registerAgent.php" method="POST" enctype="multipart/form-data">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="agent_fname" required>
            <label for="mname">Middle Name:</label>
            <input type="text" id="mname" name="agent_mname">
            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="agent_lname" required>
            <label for="sex">Sex:</label>
            <select id="sex" name="agent_sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <label for="age">Age:</label>
            <input type="number" id="age" name="agent_age" min="18" max="100" required>
            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="agent_birthdate" required>
            <label for="contact">Contact Number:</label>
            <input type="text" id="contact" name="agent_contact" pattern="\d*" maxlength="15" required>
            <label for="address">Address:</label>
            <input type="text" id="address" name="agent_address" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="agent_email" required>
            <label for="validID">Valid ID:</label>
            <select id="validID" name="agent_validID" required onchange="toggleOtherIdField()">
                <option value="Driver's License">Driver's License</option>
                <option value="Passport">Passport</option>
                <option value="National ID">National ID</option>
                <option value="Other">Other</option>
            </select>
            <div id="otherIdWrapper" style="display: none;">
                <label for="other_id_type">Specify Other ID Type:</label>
                <input type="text" id="other_id_type" name="other_id_type">
            </div>
            <label for="id_front">Upload ID Front:</label>
            <input type="file" id="id_front" name="agent_id_front" required>
            <label for="id_back">Upload ID Back:</label>
            <input type="file" id="id_back" name="agent_id_back" required>
            <button type="submit">Register Agent</button>
        </form>
    </div>
  </main>
</body>
</html>
