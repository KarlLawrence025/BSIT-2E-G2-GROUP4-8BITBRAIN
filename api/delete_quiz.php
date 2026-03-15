<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz ID is required'
    ]);
    exit;
}

$id = intval($data['id']);

// Check if quiz exists
$check = "SELECT id FROM quizzes WHERE id = $id";
$result = $conn->query($check);

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz not found'
    ]);
    exit;
}

// Delete quiz (CASCADE will auto-delete related questions, answers, etc.)
$sql = "DELETE FROM quizzes WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Quiz deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting quiz: ' . $conn->error
    ]);
}

$conn->close();
?>
