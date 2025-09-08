<?php
session_start();
include_once '../../../config/db.php';
include_once '../functions/log_event.php';

$report_type = $_POST['report_type'];
$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];

// Simulate report content
$report_data = json_encode([
  'generated_for' => $report_type,
  'company_id' => $company_id,
  'date' => date('Y-m-d H:i:s'),
  'content' => "Simulated $report_type report data..."
]);

$stmt = $conn->prepare("INSERT INTO tax_reports (company_id, report_type, report_data, generated_by) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $company_id, $report_type, $report_data, $user_id);

if ($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'tax', 'REPORT_GENERATED', 'tax_report', $conn->insert_id, "Type: {$_POST['report_type']}, Format: JSON");
    // Set success message
    $_SESSION['success'] = "Report generated successfully!";
    header("Location: ../reports.php");
    exit;
} else {
    $_SESSION['error'] = "Failed to generate report: " . $stmt->error;
    header("Location: ../reports.php");
    exit;
}