<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();
require_once '../db.php';

// Accept user_id from session or GET param
$user_id = isset($_SESSION['user_id'])
    ? intval($_SESSION['user_id'])
    : intval($_GET['user_id'] ?? 0);

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// ── User info ─────────────────────────────────────────────────────────────────
$uRes  = $conn->query("SELECT id, fullname, username, age, account_type, created_at FROM users WHERE id = $user_id");
if (!$uRes || $uRes->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$user = $uRes->fetch_assoc();

// ── Quiz attempt totals ───────────────────────────────────────────────────────
$aRes  = $conn->query(
    "SELECT
        COUNT(id)          AS total_attempts,
        SUM(correct)       AS total_correct,
        SUM(total)         AS total_questions,
        SUM(points_earned) AS total_points,
        MAX(points_earned) AS best_score
     FROM quiz_attempts
     WHERE user_id = $user_id"
);
$stats = $aRes ? $aRes->fetch_assoc() : [];

$total_attempts  = intval($stats['total_attempts']  ?? 0);
$total_correct   = intval($stats['total_correct']   ?? 0);
$total_questions = intval($stats['total_questions'] ?? 0);
$total_points    = intval($stats['total_points']    ?? 0);
$best_score      = intval($stats['best_score']      ?? 0);
$accuracy        = $total_questions > 0 ? round(($total_correct / $total_questions) * 100, 1) : 0;

// ── Best streak from attempts (use streak field if stored, else approximate) ──
// We'll approximate level from points: every 500 pts = 1 level
$level = max(1, floor($total_points / 500) + 1);
$xp_in_level    = $total_points % 500;
$xp_pct         = round(($xp_in_level / 500) * 100);

// ── Favourite mode (most attempts in one mode) ────────────────────────────────
$mRes = $conn->query(
    "SELECT mode, COUNT(*) AS cnt
     FROM quiz_attempts
     WHERE user_id = $user_id
     GROUP BY mode
     ORDER BY cnt DESC
     LIMIT 1"
);
$fav_mode = $mRes && $mRes->num_rows > 0 ? $mRes->fetch_assoc()['mode'] : null;

// ── Recent attempts (last 5) ──────────────────────────────────────────────────
$rRes = $conn->query(
    "SELECT qa.mode, qa.correct, qa.total, qa.points_earned, qa.created_at,
            q.title AS quiz_title
     FROM quiz_attempts qa
     LEFT JOIN quizzes q ON q.id = qa.quiz_id
     WHERE qa.user_id = $user_id
     ORDER BY qa.created_at DESC
     LIMIT 5"
);
$recent = [];
while ($r = $rRes->fetch_assoc()) $recent[] = $r;

// ── Global rank (by total points across all attempts) ─────────────────────────
$rankRes = $conn->query(
    "SELECT ranked.user_id, ranked.rnk
     FROM (
         SELECT user_id, RANK() OVER (ORDER BY SUM(points_earned) DESC) AS rnk
         FROM quiz_attempts
         GROUP BY user_id
     ) ranked
     WHERE ranked.user_id = $user_id"
);
$global_rank = $rankRes && $rankRes->num_rows > 0
    ? intval($rankRes->fetch_assoc()['rnk'])
    : null;

// ── Top 3 leaderboard (all modes) ─────────────────────────────────────────────
$lbRes = $conn->query(
    "SELECT u.fullname, u.username, SUM(qa.points_earned) AS pts
     FROM quiz_attempts qa
     JOIN users u ON u.id = qa.user_id
     GROUP BY qa.user_id, u.fullname, u.username
     ORDER BY pts DESC
     LIMIT 3"
);
$top3 = [];
while ($l = $lbRes->fetch_assoc()) $top3[] = $l;

echo json_encode([
    'success' => true,
    'user'    => [
        'id'           => $user['id'],
        'fullname'     => $user['fullname'],
        'username'     => $user['username'],
        'age'          => $user['age'],
        'account_type' => $user['account_type'],
        'member_since' => date('M Y', strtotime($user['created_at']))
    ],
    'stats' => [
        'total_attempts'  => $total_attempts,
        'total_correct'   => $total_correct,
        'total_questions' => $total_questions,
        'total_points'    => $total_points,
        'best_score'      => $best_score,
        'accuracy'        => $accuracy,
        'level'           => $level,
        'xp_in_level'     => $xp_in_level,
        'xp_pct'          => $xp_pct,
        'fav_mode'        => $fav_mode,
        'global_rank'     => $global_rank
    ],
    'recent' => $recent,
    'top3'   => $top3
]);

$conn->close();
?>
