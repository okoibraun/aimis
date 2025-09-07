<?php
session_start();
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'] ?? null;
$report = $conn->query("SELECT * FROM tax_reports WHERE id = $id AND company_id = $company_id")->fetch_assoc();

if (!$report) die("Report not found.");

header("Content-Type: application/json");
echo $report['report_data'];
exit;
