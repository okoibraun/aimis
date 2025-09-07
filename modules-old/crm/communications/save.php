<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$created_by = get_current_user_id();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$related_type = $_POST['related_type'];
$related_id = intval($_POST['related_id']);
$type = $_POST['communication_type'];
$subject = trim($_POST['subject']);
$details = trim($_POST['details']);

if ($id > 0) {
  $stmt = $conn->prepare("UPDATE crm_communications SET communication_type=?, subject=?, details=? WHERE id=? AND company_id=?");
  $stmt->bind_param("sssii", $type, $subject, $details, $id, $company_id);
} else {
  $stmt = $conn->prepare("INSERT INTO crm_communications (company_id, related_type, related_id, communication_type, subject, details, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isisssi", $company_id, $related_type, $related_id, $type, $subject, $details, $created_by);
}
$stmt->execute();

header("Location: list.php?related_type=$related_type&related_id=$related_id");
exit;
