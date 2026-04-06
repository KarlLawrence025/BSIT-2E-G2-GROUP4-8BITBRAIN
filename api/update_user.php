<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$id = intval($data['id']);

// ── Build update fields dynamically ──────────────────────────────────────────
$updates = [];

if (isset($data['fullname']) && trim($data['fullname']) !== '') {
    $v = $conn->real_escape_string(trim($data['fullname']));
    $updates[] = "fullname = '$v'";
}

if (isset($data['email']) && trim($data['email']) !== '') {
    $v = $conn->real_escape_string(trim($data['email']));
    // Check duplicate email (excluding this user)
    $check = $conn->query("SELECT id FROM users WHERE email = '$v' AND id != $id");
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }
    $updates[] = "email = '$v'";
}

if (isset($data['username']) && trim($data['username']) !== '') {
    $v = $conn->real_escape_string(trim($data['username']));
    // Check duplicate username (excluding this user)
    $check = $conn->query("SELECT id FROM users WHERE username = '$v' AND id != $id");
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    $updates[] = "username = '$v'";
}

if (isset($data['age'])) {
    $updates[] = "age = " . intval($data['age']);
}

if (isset($data['account_type']) && trim($data['account_type']) !== '') {
    $v = $conn->real_escape_string($data['account_type']);
    $updates[] = "account_type = '$v'";
}

// ── Password — only update if admin actually provided one ─────────────────────
if (isset($data['password']) && trim($data['password']) !== '') {
    $pwd = trim($data['password']);

    if (strlen($pwd) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    $v = $conn->real_escape_string($pwd);
    $updates[] = "password = '$v'";
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    exit;
}

$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $conn->error]);
}

$conn->close();
?>
