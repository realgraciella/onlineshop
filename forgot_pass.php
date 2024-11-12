<?php
session_start();
include 'database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    try {
        // Check if the email exists in the database
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Generate a unique token for password reset
            $token = bin2hex(random_bytes(50)); // 50-byte token

            // Save the token and its expiration time in the database
            $query = "UPDATE users SET reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Send the reset link to the user's email
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $message = "To reset your password, please click the following link: " . $resetLink;
            $headers = "From: no-reply@yourdomain.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "An email has been sent to you with instructions to reset your password.";
            } else {
                echo "There was an error sending the reset email.";
            }
        } else {
            echo "No account found with that email address.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Forgot Password</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/css/login.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="form-container">
        <h2>Forgot Password</h2>
        <form action="forgot_password.php" method="POST">
          <input type="email" name="email" placeholder="Enter your email" required>
          <button type="submit">Send Reset Link</button>
        </form>
      </div>
    </div>
  </body>
</html>
