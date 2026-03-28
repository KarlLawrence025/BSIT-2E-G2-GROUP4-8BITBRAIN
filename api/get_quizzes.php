<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Get mode from query string (default: single_player)
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'single_player';

// Get quizzes filtered by mode, with question count
$sql = "SELECT q.id, q.title, q.category, q.difficulty, q.created_at,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as question_count
        FROM quizzes q
        WHERE q.mode = ?
        ORDER BY q.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mode);
$stmt->execute();
$result = $stmt->get_result();

$quizzes = array();
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $quizzes,
    'count' => count($quizzes)
]);

$stmt->close();
$conn->close();
?>
