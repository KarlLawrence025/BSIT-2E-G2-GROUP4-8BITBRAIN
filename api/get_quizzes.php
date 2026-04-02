<?php
header('Content-Type: application/json');
require_once '../db.php';

$db_name = $conn->query("SELECT DATABASE()")->fetch_row()[0];

$sql = "SELECT * FROM quizzes";
$result = $conn->query($sql);

$quizzes = [];
$error = "none";

if (!$result) {
    $error = $conn->error;
} else {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}

echo json_encode([
    'connected_to_database' => $db_name,
    'total_quizzes_found' => count($quizzes),
    'sql_error' => $error,
    'quiz_data' => $quizzes
]);
?>