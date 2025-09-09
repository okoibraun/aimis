<?php
require_once '../../../config/db.php';

$wo_id = $_POST['work_order_id'];
mysqli_query($conn, "DELETE FROM production_cost_breakdown WHERE work_order_id = $wo_id");

$total_est = 0;
$total_actual = 0;

foreach ($_POST['type'] as $i => $type) {
    $desc = $_POST['description'][$i];
    $est = $_POST['estimated'][$i] ?? 0;
    $act = $_POST['actual'][$i] ?? 0;
    $total_est += $est;
    $total_actual += $act;

    $stmt = mysqli_prepare($conn, "INSERT INTO production_cost_breakdown
        (work_order_id, type, description, estimated, actual)
        VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'issdd', $wo_id, $type, $desc, $est, $act);
    mysqli_stmt_execute($stmt);
}

// Update totals
$stmt = mysqli_prepare($conn, "UPDATE production_work_orders SET estimated_cost = ?, actual_cost = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'ddi', $total_est, $total_actual, $wo_id);
mysqli_stmt_execute($stmt);

header("Location: ./");
