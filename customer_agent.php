<!DOCTYPE html>
<html lang="en">
<head>
    <link href="assets/css/admin.css" rel="stylesheet">
    <link href="assets/img/logo/2.png" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Contact</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding-top: 20px;
            margin: 0;
}
.agent-info {
    max-width: 500px;
    margin: 200px auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

.agent-detail {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    color: #555;
}

p {
    font-size: 16px;
    color: #666;
    margin: 5px 0 0;
}

    </style>
</head>
<body>
  <?php include 'customer_header.php'; ?>
    <div class="agent-info">
        <h1>Agent Details</h1>
        <div class="agent-detail">
            <label for="name">Agent Name:</label>
            <p id="name">John Doe</p>
        </div>
        <div class="agent-detail">
            <label for="contact">Contact Number:</label>
            <p id="contact">+1 (123) 456-7890</p>
        </div>
        <div class="agent-detail">
            <label for="address">Address:</label>
            <p id="address">1234 Elm Street, Springfield, IL 62701</p>
        </div>
    </div>
</body>
</html>