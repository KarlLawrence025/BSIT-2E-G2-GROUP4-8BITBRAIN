<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$mode = isset($_GET['mode']) && $_GET['mode'] !== ''
        ? $conn->real_escape_string($_GET['mode'])
        : null;

// Use LEFT JOIN so quiz_id=0 (endless mode) rows are still included.
// Also join users to get avatar for the leaderboard display.
if ($mode) {
    $where = "WHERE qa.mode = '$mode'";
} else {
    $where = "";
}

$sql = "
    SELECT
        u.id                              AS user_id,
        u.fullname,
        u.username,
        u.avatar,
        SUM(qa.points_earned)             AS total_points,
        SUM(qa.correct)                   AS total_correct,
        SUM(qa.total)                     AS total_questions,
        COUNT(qa.id)                      AS attempts
    FROM quiz_attempts qa
    JOIN users u ON u.id = qa.user_id
    $where
    GROUP BY u.id, u.fullname, u.username, u.avatar
    ORDER BY total_points DESC
    LIMIT 50
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit;
}

$rows = [];
$rank = 1;
while ($row = $result->fetch_assoc()) {
    $totalC          = intval($row['total_correct']);
    $totalQ          = intval($row['total_questions']);
    $row['rank']     = $rank++;
    $row['accuracy'] = $totalQ > 0 ? round(($totalC / $totalQ) * 100, 1) : 0;
    $rows[]          = $row;
}

echo json_encode([
    'success' => true,
    'data'    => $rows,
    'count'   => count($rows)
]);

$conn->close();
?>
