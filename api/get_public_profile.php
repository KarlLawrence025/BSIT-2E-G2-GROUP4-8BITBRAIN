<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$user_id = intval($_GET['id'] ?? 0);
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

// ── User info ─────────────────────────────────────────────────────────────────
$uRes = $conn->query(
    "SELECT id, fullname, username, account_type, avatar, created_at
     FROM users WHERE id = $user_id AND account_type = 'user'"
);
if (!$uRes || $uRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$user = $uRes->fetch_assoc();

// ── All-time stats ────────────────────────────────────────────────────────────
$aRes = $conn->query(
    "SELECT
        COUNT(id)                        AS total_attempts,
        COALESCE(SUM(correct),       0)  AS total_correct,
        COALESCE(SUM(total),         0)  AS total_questions,
        COALESCE(SUM(points_earned), 0)  AS total_points,
        COALESCE(MAX(points_earned), 0)  AS best_score
     FROM quiz_attempts WHERE user_id = $user_id"
);
$stats = $aRes->fetch_assoc();

$total_points    = intval($stats['total_points']);
$total_correct   = intval($stats['total_correct']);
$total_questions = intval($stats['total_questions']);
$total_attempts  = intval($stats['total_attempts']);
$best_score      = intval($stats['best_score']);
$accuracy        = $total_questions > 0
    ? round(($total_correct / $total_questions) * 100, 1) : 0;

$level       = max(1, floor($total_points / 500) + 1);
$xp_in_level = $total_points % 500;
$xp_pct      = round(($xp_in_level / 500) * 100);

// ── Global rank ───────────────────────────────────────────────────────────────
$rankRes = $conn->query(
    "SELECT COUNT(*) + 1 AS rnk
     FROM (SELECT user_id, SUM(points_earned) AS pts
           FROM quiz_attempts GROUP BY user_id) ranked
     WHERE ranked.pts > $total_points"
);
$global_rank = intval($rankRes->fetch_assoc()['rnk'] ?? 0);

// ── Stats per mode ────────────────────────────────────────────────────────────
$modeRes = $conn->query(
    "SELECT mode,
            COUNT(id)                    AS attempts,
            COALESCE(SUM(points_earned),0) AS pts,
            COALESCE(SUM(correct),      0) AS correct,
            COALESCE(SUM(total),        0) AS total
     FROM quiz_attempts
     WHERE user_id = $user_id
     GROUP BY mode
     ORDER BY pts DESC"
);
$by_mode = [];
while ($row = $modeRes->fetch_assoc()) $by_mode[] = $row;

// ── Recent 5 attempts ─────────────────────────────────────────────────────────
$rRes = $conn->query(
    "SELECT qa.mode, qa.correct, qa.total, qa.points_earned, qa.created_at,
            q.title AS quiz_title
     FROM quiz_attempts qa
     LEFT JOIN quizzes q ON q.id = qa.quiz_id
     WHERE qa.user_id = $user_id
     ORDER BY qa.created_at DESC LIMIT 5"
);
$recent = [];
while ($r = $rRes->fetch_assoc()) $recent[] = $r;

echo json_encode([
    'success' => true,
    'user'    => [
        'id'           => $user['id'],
        'fullname'     => $user['fullname'],
        'username'     => $user['username'],
        'avatar'       => $user['avatar'],
        'member_since' => date('M Y', strtotime($user['created_at']))
    ],
    'stats'   => [
        'total_points'    => $total_points,
        'total_correct'   => $total_correct,
        'total_questions' => $total_questions,
        'total_attempts'  => $total_attempts,
        'best_score'      => $best_score,
        'accuracy'        => $accuracy,
        'level'           => $level,
        'xp_in_level'     => $xp_in_level,
        'xp_pct'          => $xp_pct,
        'global_rank'     => $global_rank
    ],
    'by_mode' => $by_mode,
    'recent'  => $recent
]);

$conn->close();
?>
