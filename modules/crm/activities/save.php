<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
require_once '../../../functions/role_functions.php';


$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$type = $_POST['type'];
$subject = trim($_POST['subject']);
$due_date = $_POST['due_date'];
$related_type = $_POST['related_type'];
$related_id = intval($_POST['related_id']);
$assigned_to = intval($_POST['assigned_to']);
$status = $_POST['status'];
$description = trim($_POST['description']);
$reminder_at = !empty($_POST['reminder_at']) ? $_POST['reminder_at'] : null;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE crm_activities SET type=?, subject=?, due_date=?, related_type=?, related_id=?, assigned_to=?, status=?, description=?, reminder_at=? WHERE id=? AND company_id=?");
    $stmt->bind_param("sssssiisssii", $type, $subject, $due_date, $related_type, $related_id, $assigned_to, $status, $description, $reminder_at, $id, $company_id);
} else {
    $stmt = $conn->prepare("INSERT INTO crm_activities (company_id, type, subject, due_date, related_type, related_id, assigned_to, status, description, reminder_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssiisss", $company_id, $type, $subject, $due_date, $related_type, $related_id, $assigned_to, $status, $description, $reminder_at);
}

if($stmt->execute()) {
    if($id > 0 && $status == 'completed') {
        $update_reminder = $conn->query("UPDATE crm_reminders SET is_done = 1 WHERE activity_id = $id AND company_id = $company_id");
        if($update_reminder) {
            header("Location: ./");
            exit;
        }
    } else {
        $activity_id = $conn->insert_id;
        $save_reminder = $conn->query("INSERT INTO crm_reminders (company_id, user_id, activity_id, related_type, related_id, reminder_text, due_at) VALUES ($company_id, $user_id, $activity_id, '$related_type', $related_id, 'Remember to execute your activity today', '$reminder_at')");
        if($save_reminder) {
            header("Location: ./");
            exit;
        }
    }
}
// header("Location: ./");
// exit;
