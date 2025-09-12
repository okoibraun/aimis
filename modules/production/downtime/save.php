<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$work_order_id = $_POST['work_order_id'];
$resource_id = $_POST['resource_id'] ?: null;
$reason = $_POST['downtime_reason'];
$start = $_POST['start_time'];
$end = $_POST['end_time'];
$remarks = $_POST['remarks'];

if ($_POST['action'] === 'create') {
    $stmt = $conn->prepare("
        INSERT INTO production_downtime_logs
        (company_id, user_id, employee_id, work_order_id, resource_id, downtime_reason, start_time, end_time, remarks, logged_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiiisssssi',
        $company_id, $user_id, $employee_id, $work_order_id, $resource_id, $reason, $start, $end, $remarks, $user_id
    );
    $stmt->execute();
}

if ($_POST['action'] === 'update') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("
        UPDATE production_downtime_logs SET 
        work_order_id = ?, resource_id = ?, downtime_reason = ?, 
        start_time = ?, end_time = ?, remarks = ?
        WHERE id = ?
    ");
    $stmt->bind_param('isssssi',
        $work_order_id, $resource_id, $reason, $start, $end, $remarks, $id
    );
    $stmt->execute();
}

header("Location: index.php");
exit;
