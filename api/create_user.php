<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['fullname']) || !isset($data['email']) || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$fullname = $conn->real_escape_string($data['fullname']);
$email = $conn->real_escape_string($data['email']);
$username = $conn->real_escape_string($data['username']);
$age = isset($data['age']) ? intval($data['age']) : 18;
$password = $data['password']; // In production, use password_hash()
$account_type = isset($data['account_type']) ? $conn->real_escape_string($data['account_type']) : 'user';

// Check if email already exists
$check_email = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($check_email);
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Email already exists'
    ]);
    exit;
}

// Check if username already exists
$check_username = "SELECT id FROM users WHERE username = '$username'";
$result = $conn->query($check_username);
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Username already exists'
    ]);
    exit;
}

// Insert new user (removed status column as it might not exist)
$sql = "INSERT INTO users (fullname, email, username, age, password, account_type) 
        VALUES ('$fullname', '$email', '$username', $age, '$password', '$account_type')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error creating user: ' . $conn->error
    ]);
}

$conn->close();
?>
