<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$wo_id = $_POST['work_order_id'];
$delete_pcb = $conn->query("DELETE FROM production_cost_breakdown WHERE work_order_id = $wo_id");

$total_est = 0;
$total_actual = 0;

if($delete_pcb) {
    foreach ($_POST['type'] as $i => $type) {
        $type = $_POST['type'][$i];
        $desc = $_POST['description'][$i];
        $est = $_POST['estimated'][$i] ?? 0;
        $act = $_POST['actual'][$i] ?? 0;
        $total_est += $est;
        $total_actual += $act;

        $stmt = $conn->prepare("INSERT INTO production_cost_breakdown
            (company_id, user_id, employee_id, work_order_id, type, description, estimated, actual)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiissdd', $company_id, $user_id, $employee_id, $wo_id, $type, $desc, $est, $act);
        $stmt->execute();
    }

    // Update totals
    $stmt = $conn->prepare("UPDATE production_work_orders SET estimated_cost = ?, actual_cost = ? WHERE id = ? AND company_id = ?");
    $stmt->bind_param('ddii', $total_est, $total_actual, $wo_id, $company_id);
    if($stmt->execute()) header("Location: ./");
}

