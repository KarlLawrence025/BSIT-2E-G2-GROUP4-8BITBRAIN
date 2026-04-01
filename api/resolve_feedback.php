<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Feedback ID required']);
    exit;
}

$id  = intval($data['id']);
$sql = "UPDATE feedback SET status = 'resolved' WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Feedback resolved']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
