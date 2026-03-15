<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "8bitbrain_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Return JSON error instead of HTML
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
