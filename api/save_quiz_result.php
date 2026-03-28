<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Read JSON body
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$quizId = intval($input['quiz_id'] ?? 0);
$mode = $input['mode'] ?? 'standard';
$score = intval($input['score'] ?? 0);
$correct = intval($input['correct_answers'] ?? 0);
$total = intval($input['total_questions'] ?? 0);
$timeTaken = intval($input['time_taken'] ?? 0);
$userId = intval($input['user_id'] ?? 0);
$userStmt = $conn->prepare("SELECT username, fullname FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result()->fetch_assoc();
$username = $userResult['username'] ?? '';
$fullname = $userResult['fullname'] ?? '';
$userStmt->close();


$stmt = $conn->prepare("INSERT INTO leaderboard (user_id, username, fullname, mode, score, correct, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssiii", $userId, $username, $fullname, $mode, $score, $correct, $total);


if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
