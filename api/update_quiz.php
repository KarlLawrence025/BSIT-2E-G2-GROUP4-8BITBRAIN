<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
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
$title = isset($data['title']) ? $conn->real_escape_string($data['title']) : null;
$category = isset($data['category']) ? $conn->real_escape_string($data['category']) : null;
$difficulty = isset($data['difficulty']) ? $conn->real_escape_string($data['difficulty']) : null;

// Build update query dynamically
$updates = array();
if ($title !== null) $updates[] = "title = '$title'";
if ($category !== null) $updates[] = "category = '$category'";
if ($difficulty !== null) $updates[] = "difficulty = '$difficulty'";

if (empty($updates)) {
    echo json_encode([
        'success' => false,
        'message' => 'No fields to update'
    ]);
    exit;
}

$sql = "UPDATE quizzes SET " . implode(", ", $updates) . " WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Quiz updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating quiz: ' . $conn->error
    ]);
}

$conn->close();
?>
