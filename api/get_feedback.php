<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Get all feedback with user and quiz information
$sql = "SELECT f.id, f.feedback_text, f.rating, f.status, f.created_at,
        u.fullname as user_name, u.username,
        q.title as quiz_title
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.id
        LEFT JOIN quizzes q ON f.quiz_id = q.id
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);

$feedback = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $feedback,
    'count' => count($feedback)
]);

$conn->close();
?>
