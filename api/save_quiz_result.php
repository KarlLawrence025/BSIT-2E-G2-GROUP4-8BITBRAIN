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
$quiz_id_raw = isset($input['quiz_id']) ? intval($input['quiz_id']) : 0;
$mode        = $conn->real_escape_string($input['mode'] ?? 'single_player');
$correct     = intval($input['correct_answers'] ?? 0);
$total       = intval($input['total_questions'] ?? 0);
$time_taken  = intval($input['time_taken']      ?? 0);
$time_limit  = intval($input['time_limit']      ?? 0);
$points_override = isset($input['points_earned']) ? intval($input['points_earned']) : null;

// ── User — prefer session, fall back to payload ───────────────────────────────
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

// ── Difficulty ────────────────────────────────────────────────────────────────
$difficulty = 'medium';
if ($quiz_id_raw > 0) {
    $qRow = $conn->query("SELECT difficulty FROM quizzes WHERE id = $quiz_id_raw");
    if ($qRow && $qRow->num_rows > 0) {
        $difficulty = $qRow->fetch_assoc()['difficulty'];
    }
}

$diff_mult = match($difficulty) {
    'easy'  => 1.0,
    'hard'  => 2.0,
    default => 1.5,
};

$mode_mult = match($mode) {
    'timed_quiz'   => 1.5,
    'memory_match' => 1.25,
    'endless_quiz' => 1.5,
    'ranked_quiz'  => 2.0,
    default        => 1.0,
};

// ── Calculate points ──────────────────────────────────────────────────────────
if ($points_override !== null && $points_override > 0) {
    $points = $points_override;
} else {
    $points = (int) round($correct * 10 * $diff_mult * $mode_mult);

    if ($total > 0 && $correct === $total && $mode !== 'endless_quiz') {
        $points += 50;
    }

    if ($time_limit > 0 && in_array($mode, ['timed_quiz', 'ranked_quiz'])) {
        $time_remaining = $time_limit - $time_taken;
        $time_pct       = max(0, $time_remaining / $time_limit);
        if      ($time_pct > 0.80) $points += 30;
        elseif  ($time_pct > 0.50) $points += 15;
        elseif  ($time_pct > 0.20) $points += 5;
    }

    $points = max(0, $points);
}

// ── Insert quiz attempt ───────────────────────────────────────────────────────
// For endless quiz (quiz_id = 0), we store NULL to satisfy the FK constraint.
// We use a single raw query with conditional quiz_id to avoid goto/branching issues.

$diff_escaped = $conn->real_escape_string($difficulty);

if ($quiz_id_raw > 0) {
    // Normal quiz — include quiz_id
    $insert_sql = "INSERT INTO quiz_attempts 
                    (user_id, quiz_id, mode, difficulty, correct, total, time_taken, points_earned)
                   VALUES 
                    ($user_id, $quiz_id_raw, '$mode', '$diff_escaped', $correct, $total, $time_taken, $points)";
} else {
    // Endless / no quiz — quiz_id is NULL
    $insert_sql = "INSERT INTO quiz_attempts 
                    (user_id, quiz_id, mode, difficulty, correct, total, time_taken, points_earned)
                   VALUES 
                    ($user_id, NULL, '$mode', '$diff_escaped', $correct, $total, $time_taken, $points)";
}

if ($conn->query($insert_sql) !== TRUE) {
    echo json_encode(["success" => false, "message" => "Error saving attempt: " . $conn->error]);
    $conn->close();
    exit;
}

// ── Upsert leaderboard — always re-sum from quiz_attempts ────────────────────
// This guarantees the leaderboard reflects ALL attempts including endless (NULL quiz_id).
$uRow = $conn->query(
    "SELECT
        COALESCE(SUM(points_earned), 0) AS pts,
        COALESCE(SUM(correct),       0) AS corr,
        COALESCE(SUM(total),         0) AS tot,
        COUNT(id)                       AS atts
     FROM quiz_attempts
     WHERE user_id = $user_id"
);

if ($uRow && $uRow->num_rows > 0) {
    $uData    = $uRow->fetch_assoc();
    $acc_pts  = intval($uData['pts']);
    $acc_corr = intval($uData['corr']);
    $acc_tot  = intval($uData['tot']);
    $acc_atts = intval($uData['atts']);

    $uInfo    = $conn->query("SELECT username, fullname FROM users WHERE id = $user_id");
    $uInfoRow = $uInfo ? $uInfo->fetch_assoc() : null;
    $username = $conn->real_escape_string($uInfoRow['username'] ?? '');
    $fullname = $conn->real_escape_string($uInfoRow['fullname'] ?? '');

    $exists = $conn->query("SELECT id FROM leaderboard WHERE user_id = $user_id");

    if ($exists && $exists->num_rows > 0) {
        $conn->query(
            "UPDATE leaderboard SET
                total_points    = $acc_pts,
                total_correct   = $acc_corr,
                total_questions = $acc_tot,
                attempts        = $acc_atts,
                username        = '$username',
                fullname        = '$fullname',
                updated_at      = current_timestamp()
             WHERE user_id = $user_id"
        );
    } else {
        $conn->query(
            "INSERT INTO leaderboard
                (user_id, username, fullname, total_points, total_correct, total_questions, attempts)
             VALUES
                ($user_id, '$username', '$fullname', $acc_pts, $acc_corr, $acc_tot, $acc_atts)"
        );
    }
}

echo json_encode([
    "success"       => true,
    "points_earned" => $points,
    "difficulty"    => $difficulty,
    "mode"          => $mode,
    "message"       => "Result saved — you earned $points points!"
]);

$conn->close();
?>
