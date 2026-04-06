<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

session_start();
require_once '../db.php';

// ── Auth check ────────────────────────────────────────────────────────────────
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// ── File check ────────────────────────────────────────────────────────────────
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file     = $_FILES['avatar'];
$maxSize  = 2 * 1024 * 1024; // 2 MB

// Size check
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 2MB']);
    exit;
}

// Type check — only real images
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, GIF, or WEBP images are allowed']);
    exit;
}

// Extension from MIME (don't trust user-supplied extension)
$extMap = [
    'image/jpeg' => 'jpg',
    'image/jpg'  => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$ext = $extMap[$mimeType];

// ── Upload directory ──────────────────────────────────────────────────────────
$uploadDir = __DIR__ . '/../uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ── Delete old avatar if exists ───────────────────────────────────────────────
$oldRes = $conn->query("SELECT avatar FROM users WHERE id = $user_id");
if ($oldRes && $oldRes->num_rows > 0) {
    $oldAvatar = $oldRes->fetch_assoc()['avatar'];
    if ($oldAvatar) {
        $oldPath = __DIR__ . '/../' . $oldAvatar;
        if (file_exists($oldPath)) unlink($oldPath);
    }
}

// ── Save new file with unique name ────────────────────────────────────────────
$filename   = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
$uploadPath = $uploadDir . $filename;
$dbPath     = 'uploads/avatars/' . $filename; // relative path stored in DB

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}

// ── Update DB ─────────────────────────────────────────────────────────────────
$dbPathEsc = $conn->real_escape_string($dbPath);
$conn->query("UPDATE users SET avatar = '$dbPathEsc' WHERE id = $user_id");

echo json_encode([
    'success'    => true,
    'message'    => 'Profile picture updated!',
    'avatar_url' => $dbPath
]);

$conn->close();
?>
