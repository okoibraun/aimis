<?php
//require_once __DIR__ . '/config.php';
require_once("config.php");

// Create connection
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// Check connection
if ($db->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set charset
//$mysqli->set_charset("utf8mb4");
mysqli_set_charset($conn, "utf8mb4");
?>
