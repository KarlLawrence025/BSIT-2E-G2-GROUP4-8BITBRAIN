<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['title']) || !isset($data['category']) || !isset($data['difficulty'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields (title, category, difficulty)'
    ]);
    exit;
}

$title = $conn->real_escape_string($data['title']);
$category = $conn->real_escape_string($data['category']);
$difficulty = $conn->real_escape_string($data['difficulty']);
$created_by = isset($data['created_by']) ? intval($data['created_by']) : 1; // Default to admin (ID 1)
$reference_url = isset($data['reference_url']) && !empty($data['reference_url']) ? $conn->real_escape_string($data['reference_url']) : null;

// Start transaction
$conn->begin_transaction();

try {
    // Insert quiz
    $sql = "INSERT INTO quizzes (title, category, difficulty, created_by) 
            VALUES ('$title', '$category', '$difficulty', $created_by)";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating quiz: " . $conn->error);
    }
    
    $quiz_id = $conn->insert_id;
    
    // Insert reference link if provided
    if ($reference_url !== null) {
        $ref_sql = "INSERT INTO quiz_references (quiz_id, reference_url, reference_type) 
                    VALUES ($quiz_id, '$reference_url', 'url')";
        
        if (!$conn->query($ref_sql)) {
            throw new Exception("Error saving reference: " . $conn->error);
        }
    }
    
    // Insert questions if provided
    if (isset($data['questions']) && is_array($data['questions'])) {
        foreach ($data['questions'] as $index => $question) {
            $question_text = $conn->real_escape_string($question['text']);
            $question_order = $index + 1;
            
            $q_sql = "INSERT INTO questions (quiz_id, question_text, question_order) 
                      VALUES ($quiz_id, '$question_text', $question_order)";
            
            if (!$conn->query($q_sql)) {
                throw new Exception("Error creating question: " . $conn->error);
            }
            
            $question_id = $conn->insert_id;
            
            // Insert answers if provided
            if (isset($question['options']) && is_array($question['options'])) {
                foreach ($question['options'] as $opt_index => $option) {
                    $answer_text = $conn->real_escape_string($option['text']);
                    $is_correct = ($opt_index == $question['correctAnswer']) ? 1 : 0;
                    
                    $a_sql = "INSERT INTO answers (question_id, answer_text, is_correct, answer_index) 
                              VALUES ($question_id, '$answer_text', $is_correct, $opt_index)";
                    
                    if (!$conn->query($a_sql)) {
                        throw new Exception("Error creating answer: " . $conn->error);
                    }
                }
            }
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Quiz created successfully',
        'quiz_id' => $quiz_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
