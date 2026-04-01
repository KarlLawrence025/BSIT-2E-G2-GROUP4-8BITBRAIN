<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

// ── Validate required fields ──────────────────────────────────────────────────
if (!isset($data['title']) || !isset($data['category']) || !isset($data['difficulty'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: title, category, difficulty'
    ]);
    exit;
}

// ── Sanitise inputs ───────────────────────────────────────────────────────────
$title      = $conn->real_escape_string(trim($data['title']));
$category   = $conn->real_escape_string(trim($data['category']));
$difficulty = $conn->real_escape_string(trim($data['difficulty']));

// Validate difficulty against enum
$allowed_difficulties = ['easy', 'medium', 'hard'];
if (!in_array($difficulty, $allowed_difficulties)) {
    $difficulty = 'medium';
}

// Validate and default mode
$allowed_modes = ['single_player', 'timed_quiz', 'ranked_quiz', 'memory_match', 'endless_quiz'];
$mode = isset($data['mode']) ? $conn->real_escape_string(trim($data['mode'])) : 'single_player';
if (!in_array($mode, $allowed_modes)) {
    $mode = 'single_player';
}

$reference_url = (isset($data['reference_url']) && !empty(trim($data['reference_url'])))
    ? $conn->real_escape_string(trim($data['reference_url']))
    : null;

$questions = isset($data['questions']) && is_array($data['questions']) ? $data['questions'] : [];

// ── Validate at least one question ───────────────────────────────────────────
if (count($questions) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Please add at least one question before creating the quiz.'
    ]);
    exit;
}

// ── Begin transaction ─────────────────────────────────────────────────────────
$conn->begin_transaction();

try {

    // 1. Insert quiz — only columns that exist in the schema
    $stmt = $conn->prepare(
        "INSERT INTO quizzes (title, category, difficulty, mode) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $title, $category, $difficulty, $mode);
    if (!$stmt->execute()) {
        throw new Exception("Error creating quiz: " . $stmt->error);
    }
    $quiz_id = $conn->insert_id;
    $stmt->close();

    // 2. Insert reference link if provided (table may not exist yet — handle gracefully)
    if ($reference_url !== null) {
        $ref_check = $conn->query("SHOW TABLES LIKE 'quiz_references'");
        if ($ref_check && $ref_check->num_rows > 0) {
            $ref_stmt = $conn->prepare(
                "INSERT INTO quiz_references (quiz_id, reference_url, reference_type) VALUES (?, ?, 'url')"
            );
            $ref_stmt->bind_param("is", $quiz_id, $reference_url);
            if (!$ref_stmt->execute()) {
                throw new Exception("Error saving reference: " . $ref_stmt->error);
            }
            $ref_stmt->close();
        }
        // If table doesn't exist, skip silently — quiz still saves
    }

    // 3. Insert questions and answers
    $q_stmt = $conn->prepare(
        "INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)"
    );
    $a_stmt = $conn->prepare(
        "INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)"
    );

    foreach ($questions as $question) {
        $q_text = $conn->real_escape_string(trim($question['text'] ?? ''));

        if (empty($q_text)) continue; // skip blank questions

        $q_stmt->bind_param("is", $quiz_id, $q_text);
        if (!$q_stmt->execute()) {
            throw new Exception("Error creating question: " . $q_stmt->error);
        }
        $question_id = $conn->insert_id;

        $options       = $question['options']      ?? [];
        $correct_index = isset($question['correctAnswer']) ? intval($question['correctAnswer']) : -1;

        foreach ($options as $opt_index => $option) {
            $a_text     = $conn->real_escape_string(trim($option['text'] ?? ''));
            $is_correct = ($opt_index === $correct_index) ? 1 : 0;

            if (empty($a_text)) continue; // skip blank options

            $a_stmt->bind_param("isi", $question_id, $a_text, $is_correct);
            if (!$a_stmt->execute()) {
                throw new Exception("Error creating answer: " . $a_stmt->error);
            }
        }
    }

    $q_stmt->close();
    $a_stmt->close();

    $conn->commit();

    echo json_encode([
        'success'  => true,
        'message'  => 'Quiz created successfully!',
        'quiz_id'  => $quiz_id,
        'mode'     => $mode,
        'questions'=> count($questions)
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
