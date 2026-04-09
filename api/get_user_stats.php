<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();
require_once '../db.php';

// Get user_id from session or query param
$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
}

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get user info
$userRes = $conn->query("SELECT id, fullname, username, email, account_type, created_at FROM users WHERE id = $user_id LIMIT 1");
if (!$userRes || $userRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$user = $userRes->fetch_assoc();

// Get leaderboard stats
$lbRes = $conn->query("SELECT total_points, total_correct, total_questions, attempts FROM leaderboard WHERE user_id = $user_id LIMIT 1");
$lb = $lbRes && $lbRes->num_rows > 0 ? $lbRes->fetch_assoc() : [
    'total_points' => 0,
    'total_correct' => 0,
    'total_questions' => 0,
    'attempts' => 0
];

// Get rank
$rankRes = $conn->query("SELECT COUNT(*) + 1 AS user_rank FROM leaderboard WHERE total_points > (SELECT COALESCE(total_points, 0) FROM leaderboard WHERE user_id = $user_id)");
$rank = $rankRes ? $rankRes->fetch_assoc()['user_rank'] : 'N/A';

// Get recent quiz attempts
$attemptsRes = $conn->query("
    SELECT qa.correct, qa.total, qa.points_earned, qa.mode, qa.created_at, q.title
    FROM quiz_attempts qa
    LEFT JOIN quizzes q ON q.id = qa.quiz_id
    WHERE qa.user_id = $user_id
    ORDER BY qa.created_at DESC
    LIMIT 5
");
$recentAttempts = [];
if ($attemptsRes) {
    while ($row = $attemptsRes->fetch_assoc()) {
        $recentAttempts[] = $row;
    }
}

// Compute level (every 500 points = 1 level)
$level = max(1, floor($lb['total_points'] / 500) + 1);
$xpProgress = ($lb['total_points'] % 500) / 500 * 100;

echo json_encode([
    'success' => true,
    'data' => [
        'user'           => $user,
        'total_points'   => (int)$lb['total_points'],
        'total_correct'  => (int)$lb['total_correct'],
        'total_questions'=> (int)$lb['total_questions'],
        'attempts'       => (int)$lb['attempts'],
        'rank'           => $rank,
        'level'          => $level,
        'xp_progress'    => round($xpProgress, 1),
        'recent_attempts'=> $recentAttempts
    ]
]);

$conn->close();
?>
