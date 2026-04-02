<?php
header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Insert into 'quizzes'
    $stmt = $conn->prepare("INSERT INTO quizzes (title, category, difficulty, mode) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['title'], $data['category'], $data['difficulty'], $data['mode']);
    
    if (!$stmt->execute()) throw new Exception("Quiz Insert Failed: " . $stmt->error);
    $quiz_id = $conn->insert_id; 
    $stmt->close();

    // 2. Insert into 'questions'
    $q_stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    
    // 3. Insert into 'answers' (Using your EXACT column names: answer_text, is_correct)
    $a_stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");

    foreach ($data['questions'] as $q) {
        $q_stmt->bind_param("is", $quiz_id, $q['text']);
        if (!$q_stmt->execute()) throw new Exception("Question Insert Failed: " . $q_stmt->error);
        $question_id = $conn->insert_id; 

        foreach ($q['options'] as $index => $opt) {
            $is_correct = ($index === intval($q['correctAnswer'])) ? 1 : 0;
            
            // Match these to your screenshot: question_id, answer_text, is_correct
            $a_stmt->bind_param("isi", $question_id, $opt['text'], $is_correct);
            if (!$a_stmt->execute()) throw new Exception("Answer Insert Failed: " . $a_stmt->error);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Saved to Database!', 'quiz_id' => $quiz_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'DATABASE ERROR: ' . $e->getMessage()]);
}

$conn->close();
?>