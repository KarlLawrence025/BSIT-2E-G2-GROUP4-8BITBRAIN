<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Get total users
$users_sql = "SELECT COUNT(*) as total FROM users";
$users_result = $conn->query($users_sql);
$total_users = $users_result->fetch_assoc()['total'];

// Get total quizzes
$quizzes_sql = "SELECT COUNT(*) as total FROM quizzes";
$quizzes_result = $conn->query($quizzes_sql);
$total_quizzes = $quizzes_result->fetch_assoc()['total'];

// Get pending feedback count
$feedback_sql = "SELECT COUNT(*) as total FROM feedback WHERE status = 'pending'";
$feedback_result = $conn->query($feedback_sql);
$pending_feedback = $feedback_result->fetch_assoc()['total'];

// Get recent activity (last 10 records)
$activity_sql = "
    (SELECT 'user' as type, fullname as name, created_at FROM users ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT 'quiz' as type, title as name, created_at FROM quizzes ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 10
";
$activity_result = $conn->query($activity_sql);
$activity = array();
while($row = $activity_result->fetch_assoc()) {
    $activity[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => [
        'total_users' => $total_users,
        'total_quizzes' => $total_quizzes,
        'pending_feedback' => $pending_feedback,
        'recent_activity' => $activity
    ]
]);

$conn->close();
?>
