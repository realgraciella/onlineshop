<?php
session_start();
include 'database/db_connect.php';

// Check if the token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database and is not expired
    try {
        $query = "SELECT * FROM users WHERE reset_token = :token AND token_expiration > NOW()";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                // Check if the new passwords match
                if ($new_password == $confirm_password) {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $query = "UPDATE users SET password = :password, reset_token = NULL, token_expiration = NULL WHERE reset_token = :token";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':token', $token);
                    $stmt->execute();

                    echo "Your password has been successfully reset.";
                } else {
                    echo "Passwords do not match!";
                }
            }
        } else {
            echo "Invalid or expired token!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No token provided!";
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Reset Password</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/css/login.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="form-container">
        <h2>Reset Password</h2>
        <form action="reset_pass.php?token=<?php echo $_GET['token']; ?>" method="POST">
          <input type="password" name="new_password" placeholder="New Password" required>
          <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
          <button type="submit">Reset Password</button>
        </form>
      </div>
    </div>
  </body>
</html>
