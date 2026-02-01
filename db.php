<?php
// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set your database configuration
$host = 'localhost';
$user = 'root';
$password = '';  // Keep it blank unless you've set one in XAMPP
$database = 'advo';  // Use the name of the database you imported

// Create a new MySQL connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
