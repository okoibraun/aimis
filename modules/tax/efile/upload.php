<?php
session_start();
// Include database connection and log function
include_once '../../../config/db.php';
include_once '../functions/log_event.php';

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$country = $_POST['country'];
$period = $_POST['period'];
$method = $_POST['method'];
$status = 'pending';
$receipt_path = null;

// Handle file upload
$target_dir = "../../../uploads/efile/";
@mkdir($target_dir, 0777, true);
$filename = time() . "_" . basename($_FILES["report_file"]["name"]);
$target_file = $target_dir . $filename;

if (move_uploaded_file($_FILES["report_file"]["tmp_name"], $target_file)) {
  // Simulate e-filing API
  if ($method === 'api') {
    // Simulated external API call response
    $status = 'success';
    $receipt_path = $target_dir . $filename;
  } else {
    $status = 'submitted';
  }

  // Save to DB
  $stmt = $conn->prepare("INSERT INTO tax_efiling (company_id, user_id, country, period, method, status, report_path, receipt_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iissssss", $company_id, $user_id, $country, $period, $method, $status, $receipt_path, $receipt_path);
  $stmt->execute();

  log_tax_event($pdo, $company_id, $user_id, 'tax', 'EFILE_SUBMITTED', 'tax_efiling', $conn->insert_id, "Method: $method, File: $filename");
}

header("Location: ./");
exit;


// Lookup API config
$api = $conn->query("SELECT * FROM tax_api_config WHERE company_id = $company_id AND country = $country")->fetch_assoc();

if ($method === 'api' && $api) {
  // Simulated API Call
  $headers = [
    "Authorization: Bearer {$api['api_token']}",
    "Content-Type: application/json"
  ];

  // Normally use cURL or Guzzle here
  $status = 'success'; // simulated API response
}
