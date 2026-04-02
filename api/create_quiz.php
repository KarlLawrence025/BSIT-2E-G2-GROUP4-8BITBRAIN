<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

// ── Validate required fields ──────────────────────────────────────────────────
if (!isset($data['title']) || !isset($data['category']) || !isset($data['difficulty'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: title, category, difficulty']);
    exit;
}

$title      = $conn->real_escape_string(trim($data['title']));
$category   = $conn->real_escape_string(trim($data['category']));
$difficulty = $conn->real_escape_string(trim($data['difficulty']));

$allowed_difficulties = ['easy', 'medium', 'hard'];
if (!in_array($difficulty, $allowed_difficulties)) $difficulty = 'medium';

$allowed_modes = ['single_player', 'timed_quiz', 'ranked_quiz', 'memory_match', 'endless_quiz'];
$mode = isset($data['mode']) ? $conn->real_escape_string(trim($data['mode'])) : 'single_player';
if (!in_array($mode, $allowed_modes)) $mode = 'single_player';

$reference_url = (isset($data['reference_url']) && !empty(trim($data['reference_url'])))
    ? $conn->real_escape_string(trim($data['reference_url'])) : null;

$questions = isset($data['questions']) && is_array($data['questions']) ? $data['questions'] : [];

if (count($questions) === 0) {
    echo json_encode(['success' => false, 'message' => 'Please add at least one question.']);
    exit;
}

// ── Validate every question has text, 4 options, and a valid correct answer ──
foreach ($questions as $i => $q) {
    $qNum = $i + 1;

    if (empty(trim($q['text'] ?? ''))) {
        echo json_encode(['success' => false, 'message' => "Question $qNum: question text is empty."]);
        exit;
    }

    $options = $q['options'] ?? [];
    if (count($options) < 2) {
        echo json_encode(['success' => false, 'message' => "Question $qNum: must have at least 2 answer options."]);
        exit;
    }

    foreach ($options as $j => $opt) {
        if (empty(trim($opt['text'] ?? ''))) {
            echo json_encode(['success' => false, 'message' => "Question $qNum, option " . ($j+1) . ": option text is empty."]);
            exit;
        }
    }

    // correctAnswer must be a valid integer index
    $correctAnswer = $q['correctAnswer'] ?? null;
    if ($correctAnswer === null || $correctAnswer === '' || !is_numeric($correctAnswer)) {
        echo json_encode(['success' => false, 'message' => "Question $qNum: no correct answer selected."]);
        exit;
    }

    $correctAnswer = intval($correctAnswer);
    if ($correctAnswer < 0 || $correctAnswer >= count($options)) {
        echo json_encode(['success' => false, 'message' => "Question $qNum: correct answer index is out of range."]);
        exit;
    }
}

// ── All validation passed — begin transaction ─────────────────────────────────
$conn->begin_transaction();

try {
    // Insert quiz
    $stmt = $conn->prepare("INSERT INTO quizzes (title, category, difficulty, mode) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $category, $difficulty, $mode);
    if (!$stmt->execute()) throw new Exception("Error creating quiz: " . $stmt->error);
    $quiz_id = $conn->insert_id;
    $stmt->close();

    // Insert reference if provided
    if ($reference_url !== null) {
        $refCheck = $conn->query("SHOW TABLES LIKE 'quiz_references'");
        if ($refCheck && $refCheck->num_rows > 0) {
            $ref_stmt = $conn->prepare("INSERT INTO quiz_references (quiz_id, reference_url, reference_type) VALUES (?, ?, 'url')");
            $ref_stmt->bind_param("is", $quiz_id, $reference_url);
            if (!$ref_stmt->execute()) throw new Exception("Error saving reference: " . $ref_stmt->error);
            $ref_stmt->close();
        }
    }

    // Insert questions and answers
    $q_stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $a_stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");

    foreach ($questions as $question) {
        $q_text        = trim($question['text']);
        $correctAnswer = intval($question['correctAnswer']);
        $options       = $question['options'];

        $q_stmt->bind_param("is", $quiz_id, $q_text);
        if (!$q_stmt->execute()) throw new Exception("Error creating question: " . $q_stmt->error);
        $question_id = $conn->insert_id;

        foreach ($options as $opt_index => $option) {
            $a_text     = trim($option['text']);
            $is_correct = ($opt_index === $correctAnswer) ? 1 : 0;

            $a_stmt->bind_param("isi", $question_id, $a_text, $is_correct);
            if (!$a_stmt->execute()) throw new Exception("Error creating answer: " . $a_stmt->error);
        }
    }

    $q_stmt->close();
    $a_stmt->close();

    $conn->commit();

    echo json_encode([
        'success'   => true,
        'message'   => 'Quiz created successfully!',
        'quiz_id'   => $quiz_id,
        'mode'      => $mode,
        'questions' => count($questions)
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
