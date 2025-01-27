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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
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
        if (!isset($_FILES['agent_id_front']['tmp_name']) || !isset($_FILES['agent_id_back']['tmp_name'])) {
            throw new Exception("ID images are required for registration.");
        }

        $id_front_data = file_get_contents($_FILES['agent_id_front']['tmp_name']);
        $id_back_data = file_get_contents($_FILES['agent_id_back']['tmp_name']);

        // Check for duplicate contact number
        $stmt = $pdo->prepare("SELECT agent_contact FROM agents WHERE agent_contact = :agent_contact");
        $stmt->bindParam(':agent_contact', $agent_contact);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            throw new Exception("This contact number is already in use.");
        }

        // Generate unique username and random password
        $agent_user = "AGT-" . substr($agent_fname, 0, 2) . rand(1000, 9999);
        $password = bin2hex(random_bytes(5)); // Generate a random password

        // Plain text password for demonstration (not recommended for production)
        $plain_password = $password;

        // Insert agent details with a starting credit limit of 2500 pesos
        $sql = "INSERT INTO agents (agent_fname, agent_mname, agent_lname, agent_sex, agent_age, agent_birthdate, agent_contact, agent_address, agent_validID, agent_email, id_front_image, id_back_image, role, credit_limit, agent_status, agent_creationDate, agent_user) 
                VALUES (:agent_fname, :agent_mname, :agent_lname, :agent_sex, :agent_age, :agent_birthdate, :agent_contact, :agent_address, :agent_validID, :agent_email, :id_front_image, :id_back_image, 'Sales Agent', 2500, 'Active', NOW(), :agent_user)";

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
        $stmt->bindParam(':agent_user', $agent_user);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert agent data into the database.");
        }

        // Insert login credentials into users table
        $sql_users = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'Sales Agent')";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->bindParam(':username', $agent_user);
        $stmt_users->bindParam(':password', $plain_password);

        if (!$stmt_users->execute()) {
            throw new Exception("Failed to create login credentials.");
        }

        // Send email to the agent
        $mail = new PHPMailer(true);

        try {
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
            // $mail->Body = "Hello $agent_fname,<br><br>Your registration as a sales agent was successful. Your username is: $agent_user and your password is: $password.<br><br>Best regards,<br>Dho & Myrna Fashion Boutique";

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


            $mail->send();
        } catch (Exception $e) {
            logError("Email error: " . $mail->ErrorInfo);
            throw new Exception("Agent registered but failed to send email.");
        }

        // Redirect on success
        $_SESSION['success'] = 'Agent registered successfully!';
        header("Location: admin_viewAgent.php");
        exit();
    } catch (Exception $e) {
        logError($e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header("Location: admin_registerAgent.php");
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
        <input type="text" id="contact" name="agent_contact" pattern="\d*" maxlength="11" required>
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
        <div id="otherIdField" style="display:none;">
          <label for="otherId">Specify Other ID:</label>
          <input type="text" id="otherId" name="agent_validID_other" placeholder="Specify other ID">
        </div>
        <label for="id_front">Upload ID Front:</label>
        <input type="file" id="id_front" name="agent_id_front" required>
        <label for="id_back">Upload ID Back:</label>
        <input type="file" id="id_back" name="agent_id_back" required>
        <button type="submit">Register Agent</button>
      </form>

    </div>
  </main>


  <script>
    // Display success or error alerts if they exist in session
    <?php if (isset($_SESSION['success'])): ?>
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $_SESSION['success']; ?>'
      });
      <?php unset($_SESSION['success']); // Clear the success message ?>
    <?php elseif (isset($_SESSION['error'])): ?>
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?php echo $_SESSION['error']; ?>'
      });
      <?php unset($_SESSION['error']); // Clear the error message ?>
    <?php endif; ?>

    // Function to toggle the "Specify Other ID Type" field
    function toggleOtherIdField() {
      var validIDSelect = document.getElementById('validID');
      var otherIdField = document.getElementById('otherIdField');
      if (validIDSelect.value === 'Other') {
        otherIdField.style.display = 'block';
      } else {
        otherIdField.style.display = 'none';
      }
    }

    function logFileDetails(inputId) {
        const fileInput = document.getElementById(inputId);
        const file = fileInput.files[0];  // Get the first file uploaded

        if (file) {
            console.log(`File Name: ${file.name}`);
            console.log(`File Type: ${file.type}`);
            console.log(`File Size: ${file.size} bytes`);
            console.log(`File URL: ${URL.createObjectURL(file)}`); // This is a temporary URL to preview the file
        } else {
            console.log("No file selected.");
        }
    }

    // Event listeners for the file inputs
    document.getElementById("id_front").addEventListener("change", function() {
        logFileDetails("id_front");
    });

    document.getElementById("id_back").addEventListener("change", function() {
        logFileDetails("id_back");
    });

  </script>
</body>

</html>