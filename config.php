<?php
$servername = "localhost";
$username = "root"; // Your MySQL username (usually 'root' for local servers)
$password = ""; // Your MySQL password (usually empty for local setups)
$dbname = "portfolio"; // The database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
