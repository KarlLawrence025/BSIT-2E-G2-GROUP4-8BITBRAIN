<?php
// Simple database connection test
header('Content-Type: text/html; charset=utf-8');

echo "<h1>8BitBrain Database Test</h1>";
echo "<p>Testing database connection...</p>";

// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "8bitbrain_db";

// Try to connect
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo "<div style='background: #ef4444; color: white; padding: 20px; border-radius: 5px;'>";
    echo "❌ <strong>Connection Failed!</strong><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "<br>Common fixes:<br>";
    echo "1. Make sure MySQL is running in XAMPP<br>";
    echo "2. Check database name is '8bitbrain_db'<br>";
    echo "3. Check username/password in db.php<br>";
    echo "</div>";
    die();
}

echo "<div style='background: #22c55e; color: black; padding: 20px; border-radius: 5px;'>";
echo "✅ <strong>Connection Successful!</strong><br>";
echo "Database: " . $database . "<br>";
echo "Host: " . $host . "<br>";
echo "</div>";

// Check if tables exist
$tables = ['users', 'quizzes', 'questions', 'answers', 'feedback', 'quiz_attempts', 'leaderboards', 'quiz_references'];
echo "<h2>Table Check:</h2>";
echo "<ul>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        // Count rows in table
        $countResult = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $countResult->fetch_assoc()['count'];
        echo "<li style='color: #22c55e;'>✅ <strong>$table</strong> exists ($count rows)</li>";
    } else {
        echo "<li style='color: #ef4444;'>❌ <strong>$table</strong> NOT FOUND</li>";
    }
}

echo "</ul>";

$conn->close();

echo "<hr>";
echo "<p><a href='dashboard-admin.html'>← Back to Admin Dashboard</a></p>";
echo "<p><a href='test-connection.html'>← Back to Connection Test</a></p>";
?>
