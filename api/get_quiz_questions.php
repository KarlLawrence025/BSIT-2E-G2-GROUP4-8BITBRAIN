<?php
require_once '../db.php';
header('Content-Type: application/json');

$quizId = intval($_GET['quiz_id'] ?? 0);

// Get quiz info
$quizResult = $conn->query("SELECT * FROM quizzes WHERE id = $quizId");
$quiz = $quizResult->fetch_assoc();

// Get questions + answers
$sql = "SELECT q.id AS question_id, q.question_text,
               a.id AS answer_id, a.answer_text, a.is_correct
        FROM questions q
        JOIN answers a ON q.id = a.question_id
        WHERE q.quiz_id = $quizId";

$result = $conn->query($sql);

$questions = [];
while ($row = $result->fetch_assoc()) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'id' => $qid,
            'question' => $row['question_text'],
            'answers' => []
        ];
    }
    $questions[$qid]['answers'][] = [
        'id' => $row['answer_id'],
        'text' => $row['answer_text'],
        'is_correct' => $row['is_correct']
    ];
}

echo json_encode([
    'success' => true,
    'quiz' => $quiz,
    'questions' => array_values($questions)
]);

$conn->close();
?>
