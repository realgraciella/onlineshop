<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // TODO: Validate and sanitize input (You can add more robust validation here)

    // Using prepared statement to prevent SQL injection with named placeholders
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (:first_name, :last_name, :username, :password)");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        // Registration successful
        header("Location: login.php");
        exit();
    } else {
        // Registration failed
        echo "Error: " . $stmt->errorInfo()[2];
        exit(); // Add exit to stop further execution
    }

    // Close the statement
    $stmt = null;
    // Close the connection
    $conn = null;
}
?>



<!DOCTYPE html>
<html>
  <head>
    <title>Login and Registration</title>
    <link href="assets/css/login.css" rel="stylesheet">
    <script src="script.js"></script>

    <style type="text/css">
      button {
        background-color: green;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background-color: green;
        color: black;
    }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="form-container">
        <div id="registerContainer">
          <h2>Register</h2>
          <form id="registerForm" action="register.php" method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
          </form>
          <p>Already have an account? <a href="login.php" id="registerLink">Login Now</a></p>
        </div>
      </div>
    </div>
  </body>
</html>
