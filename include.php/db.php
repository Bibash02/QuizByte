<?php
// Database configuration
$servername = "localhost"; // Your database server, usually 'localhost'
$username = "root"; // Your database username (often 'root' for local development)
$password = ""; // Your database password (leave empty if no password)
$dbname = "quiz_app"; // The name of your database

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
