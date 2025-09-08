<?php
session_start();
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'];
$report = $conn->query("SELECT * FROM tax_reports WHERE id = $id AND company_id = $company_id")->fetch_assoc();

if (!$report) die("Report not found.");

$filename = "report_" . $report['report_type'] . "_" . date("Ymd_His") . ".json";
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $report['report_data'];
exit;
