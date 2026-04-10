<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$users     = $conn->query("SELECT COUNT(*) AS c FROM users WHERE account_type = 'user'")->fetch_assoc()['c'];
$attempts  = $conn->query("SELECT COUNT(*) AS c FROM quiz_attempts")->fetch_assoc()['c'];
$questions = $conn->query("SELECT COUNT(*) AS c FROM questions")->fetch_assoc()['c'];
$modes     = 5; // fixed — always 5 game modes

echo json_encode([
    'success'            => true,
    'active_players'     => intval($users),
    'quizzes_completed'  => intval($attempts),
    'questions_available'=> intval($questions),
    'game_modes'         => $modes
]);

$conn->close();
?>
