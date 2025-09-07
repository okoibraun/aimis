<?php
require_once '../../../config/db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;

if ($_POST['action'] === 'create') {
    // Insert requisition
    $stmt = mysqli_prepare($conn, "INSERT INTO production_requisitions
        (requisition_code, work_order_id, created_by) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sii', $_POST['requisition_code'], $_POST['work_order_id'], $user_id);
    mysqli_stmt_execute($stmt);
    $req_id = mysqli_insert_id($conn);

    // Insert requisition items
    foreach ($_POST['material_id'] as $i => $material_id) {
        $r = $_POST['qty_requested'][$i] ?? 0;
        $i_ = $_POST['qty_issued'][$i] ?? 0;
        $c = $_POST['qty_consumed'][$i] ?? 0;

        $stmt = mysqli_prepare($conn, "INSERT INTO production_requisition_items
            (requisition_id, material_id, qty_requested, qty_issued, qty_consumed)
            VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iiddd', $req_id, $material_id, $r, $i_, $c);
        mysqli_stmt_execute($stmt);
    }

    header("Location: view.php?id=$req_id");
    exit;
}

if ($_POST['action'] === 'update') {
    $req_id = $_POST['id'];

    // Update main requisition
    $stmt = mysqli_prepare($conn, "UPDATE production_requisitions
        SET requisition_code = ?, work_order_id = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'sii', $_POST['requisition_code'], $_POST['work_order_id'], $req_id);
    mysqli_stmt_execute($stmt);

    // Delete existing items
    mysqli_query($conn, "DELETE FROM production_requisition_items WHERE requisition_id = $req_id");

    // Re-insert items
    foreach ($_POST['material_id'] as $i => $material_id) {
        $r = $_POST['qty_requested'][$i] ?? 0;
        $i_ = $_POST['qty_issued'][$i] ?? 0;
        $c = $_POST['qty_consumed'][$i] ?? 0;

        $stmt = mysqli_prepare($conn, "INSERT INTO production_requisition_items
            (requisition_id, material_id, qty_requested, qty_issued, qty_consumed)
            VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iiddd', $req_id, $material_id, $r, $i_, $c);
        mysqli_stmt_execute($stmt);
    }

    header("Location: view.php?id=$req_id");
    exit;
}
