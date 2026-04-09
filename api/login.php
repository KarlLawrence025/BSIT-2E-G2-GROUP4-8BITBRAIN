<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success'=>false,'message'=>'Email and password are required']); exit;
}

$email    = $conn->real_escape_string($data['email']);
$password = $data['password'];
$sql      = "SELECT id, fullname, email, username, account_type, password FROM users WHERE email = '$email'";
$result   = $conn->query($sql);

if ($result->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Invalid email or password']); exit; }
$user = $result->fetch_assoc();
if ($user['password'] !== $password) { echo json_encode(['success'=>false,'message'=>'Invalid email or password']); exit; }

$_SESSION['logged_in']   = true;
$_SESSION['user_id']     = $user['id'];
$_SESSION['username']    = $user['username'];
$_SESSION['fullname']    = $user['fullname'];
$_SESSION['account_type']= $user['account_type'];

echo json_encode(['success'=>true,'message'=>'Login successful','user'=>[
    'id'=>$user['id'],'fullname'=>$user['fullname'],'email'=>$user['email'],
    'username'=>$user['username'],'account_type'=>$user['account_type']
]]);
$conn->close();
?>
