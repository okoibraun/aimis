<?php
require_once '../../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Soft delete for user-owned schedule
$stmt = $conn->prepare("UPDATE report_schedules SET status = 'deleted' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: schedule_list.php");
exit;
