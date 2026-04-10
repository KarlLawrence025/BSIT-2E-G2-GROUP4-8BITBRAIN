<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['admin_code']) || empty(trim($data['admin_code']))) {
    echo json_encode(['success' => false, 'message' => 'Admin authorization code is required']);
    exit;
}

$submitted_code = trim($data['admin_code']);

// Check submitted code against any existing admin's password
// An admin must authorize the new account by providing their own password + username/email
$identifier = isset($data['identifier']) ? $conn->real_escape_string(trim($data['identifier'])) : '';

if (empty($identifier)) {
    echo json_encode(['success' => false, 'message' => 'Admin username or email is required for verification']);
    exit;
}

// Find the authorizing admin by username or email
$sql = "SELECT id, username, password FROM users 
        WHERE account_type = 'admin' 
        AND (username = '$identifier' OR email = '$identifier')
        AND status = 'active'
        LIMIT 1";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No active admin found with that username or email']);
    exit;
}

$admin = $result->fetch_assoc();

// Verify the submitted code matches the admin's password
if ($admin['password'] !== $submitted_code) {
    echo json_encode(['success' => false, 'message' => 'Invalid admin password. Authorization denied']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Admin authorization verified',
    'authorized_by' => $admin['username']
]);

$conn->close();
?>
