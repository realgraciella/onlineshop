<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // TODO: Validate and sanitize input

    $sql = "SELECT * FROM users WHERE email=:email AND password=:password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // User login successful

        // Insert a record into the logins table
        $custId = $result['cust_id'];
        $email = $result['email'];
        $date = date("Y-m-d");
        $time = date("H:i:s");

        $insertSql = "INSERT INTO logins (cust_id, email, date, time) VALUES (:custId, :email, :date, :time)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindParam(':custId', $custId);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':date', $date);
        $insertStmt->bindParam(':time', $time);
        $insertStmt->execute();

        // Default redirection for all users
        header("Location: customer.php");
        exit();
    } else {
        // Login failed
        echo "Invalid email or password";
    }

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

    <style>
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
        <div id="loginContainer">
          <h2>Login</h2>
          <form id="loginForm" action="login.php" method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
          </form>
          <p>New user? <a href="register.php" id="registerLink">Register here</a></p>
        </div>
      </div>
    </div>
  </body>
</html>

