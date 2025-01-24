<?php
session_start();
include 'database/db_connect.php'; // Database connection using PDO

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
    $username = trim($_POST['username']); // Trim input to avoid whitespace issues
    $password = trim($_POST['password']);

    // Check for default admin credentials (hardcoded for testing)
    // if ($username === 'fashionadmin' && $password === 'admin310') {
    //     $_SESSION['username'] = 'fashionadmin';
    //     $_SESSION['role'] = 'admin';
    //     header('Location: admin.php');
    //     exit();
    // }

    try {
        // Prepare and execute the query to check username
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Check password (plain-text version for testing)
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['role'];

                // Redirect based on username prefix
                if (strpos($username, 'AGT-') === 0) {
                    header('Location: agent.php');
                    exit();
                } elseif (strpos($username, 'CLT-') === 0) {
                    header('Location: customer.php');
                    exit();
                } elseif ($row['role'] === 'admin') {
                    header('Location: admin.php');
                    exit();
                } else {
                    echo "<script>Swal.fire('Error', 'Invalid role!', 'error');</script>";
                }
            } else {
                echo "<script>Swal.fire('Error', 'Invalid password!', 'error');</script>";
            }
        } else {
            echo "<script>Swal.fire('Error', 'Invalid username!', 'error');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>Swal.fire('Error', 'Database Error: " . $e->getMessage() . "', 'error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet"> <!-- Include the custom CSS -->
</head>
<body>
    <div class="video-container">
        <video autoplay muted loop id="bg-video">
            <source src="assets/img/logo/bg.mp4" type="video/mp4">
        </video>
    </div>

    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <form id="loginForm" action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <p><a href="forgot_pass.php">Forgot Password?</a></p>
        </div>
    </div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>
</html>
