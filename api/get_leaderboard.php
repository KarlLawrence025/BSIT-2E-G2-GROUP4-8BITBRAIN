<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$mode = isset($_GET['mode']) ? $conn->real_escape_string($_GET['mode']) : null;

if ($mode) {
    $sql = "SELECT l.*, u.fullname, u.username FROM leaderboard l JOIN users u ON u.id = l.user_id WHERE l.mode = '$mode' ORDER BY l.score DESC, l.created_at ASC LIMIT 50";
} else {
    $sql = "SELECT l.*, u.fullname, u.username FROM leaderboard l JOIN users u ON u.id = l.user_id ORDER BY l.score DESC, l.created_at ASC LIMIT 50";
}

$result = $conn->query($sql);
$rows = [];

if ($result && $result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $row['rank'] = $rank++;
        $rows[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $rows,
    'count' => count($rows)
]);

$conn->close();
?>