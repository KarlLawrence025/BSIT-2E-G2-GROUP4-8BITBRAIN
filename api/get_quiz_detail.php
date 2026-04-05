<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$quiz_id = intval($_GET['id'] ?? 0);
if (!$quiz_id) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID required']);
    exit;
}

// Get quiz info
$quizRes = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id");
if (!$quizRes || $quizRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Quiz not found']);
    exit;
}
$quiz = $quizRes->fetch_assoc();

// Get all questions with their answers
$qRes = $conn->query(
    "SELECT q.id AS question_id, q.question_text,
            a.id AS answer_id, a.answer_text, a.is_correct
     FROM questions q
     LEFT JOIN answers a ON a.question_id = q.id
     WHERE q.quiz_id = $quiz_id
     ORDER BY q.id ASC, a.id ASC"
);

$questionsMap = [];
while ($row = $qRes->fetch_assoc()) {
    $qid = $row['question_id'];
    if (!isset($questionsMap[$qid])) {
        $questionsMap[$qid] = [
            'id'       => $qid,
            'text'     => $row['question_text'],
            'answers'  => []
        ];
    }
    if ($row['answer_id']) {
        $questionsMap[$qid]['answers'][] = [
            'id'         => $row['answer_id'],
            'text'       => $row['answer_text'],
            'is_correct' => intval($row['is_correct'])
        ];
    }
}

echo json_encode([
    'success'   => true,
    'quiz'      => $quiz,
    'questions' => array_values($questionsMap)
]);

$conn->close();
?>
