<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
session_start();
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['feedback_text']) || empty(trim($data['feedback_text']))) {
    echo json_encode(['success'=>false,'message'=>'Feedback text is required']); exit;
}

$feedback_text = $conn->real_escape_string(trim($data['feedback_text']));
$feedback_type = isset($data['feedback_type']) ? $conn->real_escape_string($data['feedback_type']) : 'general';
$rating        = isset($data['rating']) && $data['rating'] !== null ? intval($data['rating']) : null;
$quiz_id       = isset($data['quiz_id']) && $data['quiz_id'] !== null ? intval($data['quiz_id']) : null;
$user_id       = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($data['user_id']) ? intval($data['user_id']) : null);

$rating_sql  = $rating  !== null ? $rating  : 'NULL';
$quiz_id_sql = $quiz_id !== null ? $quiz_id : 'NULL';
$user_id_sql = $user_id !== null ? $user_id : 'NULL';

$sql = "INSERT INTO feedback (user_id, quiz_id, feedback_text, feedback_type, rating, status)
        VALUES ($user_id_sql, $quiz_id_sql, '$feedback_text', '$feedback_type', $rating_sql, 'pending')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success'=>true,'message'=>'Feedback submitted','feedback_id'=>$conn->insert_id]);
} else {
    echo json_encode(['success'=>false,'message'=>'Error: '.$conn->error]);
}
$conn->close();
?>
