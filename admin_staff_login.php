<?php
// Include your database connection configuration
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check login credentials
    $sql = "SELECT * FROM staff_reg WHERE email = ? AND pass = ?";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Login successful, redirect to staff.php
            header('Location: staff.php');
            exit();
        } else {
            echo 'Invalid username or password';
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
    <link href="assets/css/login.css" rel="stylesheet">
    <script src="script.js"></script>
  </head>

  <body>
    <div class="container">
      <div class="form-container">
        <div id="loginContainer">
          <h2>Login</h2>
          <form id="loginForm" action="admin_staff_login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
