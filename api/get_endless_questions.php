<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Pull ALL questions from ALL quizzes (ignore mode), shuffled
// Only include questions that actually have answers
$sql = "SELECT
            q.id          AS question_id,
            q.question_text,
            qz.title      AS quiz_title,
            qz.category,
            qz.difficulty
        FROM questions q
        JOIN quizzes qz ON qz.id = q.quiz_id
        WHERE (
            SELECT COUNT(*) FROM answers a WHERE a.question_id = q.id
        ) > 0
        ORDER BY RAND()";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit;
}

$questions = [];
while ($row = $result->fetch_assoc()) {
    // Fetch answers for each question
    $qid  = intval($row['question_id']);
    $aRes = $conn->query(
        "SELECT id, answer_text, is_correct FROM answers WHERE question_id = $qid ORDER BY RAND()"
    );
    $answers = [];
    while ($a = $aRes->fetch_assoc()) {
        $answers[] = [
            'id'         => $a['id'],
            'text'       => $a['answer_text'],
            'is_correct' => intval($a['is_correct'])
        ];
    }

    $questions[] = [
        'id'         => $qid,
        'question'   => $row['question_text'],
        'quiz_title' => $row['quiz_title'],
        'category'   => $row['category'],
        'difficulty' => $row['difficulty'],
        'answers'    => $answers
    ];
}

if (count($questions) === 0) {
    echo json_encode(['success' => false, 'message' => 'No questions in the database yet. Ask your admin to create some quizzes!']);
    exit;
}

echo json_encode([
    'success' => true,
    'data'    => $questions,
    'count'   => count($questions)
]);

$conn->close();
?>
