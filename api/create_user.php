<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['fullname']) || !isset($data['email']) || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// ── Sanitise ──────────────────────────────────────────────────────────────────
$fullname     = $conn->real_escape_string(trim($data['fullname']));
$email        = $conn->real_escape_string(trim($data['email']));
$username     = $conn->real_escape_string(trim($data['username']));
$age          = isset($data['age']) ? intval($data['age']) : 18;
$account_type = isset($data['account_type']) ? $conn->real_escape_string($data['account_type']) : 'user';

// ── Use exactly the password the admin typed — no override ────────────────────
$password = trim($data['password']);

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

// Escape the password for the SQL query
$password_escaped = $conn->real_escape_string($password);

// ── Duplicate checks ──────────────────────────────────────────────────────────
$check = $conn->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

$check2 = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check2->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    exit;
}

// ── Insert ────────────────────────────────────────────────────────────────────
$sql = "INSERT INTO users (fullname, email, username, age, password, account_type)
        VALUES ('$fullname', '$email', '$username', $age, '$password_escaped', '$account_type')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => "User '$username' created successfully",
        'user_id' => $conn->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating user: ' . $conn->error]);
}

$conn->close();
?>
