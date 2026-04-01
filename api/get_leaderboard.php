<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Leaderboard shows accumulated points per user across all quiz attempts
// Optional: filter by mode via quiz_attempts join
$mode = isset($_GET['mode']) && $_GET['mode'] !== ''
        ? $conn->real_escape_string($_GET['mode'])
        : null;

if ($mode) {
    // Per-mode leaderboard: sum points only from attempts of that mode
    $sql = "SELECT
                u.id         AS user_id,
                u.fullname,
                u.username,
                SUM(qa.points_earned)  AS total_points,
                SUM(qa.correct)        AS total_correct,
                SUM(qa.total)          AS total_questions,
                COUNT(qa.id)           AS attempts
            FROM quiz_attempts qa
            JOIN users u ON u.id = qa.user_id
            WHERE qa.mode = '$mode'
            GROUP BY u.id, u.fullname, u.username
            ORDER BY total_points DESC
            LIMIT 50";
} else {
    // Overall leaderboard: all accumulated points
    $sql = "SELECT
                l.user_id,
                l.fullname,
                l.username,
                l.total_points,
                l.total_correct,
                l.total_questions,
                l.attempts
            FROM leaderboard l
            ORDER BY l.total_points DESC
            LIMIT 50";
}

$result = $conn->query($sql);
$rows   = [];

if ($result && $result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $row['rank']      = $rank++;
        $row['accuracy']  = $row['total_questions'] > 0
            ? round(($row['total_correct'] / $row['total_questions']) * 100, 1)
            : 0;
        $rows[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data'    => $rows,
    'count'   => count($rows)
]);

$conn->close();
?>
