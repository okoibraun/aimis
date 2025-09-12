<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

if ($_POST['action'] === 'create') {
    // Insert requisition
    $stmt = $conn->prepare("INSERT INTO production_requisitions
        (company_id, user_id, employee_id, requisition_code, work_order_id, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisii', $company_id, $user_id, $employee_id, $_POST['requisition_code'], $_POST['work_order_id'], $user_id);
    $stmt->execute();

    // Insert requisition items
    $req_id = $conn->insert_id;
    foreach ($_POST['material'] as $i => $material) {
        $material = $_POST['material'][$i];
        $r = $_POST['qty_requested'][$i] ?? 0;
        $i_ = $_POST['qty_issued'][$i] ?? 0;
        $c = $_POST['qty_consumed'][$i] ?? 0;

        $stmt = $conn->prepare("INSERT INTO production_requisition_items
            (company_id, user_id, employee_id, requisition_id, material, qty_requested)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiisd', $company_id, $user_id, $employee_id, $req_id, $material, $r);
        $stmt->execute();
    }

    // header("Location: view.php?id=$req_id");
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'update') {
    $req_id = $_POST['id'];
    $work_order_id = intval($_POST['work_order_id']);

    // Update main requisition
    $stmt = $conn->prepare("UPDATE production_requisitions SET work_order_id = ? WHERE id = ?");
    $stmt->bind_param('ii', $work_order_id, $req_id);
    $stmt->execute();

    // Delete existing items
    $delete_requisition_items = $conn->query("DELETE FROM production_requisition_items WHERE requisition_id = $req_id AND company_id = $company_id");

    // Re-insert items
    if($delete_requisition_items) {
        foreach ($_POST['material'] as $i => $material) {
            $material = $_POST['material'][$i];
            $r = $_POST['qty_requested'][$i] ?? 0;
            $i_ = $_POST['qty_issued'][$i] ?? 0;
            $c = $_POST['qty_consumed'][$i] ?? 0;

            $stmt = $conn->prepare("INSERT INTO production_requisition_items
                (company_id, user_id, employee_id, requisition_id, material, qty_requested, qty_issued, qty_consumed)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iiiisddd', $company_id, $user_id, $employee_id, $req_id, $material, $r, $i_, $c);
            $stmt->execute();
        }
    
        // header("Location: view.php?id=$req_id");
        header("Location: ./");
        exit;
    }
}
