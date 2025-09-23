<?php
// $host = "localhost";
// $user = "legalass_root";
// $pass = "5m@ck3d!!";
// $dbname = "legalass_aimis";
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aimis";

// Base app settings
define('APP_NAME', 'AIMIS Cloud');
define('BASE_URL_DEV', 'http://aimiscloud.com.ng/');
define('BASE_URL_SECURED', 'https://aimiscloud.com.ng');

// OOP Style
// Create connection
$db = new mysqli($host, $user, $pass, $dbname);
// Check connection
if ($db->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

require_once __DIR__ . '/config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set charset
//$mysqli->set_charset("utf8mb4");
mysqli_set_charset($conn, "utf8mb4");
?>
