<?php
// Database configuration
$dbHost = 'localhost';
$dbName = 'urbanlink';
$dbUser = 'root';
$dbPass = '';

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Display error message
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
