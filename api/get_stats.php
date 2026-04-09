<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$users_result    = $conn->query("SELECT COUNT(*) as total FROM users");
$quizzes_result  = $conn->query("SELECT COUNT(*) as total FROM quizzes");
$feedback_result = $conn->query("SELECT COUNT(*) as total FROM feedback WHERE status = 'pending'");
$total_users     = $users_result->fetch_assoc()['total'];
$total_quizzes   = $quizzes_result->fetch_assoc()['total'];
$pending_feedback= $feedback_result->fetch_assoc()['total'];

$activity_sql = "(SELECT 'user' as type, fullname as name, created_at FROM users ORDER BY created_at DESC LIMIT 5)
                 UNION ALL
                 (SELECT 'quiz' as type, title as name, created_at FROM quizzes ORDER BY created_at DESC LIMIT 5)
                 ORDER BY created_at DESC LIMIT 10";
$activity_result = $conn->query($activity_sql);
$activity = [];
while($row = $activity_result->fetch_assoc()) $activity[] = $row;

echo json_encode(['success'=>true,'data'=>[
    'total_users'=>$total_users,'total_quizzes'=>$total_quizzes,
    'pending_feedback'=>$pending_feedback,'recent_activity'=>$activity
]]);
$conn->close();
?>
