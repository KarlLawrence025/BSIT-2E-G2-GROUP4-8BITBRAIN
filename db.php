<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "8bitbrain_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'message'=>'Database connection failed: '.$conn->connect_error]);
    exit;
}
$conn->set_charset("utf8mb4");
?>
