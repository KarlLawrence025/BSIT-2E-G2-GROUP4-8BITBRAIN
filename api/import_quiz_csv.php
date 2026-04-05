<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db.php';

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit;
}

$file = $_FILES['csv_file']['tmp_name'];
$ext  = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

if ($ext !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'Only .csv files are accepted.']);
    exit;
}

// ── Read CSV ──────────────────────────────────────────────────────────────────
$handle = fopen($file, 'r');
if (!$handle) {
    echo json_encode(['success' => false, 'message' => 'Could not open file.']);
    exit;
}

$rows      = [];
$rowNumber = 0;

while (($line = fgetcsv($handle, 1000, ',')) !== false) {
    $rowNumber++;
    if ($rowNumber === 1) continue; // skip header
    if (count(array_filter($line, fn($c) => trim($c) !== '')) === 0) continue; // skip empty lines

    if (count($line) < 10) {
        echo json_encode(['success' => false, 'message' => "Row $rowNumber has too few columns (expected 10, got " . count($line) . ")."]);
        fclose($handle);
        exit;
    }

    $rows[] = [
        'title'      => trim($line[0]),
        'category'   => trim($line[1]),
        'difficulty' => strtolower(trim($line[2])),
        'mode'       => strtolower(trim($line[3])),
        'question'   => trim($line[4]),
        'optionA'    => trim($line[5]),
        'optionB'    => trim($line[6]),
        'optionC'    => trim($line[7]),
        'optionD'    => trim($line[8]),
        'correct'    => strtoupper(trim($line[9]))
    ];
}
fclose($handle);

if (count($rows) === 0) {
    echo json_encode(['success' => false, 'message' => 'CSV is empty or only has a header row.']);
    exit;
}

// ── Validate ──────────────────────────────────────────────────────────────────
$validDiff    = ['easy', 'medium', 'hard'];
$validModes   = ['single_player', 'timed_quiz', 'ranked_quiz', 'memory_match', 'endless_quiz'];
$validCorrect = ['A', 'B', 'C', 'D'];

foreach ($rows as $i => $row) {
    $r = $i + 2;
    if (empty($row['title']))    { echo json_encode(['success'=>false,'message'=>"Row $r: title is empty."]);    exit; }
    if (empty($row['question'])) { echo json_encode(['success'=>false,'message'=>"Row $r: question is empty."]); exit; }
    if (empty($row['optionA']) || empty($row['optionB']) || empty($row['optionC']) || empty($row['optionD']))
        { echo json_encode(['success'=>false,'message'=>"Row $r: all four options (A/B/C/D) are required."]); exit; }
    if (!in_array($row['difficulty'], $validDiff))
        { echo json_encode(['success'=>false,'message'=>"Row $r: difficulty must be easy / medium / hard."]); exit; }
    if (!in_array($row['mode'], $validModes))
        { echo json_encode(['success'=>false,'message'=>"Row $r: invalid mode '{$row['mode']}'."]);exit; }
    if (!in_array($row['correct'], $validCorrect))
        { echo json_encode(['success'=>false,'message'=>"Row $r: correct must be A, B, C, or D."]); exit; }
}

// ── Group rows into quizzes ───────────────────────────────────────────────────
$quizGroups = [];
foreach ($rows as $row) {
    $key = $row['title'].'|||'.$row['category'].'|||'.$row['difficulty'].'|||'.$row['mode'];
    if (!isset($quizGroups[$key])) {
        $quizGroups[$key] = [
            'title'     => $row['title'],   'category'   => $row['category'],
            'difficulty'=> $row['difficulty'], 'mode'     => $row['mode'],
            'questions' => []
        ];
    }
    $quizGroups[$key]['questions'][] = [
        'question' => $row['question'],
        'options'  => ['A'=>$row['optionA'],'B'=>$row['optionB'],'C'=>$row['optionC'],'D'=>$row['optionD']],
        'correct'  => $row['correct']
    ];
}

// ── Insert using loops ────────────────────────────────────────────────────────
$conn->begin_transaction();
$quizzesImported = $questionsImported = 0;

try {
    $quizStmt     = $conn->prepare("INSERT INTO quizzes (title, category, difficulty, mode) VALUES (?, ?, ?, ?)");
    $questionStmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $answerStmt   = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");

    // LOOP 1 — each quiz
    foreach ($quizGroups as $quiz) {
        $quizStmt->bind_param("ssss", $quiz['title'], $quiz['category'], $quiz['difficulty'], $quiz['mode']);
        if (!$quizStmt->execute()) throw new Exception("Quiz insert failed: " . $quizStmt->error);
        $quiz_id = $conn->insert_id;
        $quizzesImported++;

        // LOOP 2 — each question
        foreach ($quiz['questions'] as $q) {
            $questionStmt->bind_param("is", $quiz_id, $q['question']);
            if (!$questionStmt->execute()) throw new Exception("Question insert failed: " . $questionStmt->error);
            $question_id = $conn->insert_id;
            $questionsImported++;

            // LOOP 3 — each answer option A B C D
            foreach (['A','B','C','D'] as $letter) {
                $text      = $q['options'][$letter];
                $isCorrect = ($letter === $q['correct']) ? 1 : 0;
                $answerStmt->bind_param("isi", $question_id, $text, $isCorrect);
                if (!$answerStmt->execute()) throw new Exception("Answer insert failed: " . $answerStmt->error);
            }
        }
    }

    $quizStmt->close();
    $questionStmt->close();
    $answerStmt->close();
    $conn->commit();

    echo json_encode([
        'success'            => true,
        'message'            => 'CSV import successful!',
        'quizzes_imported'   => $quizzesImported,
        'questions_imported' => $questionsImported,
        'total_rows_read'    => count($rows)
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success'=>false,'message'=>'Import failed and rolled back: '.$e->getMessage()]);
}

$conn->close();
?>
