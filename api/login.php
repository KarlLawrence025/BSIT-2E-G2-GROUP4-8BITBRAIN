<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Log the request for debugging
error_log("Login attempt - Data received: " . json_encode($data));

// Validate required fields
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required'
    ]);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// Find user by email
$sql = "SELECT id, fullname, email, username, account_type, password FROM users WHERE email = '$email'";
error_log("SQL Query: " . $sql);

$result = $conn->query($sql);

if (!$result) {
    error_log("SQL Error: " . $conn->error);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $conn->error
    ]);
    exit;
}

if ($result->num_rows === 0) {
    error_log("No user found with email: " . $email);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email or password'
    ]);
    exit;
}

$user = $result->fetch_assoc();
error_log("User found: " . $user['email'] . " | Password match: " . ($user['password'] === $password ? 'YES' : 'NO'));

// Check password (in production, use password_verify with hashed passwords)
if ($user['password'] !== $password) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email or password'
    ]);
    exit;
}

// Login successful
error_log("Login successful for user: " . $user['email']);
echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'username' => $user['username'],
        'account_type' => $user['account_type']
    ]
]);

$conn->close();
?>
