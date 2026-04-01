<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Get ALL quizzes (no mode filter) with question count — used by admin dashboard
$sql = "SELECT q.id, q.title, q.category, q.difficulty, q.mode, q.created_at,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as question_count
        FROM quizzes q
        ORDER BY q.created_at DESC";

$result = $conn->query($sql);

$quizzes = array();
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $quizzes,
    'count' => count($quizzes)
]);

$conn->close();
?>
