<?php
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Validate required fields
if (empty(trim($data['title'] ?? ''))) {
    echo json_encode(['success' => false, 'message' => 'Quiz title is required']);
    exit;
}
if (empty($data['questions']) || !is_array($data['questions'])) {
    echo json_encode(['success' => false, 'message' => 'At least one question is required']);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Insert quiz
    $title      = $conn->real_escape_string(trim($data['title']));
    $category   = $conn->real_escape_string(trim($data['category']   ?? 'General'));
    $difficulty = $conn->real_escape_string($data['difficulty'] ?? 'medium');
    $mode       = $conn->real_escape_string($data['mode']       ?? 'single_player');

    $stmt = $conn->prepare("INSERT INTO quizzes (title, category, difficulty, mode) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $category, $difficulty, $mode);
    if (!$stmt->execute()) throw new Exception("Quiz insert failed: " . $stmt->error);
    $quiz_id = $conn->insert_id;
    $stmt->close();

    // 2. Insert questions + answers
    $q_stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $a_stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");

    foreach ($data['questions'] as $q) {
        $q_text = trim($q['text'] ?? '');
        if (empty($q_text)) continue;

        $q_stmt->bind_param("is", $quiz_id, $q_text);
        if (!$q_stmt->execute()) throw new Exception("Question insert failed: " . $q_stmt->error);
        $question_id = $conn->insert_id;

        foreach ($q['options'] as $index => $opt) {
            $opt_text   = trim($opt['text'] ?? '');
            $is_correct = ($index === intval($q['correctAnswer'])) ? 1 : 0;
            $a_stmt->bind_param("isi", $question_id, $opt_text, $is_correct);
            if (!$a_stmt->execute()) throw new Exception("Answer insert failed: " . $a_stmt->error);
        }
    }

    $q_stmt->close();
    $a_stmt->close();

    // 3. Insert reference if provided
    $ref_url  = trim($data['reference_url']  ?? '');
    $ref_text = trim($data['reference_text'] ?? '');
    $ref_type = trim($data['reference_type'] ?? 'url');

    if (!empty($ref_url) || !empty($ref_text)) {
        $r_stmt = $conn->prepare(
            "INSERT INTO quiz_references (quiz_id, question_id, reference_text, reference_url, reference_type)
             VALUES (?, NULL, ?, ?, ?)"
        );
        $r_stmt->bind_param("isss", $quiz_id, $ref_text, $ref_url, $ref_type);
        if (!$r_stmt->execute()) throw new Exception("Reference insert failed: " . $r_stmt->error);
        $r_stmt->close();
    }

    $conn->commit();

    echo json_encode([
        'success'  => true,
        'message'  => 'Quiz saved successfully!',
        'quiz_id'  => $quiz_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
