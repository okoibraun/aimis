<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$created_by = get_current_user_id();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['segment_name']);
$type = $_POST['target_type'];

$filters = json_encode([
  'tag' => $_POST['tag'] ?? '',
  'status' => $_POST['status'] ?? '',
  'location' => $_POST['location'] ?? '',
]);

if ($id > 0) {
  $stmt = $conn->prepare("UPDATE crm_segments SET segment_name=?, target_type=?, filters=? WHERE id=? AND company_id=?");
  $stmt->bind_param("sssii", $name, $type, $filters, $id, $company_id);
} else {
  $stmt = $conn->prepare("INSERT INTO crm_segments (company_id, segment_name, target_type, filters, created_by) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("isssi", $company_id, $name, $type, $filters, $created_by);
}
$stmt->execute();
header("Location: ./");
exit;
