<?php
session_start();
include_once '../../../config/db.php';

$company_id = $_SESSION['company_id'] ?? null;
$country = $_POST['country'] ?? null;
$authority_name = $_POST['authority_name'] ?? null;
$api_endpoint = $_POST['api_endpoint'] ?? null;
$api_token = $_POST['api_token'] ?? null;
$environment = $_POST['environment'];

$stmt = $conn->prepare("
  INSERT INTO tax_api_config (company_id, country, authority_name, api_endpoint, api_token, environment)
  VALUES (?, ?, ?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE 
    authority_name = VALUES(authority_name),
    api_endpoint = VALUES(api_endpoint),
    api_token = VALUES(api_token),
    environment = VALUES(environment),
    updated_at = NOW()
");
$stmt->bind_param("isssss", $company_id, $country, $authority_name, $api_endpoint, $api_token, $environment);

$stmt->execute();

header("Location: ../api_config.php");
exit;
