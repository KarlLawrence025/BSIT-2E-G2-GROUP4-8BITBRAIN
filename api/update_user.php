<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

$id = intval($data['id']);
$fullname = isset($data['fullname']) ? $conn->real_escape_string($data['fullname']) : null;
$email = isset($data['email']) ? $conn->real_escape_string($data['email']) : null;
$username = isset($data['username']) ? $conn->real_escape_string($data['username']) : null;
$age = isset($data['age']) ? intval($data['age']) : null;
$account_type = isset($data['account_type']) ? $conn->real_escape_string($data['account_type']) : null;
$status = isset($data['status']) ? $conn->real_escape_string($data['status']) : null;

// Build update query dynamically
$updates = array();
if ($fullname !== null) $updates[] = "fullname = '$fullname'";
if ($email !== null) {
    // Check if email already exists for another user
    $check = "SELECT id FROM users WHERE email = '$email' AND id != $id";
    $result = $conn->query($check);
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        exit;
    }
    $updates[] = "email = '$email'";
}
if ($username !== null) {
    // Check if username already exists for another user
    $check = "SELECT id FROM users WHERE username = '$username' AND id != $id";
    $result = $conn->query($check);
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }
    $updates[] = "username = '$username'";
}
if ($age !== null) $updates[] = "age = $age";
if ($account_type !== null) $updates[] = "account_type = '$account_type'";
if ($status !== null) $updates[] = "status = '$status'";

if (empty($updates)) {
    echo json_encode([
        'success' => false,
        'message' => 'No fields to update'
    ]);
    exit;
}

$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating user: ' . $conn->error
    ]);
}

$conn->close();
?>
