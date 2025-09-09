<?php
session_start();

require_once '../../../config/db.php';
$user_id = $_SESSION['user_id'] ?? 1;

$work_order_id = $_POST['work_order_id'];
$resource_id = $_POST['resource_id'] ?: null;
$reason = $_POST['downtime_reason'];
$start = $_POST['start_time'];
$end = $_POST['end_time'];
$remarks = $_POST['remarks'];

if ($_POST['action'] === 'create') {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO production_downtime_logs
        (work_order_id, resource_id, downtime_reason, start_time, end_time, remarks, logged_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'isssssi',
        $work_order_id, $resource_id, $reason, $start, $end, $remarks, $user_id
    );
    mysqli_stmt_execute($stmt);
}

if ($_POST['action'] === 'update') {
    $id = $_POST['id'];
    $stmt = mysqli_prepare($conn, "
        UPDATE production_downtime_logs SET 
        work_order_id = ?, resource_id = ?, downtime_reason = ?, 
        start_time = ?, end_time = ?, remarks = ?
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, 'isssssi',
        $work_order_id, $resource_id, $reason, $start, $end, $remarks, $id
    );
    mysqli_stmt_execute($stmt);
}

header("Location: index.php");
exit;
