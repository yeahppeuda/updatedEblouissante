<?php
$host = 'localhost';
$db = 'users_db'; // Database name (where both users and feedback tables exist)
$user = 'root';   // Database username
$pass = '';       // Database password (leave empty if not applicable)

// Create the connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Test the connection (optional, for debugging)
// echo "Connected successfully to the database.";
?>
