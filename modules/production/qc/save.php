<?php
require_once '../../../config/db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;

if ($_POST['action'] === 'create') {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO production_qc_checkpoints 
        (work_order_id, checkpoint_type, material_id, description, result, remarks, inspected_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $material_id = $_POST['checkpoint_type'] === 'Incoming' ? ($_POST['material_id'] ?: null) : null;
    mysqli_stmt_bind_param($stmt, 'isssssi', 
        $_POST['work_order_id'],
        $_POST['checkpoint_type'],
        $material_id,
        $_POST['description'],
        $_POST['result'],
        $_POST['remarks'],
        $user_id
    );
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit;
}

if ($_POST['action'] === 'update') {
    $stmt = mysqli_prepare($conn, "
        UPDATE production_qc_checkpoints SET
        work_order_id = ?, checkpoint_type = ?, material_id = ?, description = ?, result = ?, remarks = ?
        WHERE id = ?
    ");
    $material_id = $_POST['checkpoint_type'] === 'Incoming' ? ($_POST['material_id'] ?: null) : null;
    mysqli_stmt_bind_param($stmt, 'isssssi',
        $_POST['work_order_id'],
        $_POST['checkpoint_type'],
        $material_id,
        $_POST['description'],
        $_POST['result'],
        $_POST['remarks'],
        $_POST['id']
    );
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit;
}

