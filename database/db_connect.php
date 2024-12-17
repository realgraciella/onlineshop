<?php
// PDO connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmshop1";

try {
    // Use $servername instead of $host
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
