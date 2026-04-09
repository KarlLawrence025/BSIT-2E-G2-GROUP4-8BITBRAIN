<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) { echo json_encode(['success'=>false,'message'=>'ID required']); exit; }
$id = intval($data['id']);
if ($conn->query("DELETE FROM feedback WHERE id=$id") === TRUE) {
    echo json_encode(['success'=>true,'message'=>'Feedback deleted']);
} else {
    echo json_encode(['success'=>false,'message'=>$conn->error]);
}
$conn->close();
?>
