<?php
session_start();
include 'database/db_connect.php'; // Database connection using PDO

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check for default admin credentials
    if ($username == 'fashionadmin' && $password == 'admin310') {
        $_SESSION['username'] = 'fashionadmin';
        $_SESSION['role'] = 'admin';
        header('Location: admin.php');
        exit();
    }

    try {
        // Prepare the query to check username
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Check password
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == 'admin') {
                    header('Location: admin.php');
                } elseif ($row['role'] == 'Sales Agent') {
                    header('Location: salesagent.php');
                } elseif ($row['role'] == 'client') {
                    header('Location: customer.php');
                } else {
                    echo "Invalid role!";
                }
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "Invalid username!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/css/login.css" rel="stylesheet">
  </head>

  <body>
  <div class="video-container">
    <video autoplay muted loop id="bg-video">
      <source src="assets/img/logo/bg.mp4" type="video/mp4">
    </video>
  </div>

    <div class="container">
      <div class="form-container">
        <div id="loginContainer">
          <h2>Login</h2>
          <form id="loginForm" action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
          </form>
          <p><a href="forgot_pass.php">Forgot Password?</a></p>
        </div>
      </div>
    </div>
  </body>
</html>

