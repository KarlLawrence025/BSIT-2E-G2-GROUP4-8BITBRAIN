<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

session_start();
require_once '../db.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get current avatar
$res = $conn->query("SELECT avatar FROM users WHERE id = $user_id");
if ($res && $res->num_rows > 0) {
    $avatar = $res->fetch_assoc()['avatar'];
    if ($avatar) {
        $path = __DIR__ . '/../' . $avatar;
        if (file_exists($path)) unlink($path);
    }
}

// Clear from DB
$conn->query("UPDATE users SET avatar = NULL WHERE id = $user_id");

echo json_encode(['success' => true, 'message' => 'Profile picture removed']);
$conn->close();
?>
