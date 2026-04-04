<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$mode = isset($_GET['mode']) && $_GET['mode'] !== ''
        ? $conn->real_escape_string($_GET['mode'])
        : null;

if ($mode) {
    // ── Per-mode: SUM all attempts for that mode per user ──────────────────
    $sql = "
        SELECT
            u.id                    AS user_id,
            u.fullname,
            u.username,
            SUM(qa.points_earned)   AS total_points,
            SUM(qa.correct)         AS total_correct,
            SUM(qa.total)           AS total_questions,
            COUNT(qa.id)            AS attempts
        FROM quiz_attempts qa
        JOIN users u ON u.id = qa.user_id
        WHERE qa.mode = '$mode'
        GROUP BY u.id, u.fullname, u.username
        ORDER BY total_points DESC
        LIMIT 50";
} else {
    // ── All modes: SUM every attempt ever made per user ────────────────────
    // Pull directly from quiz_attempts so we always get live accumulated totals
    $sql = "
        SELECT
            u.id                    AS user_id,
            u.fullname,
            u.username,
            SUM(qa.points_earned)   AS total_points,
            SUM(qa.correct)         AS total_correct,
            SUM(qa.total)           AS total_questions,
            COUNT(qa.id)            AS attempts
        FROM quiz_attempts qa
        JOIN users u ON u.id = qa.user_id
        GROUP BY u.id, u.fullname, u.username
        ORDER BY total_points DESC
        LIMIT 50";
}

$result = $conn->query($sql);
$rows   = [];

if ($result && $result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $row['rank']     = $rank++;
        $totalQ          = intval($row['total_questions']);
        $totalC          = intval($row['total_correct']);
        $row['accuracy'] = $totalQ > 0
            ? round(($totalC / $totalQ) * 100, 1)
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
