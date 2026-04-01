<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

// Get all users from database (removed 'status' - column does not exist in schema)
$sql = "SELECT id, fullname, email, username, age, account_type, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

$users = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $users,
    'count' => count($users)
]);

$conn->close();
?>
