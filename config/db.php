<?php
// config/db.php

$servername = "localhost";
$username = "root";         // Default XAMPP/WAMP username
$password = "";             // Default is empty
$dbname = "book_seekers_db"; // Must match your PHPMyAdmin database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure support for special characters (like in Bangla language support)
$conn->set_charset("utf8mb4");
?>