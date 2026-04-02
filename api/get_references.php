<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$sql = "SELECT
            qr.id,
            qr.quiz_id,
            qr.question_id,
            qr.reference_text,
            qr.reference_url,
            qr.reference_type,
            qr.created_at,
            q.title        AS quiz_title,
            q.category     AS quiz_category,
            q.difficulty   AS quiz_difficulty,
            q.mode         AS quiz_mode,
            COALESCE(qs.question_text, 'General Quiz Reference') AS question_text
        FROM quiz_references qr
        LEFT JOIN quizzes   q  ON qr.quiz_id     = q.id
        LEFT JOIN questions qs ON qr.question_id = qs.id
        ORDER BY qr.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$references = [];
while ($row = $result->fetch_assoc()) {
    $references[] = $row;
}

echo json_encode([
    'success' => true,
    'data'    => $references,
    'count'   => count($references)
]);

$conn->close();
?>
