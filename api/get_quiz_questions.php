<?php
require_once '../db.php';
header('Content-Type: application/json');

$quizId = intval($_GET['quiz_id'] ?? 0);
if ($quizId <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid quiz ID']); exit; }

$quizResult = $conn->query("SELECT * FROM quizzes WHERE id = $quizId");
if (!$quizResult || $quizResult->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Quiz not found']); exit; }
$quiz = $quizResult->fetch_assoc();

$sql = "SELECT q.id AS question_id, q.question_text, a.id AS answer_id, a.answer_text, a.is_correct
        FROM questions q LEFT JOIN answers a ON a.question_id = q.id
        WHERE q.quiz_id = $quizId ORDER BY q.id ASC, a.id ASC";
$result = $conn->query($sql);
if (!$result) { echo json_encode(['success'=>false,'message'=>'Query error: '.$conn->error]); exit; }

$questions = [];
while ($row = $result->fetch_assoc()) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = ['id'=>$qid,'question'=>$row['question_text'],'answers'=>[]];
    }
    if ($row['answer_id'] !== null) {
        $questions[$qid]['answers'][] = ['id'=>$row['answer_id'],'text'=>$row['answer_text'],'is_correct'=>intval($row['is_correct'])];
    }
}
$questions = array_values($questions);
$valid = array_values(array_filter($questions, fn($q) => count($q['answers']) > 0));

if (count($valid) === 0) {
    echo json_encode(['success'=>false,'message'=>'This quiz has no valid questions with answers yet.']);
    exit;
}
echo json_encode(['success'=>true,'quiz'=>$quiz,'questions'=>$valid,'count'=>count($valid)]);
$conn->close();
?>
