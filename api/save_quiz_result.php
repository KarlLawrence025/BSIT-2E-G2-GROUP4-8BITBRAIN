<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../db.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

// ── Inputs ────────────────────────────────────────────────────────────────────
$quiz_id        = intval($input['quiz_id']         ?? 0);
$mode           = $conn->real_escape_string($input['mode'] ?? 'single_player');
$correct        = intval($input['correct_answers'] ?? 0);
$total          = intval($input['total_questions'] ?? 0);
$time_taken     = intval($input['time_taken']      ?? 0);
$time_limit     = intval($input['time_limit']      ?? 0);   // sent by quiz.js

// User — prefer session
$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($input['user_id']) && $input['user_id'] !== null) {
    $user_id = intval($input['user_id']);
}

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

// ── Fetch quiz difficulty ────────────────────────────────────────────────────
$qRow = $conn->query("SELECT difficulty FROM quizzes WHERE id = $quiz_id");
$difficulty = 'medium';
if ($qRow && $qRow->num_rows > 0) {
    $difficulty = $qRow->fetch_assoc()['difficulty'];
}

// ── Difficulty multiplier ────────────────────────────────────────────────────
$diff_mult = match($difficulty) {
    'easy' => 1.0,
    'hard' => 2.0,
    default => 1.5,   // medium
};

// ── Mode multiplier ──────────────────────────────────────────────────────────
$mode_mult = match($mode) {
    'timed_quiz'   => 1.5,
    'memory_match' => 1.25,
    'endless_quiz' => 1.5,
    'ranked_quiz'  => 2.0,
    default        => 1.0,   // single_player
};

// ── Base points ──────────────────────────────────────────────────────────────
$base_points = $correct * 10;

// ── Apply multipliers ────────────────────────────────────────────────────────
$points = (int) round($base_points * $diff_mult * $mode_mult);

// ── Perfect score bonus ──────────────────────────────────────────────────────
if ($total > 0 && $correct === $total) {
    $points += 50;
}

// ── Time bonus (timed & ranked modes only) ───────────────────────────────────
if ($time_limit > 0 && in_array($mode, ['timed_quiz', 'ranked_quiz'])) {
    $time_remaining = $time_limit - $time_taken;
    $time_pct       = $time_remaining / $time_limit;

    if ($time_pct > 0.80) {
        $points += 30;
    } elseif ($time_pct > 0.50) {
        $points += 15;
    } elseif ($time_pct > 0.20) {
        $points += 5;
    }
}

// ── Fetch user info ───────────────────────────────────────────────────────────
$uRow     = $conn->query("SELECT username, fullname FROM users WHERE id = $user_id");
$uData    = $uRow ? $uRow->fetch_assoc() : null;
$username = $conn->real_escape_string($uData['username'] ?? '');
$fullname = $conn->real_escape_string($uData['fullname'] ?? '');

// ── Save attempt ──────────────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO quiz_attempts
        (user_id, quiz_id, mode, difficulty, correct, total, time_taken, points_earned)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("iissiiid", $user_id, $quiz_id, $mode, $difficulty,
                               $correct, $total, $time_taken, $points);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Error saving attempt: " . $stmt->error]);
    $stmt->close(); $conn->close(); exit;
}
$stmt->close();

// ── Upsert leaderboard (accumulated total per user) ──────────────────────────
// Check if user already has a leaderboard row
$existing = $conn->query(
    "SELECT id, total_points, total_correct, total_questions, attempts
     FROM leaderboard WHERE user_id = $user_id"
);

if ($existing && $existing->num_rows > 0) {
    $row = $existing->fetch_assoc();
    $new_total_points    = $row['total_points']    + $points;
    $new_total_correct   = $row['total_correct']   + $correct;
    $new_total_questions = $row['total_questions'] + $total;
    $new_attempts        = $row['attempts']        + 1;
    $lid                 = $row['id'];

    $conn->query(
        "UPDATE leaderboard SET
            total_points    = $new_total_points,
            total_correct   = $new_total_correct,
            total_questions = $new_total_questions,
            attempts        = $new_attempts,
            username        = '$username',
            fullname        = '$fullname',
            updated_at      = current_timestamp()
         WHERE id = $lid"
    );
} else {
    $conn->query(
        "INSERT INTO leaderboard
            (user_id, username, fullname, total_points, total_correct, total_questions, attempts)
         VALUES
            ($user_id, '$username', '$fullname', $points, $correct, $total, 1)"
    );
}

echo json_encode([
    "success"      => true,
    "points_earned"=> $points,
    "difficulty"   => $difficulty,
    "mode"         => $mode,
    "message"      => "Result saved — you earned $points points!"
]);

$conn->close();
?>
