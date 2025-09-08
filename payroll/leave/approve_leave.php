<?php
include '../../config/db.php';

$id = $_GET['id'];
$action = $_GET['action'];

if (in_array($action, ['Approved', 'Rejected'])) {
    $stmt = $conn->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
}

header("Location: leave_requests.php");
exit;
