<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) { echo json_encode(['success'=>false,'message'=>'User ID is required']); exit; }
$id = intval($data['id']);
$check = $conn->query("SELECT id FROM users WHERE id = $id");
if ($check->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'User not found']); exit; }
if ($conn->query("DELETE FROM users WHERE id = $id") === TRUE) {
    echo json_encode(['success'=>true,'message'=>'User deleted successfully']);
} else {
    echo json_encode(['success'=>false,'message'=>'Error: '.$conn->error]);
}
$conn->close();
?>
