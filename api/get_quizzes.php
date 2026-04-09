<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$mode = isset($_GET['mode']) && $_GET['mode'] !== ''
        ? $conn->real_escape_string($_GET['mode'])
        : null;

if ($mode) {
    $sql = "SELECT q.id, q.title, q.category, q.difficulty, q.mode, q.created_at,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
            FROM quizzes q WHERE q.mode = '$mode' ORDER BY q.created_at DESC";
} else {
    $sql = "SELECT q.id, q.title, q.category, q.difficulty, q.mode, q.created_at,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
            FROM quizzes q ORDER BY q.created_at DESC";
}

$result  = $conn->query($sql);
$quizzes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) $quizzes[] = $row;
}
echo json_encode(['success'=>true,'data'=>$quizzes,'count'=>count($quizzes),'mode_filter'=>$mode??'all']);
$conn->close();
?>
