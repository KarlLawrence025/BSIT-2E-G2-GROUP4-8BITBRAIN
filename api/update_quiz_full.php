<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$quiz_id = intval($data['id'] ?? 0);
if (!$quiz_id) {
    echo json_encode(['success' => false, 'message' => 'Quiz ID required']);
    exit;
}

// Check quiz exists
$check = $conn->query("SELECT id FROM quizzes WHERE id = $quiz_id");
if (!$check || $check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Quiz not found']);
    exit;
}

$title      = $conn->real_escape_string(trim($data['title']      ?? ''));
$category   = $conn->real_escape_string(trim($data['category']   ?? ''));
$difficulty = $conn->real_escape_string($data['difficulty'] ?? 'medium');
$mode       = $conn->real_escape_string($data['mode']       ?? 'single_player');
$questions  = $data['questions'] ?? [];

if (empty($title))      { echo json_encode(['success'=>false,'message'=>'Title is required']); exit; }
if (empty($questions))  { echo json_encode(['success'=>false,'message'=>'At least one question is required']); exit; }

$conn->begin_transaction();

try {
    // 1. Update quiz meta
    $conn->query(
        "UPDATE quizzes SET
            title='$title', category='$category',
            difficulty='$difficulty', mode='$mode'
         WHERE id = $quiz_id"
    );

    // 2. Delete existing questions (CASCADE deletes answers too)
    $conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");

    // 3. Re-insert questions + answers
    $qStmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $aStmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");

    foreach ($questions as $q) {
        $qText = trim($q['text'] ?? '');
        if (empty($qText)) continue;

        $qStmt->bind_param("is", $quiz_id, $qText);
        if (!$qStmt->execute()) throw new Exception("Question insert failed: " . $qStmt->error);
        $question_id = $conn->insert_id;

        foreach ($q['options'] as $index => $opt) {
            $optText    = trim($opt['text'] ?? '');
            $isCorrect  = ($index === intval($q['correctAnswer'])) ? 1 : 0;
            $aStmt->bind_param("isi", $question_id, $optText, $isCorrect);
            if (!$aStmt->execute()) throw new Exception("Answer insert failed: " . $aStmt->error);
        }
    }

    $qStmt->close();
    $aStmt->close();
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Quiz updated successfully!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
}

$conn->close();
?>
