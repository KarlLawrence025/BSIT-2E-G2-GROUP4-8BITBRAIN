<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

$required = ['user_id', 'score', 'mode'];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

$user_id = intval($data['user_id']);
$mode = $conn->real_escape_string(trim($data['mode']));
$score = intval($data['score']);
$correct = isset($data['correct']) ? intval($data['correct']) : null;
$total = isset($data['total']) ? intval($data['total']) : null;

$userRes = $conn->query("SELECT id, username, fullname FROM users WHERE id = $user_id LIMIT 1");
if (!$userRes || $userRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$user = $userRes->fetch_assoc();

$username = $conn->real_escape_string($user['username']);
$fullname = $conn->real_escape_string($user['fullname']);

$sql = "INSERT INTO leaderboard (user_id, username, fullname, mode, score, correct, total) VALUES ($user_id, '$username', '$fullname', '$mode', $score, " . ($correct===null ? 'NULL' : $correct) . ", " . ($total===null ? 'NULL' : $total) . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Score recorded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>