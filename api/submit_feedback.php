<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['message']) || empty($data['message'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Feedback message is required'
    ]);
    exit;
}

$user_id = isset($data['user_id']) && $data['user_id'] !== null ? intval($data['user_id']) : null;
$name = isset($data['name']) ? $conn->real_escape_string($data['name']) : 'Anonymous';
$email = isset($data['email']) ? $conn->real_escape_string($data['email']) : null;
$feedback_text = $conn->real_escape_string($data['message']);
$feedback_type = isset($data['feedback_type']) ? $conn->real_escape_string($data['feedback_type']) : 'general';
$rating = isset($data['rating']) && $data['rating'] !== null ? intval($data['rating']) : null;

// Insert feedback
if ($user_id !== null) {
    // User is logged in
    $sql = "INSERT INTO feedback (user_id, feedback_text, rating, status) 
            VALUES ($user_id, '$feedback_text', " . ($rating !== null ? $rating : "NULL") . ", 'pending')";
} else {
    // Guest feedback - we'll store name and email in feedback_text for now
    // You could create a separate table for guest feedback if needed
    $guest_info = "[Guest: $name";
    if ($email) {
        $guest_info .= " | Email: $email";
    }
    $guest_info .= " | Type: $feedback_type]\n\n" . $feedback_text;
    
    $sql = "INSERT INTO feedback (user_id, feedback_text, rating, status) 
            VALUES (NULL, '$guest_info', " . ($rating !== null ? $rating : "NULL") . ", 'pending')";
}

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Feedback submitted successfully',
        'feedback_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting feedback: ' . $conn->error
    ]);
}

$conn->close();
?>
