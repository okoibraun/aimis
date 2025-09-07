<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$type = $_POST['type'];
$subject = trim($_POST['subject']);
$due_date = $_POST['due_date'];
$related_type = $_POST['related_type'];
$related_id = intval($_POST['related_id']);
$assigned_to = intval($_POST['assigned_to']);
$status = $_POST['status'];
$notes = trim($_POST['notes']);
$reminder_at = !empty($_POST['reminder_at']) ? $_POST['reminder_at'] : null;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE crm_activities SET type=?, subject=?, due_date=?, related_type=?, related_id=?, assigned_to=?, status=?, description=?, reminder_at=? WHERE id=? AND company_id=?");
    $stmt->bind_param("sssssiisssii", $type, $subject, $due_date, $related_type, $related_id, $assigned_to, $status, $notes, $reminder_at, $id, $company_id);
} else {
    $stmt = $conn->prepare("INSERT INTO crm_activities (company_id, type, subject, due_date, related_type, related_id, assigned_to, status, description, reminder_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssiisss", $company_id, $type, $subject, $due_date, $related_type, $related_id, $assigned_to, $status, $notes, $reminder_at);
}


$stmt->execute();
header("Location: list.php");
exit;
