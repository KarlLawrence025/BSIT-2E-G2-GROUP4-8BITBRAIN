<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$sql = "SELECT
            f.id,
            f.feedback_text,
            f.feedback_type,
            f.rating,
            f.status,
            f.created_at,
            COALESCE(u.fullname, u.username, 'Anonymous') AS user_name,
            u.username,
            q.title      AS quiz_title,
            q.category   AS quiz_category,
            q.mode       AS quiz_mode,
            q.difficulty AS quiz_difficulty
        FROM feedback f
        LEFT JOIN users   u ON f.user_id = u.id
        LEFT JOIN quizzes q ON f.quiz_id = q.id
        ORDER BY f.created_at DESC";

$result   = $conn->query($sql);
$feedback = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data'    => $feedback,
    'count'   => count($feedback)
]);

$conn->close();
?>
