<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

if ($_POST['action'] === 'create') {
    $stmt = $conn->prepare("
        INSERT INTO production_qc_checkpoints 
        (company_id, user_id, employee_id, work_order_id, checkpoint_type, material_id, description, result, remarks, inspected_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $material_id = $_POST['checkpoint_type'] === 'Incoming' ? ($_POST['material_id'] ? : null) : null;
    $stmt->bind_param('iiiisssssi',
        $company_id,
        $user_id,
        $employee_id,
        $_POST['work_order_id'],
        $_POST['checkpoint_type'],
        $material_id,
        $_POST['description'],
        $_POST['result'],
        $_POST['remarks'],
        $user_id
    );
    $stmt->execute();
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'update') {
    $stmt = $conn->prepare("
        UPDATE production_qc_checkpoints SET
        work_order_id = ?, checkpoint_type = ?, material_id = ?, description = ?, result = ?, remarks = ?
        WHERE id = ?
    ");
    $material_id = $_POST['checkpoint_type'] === 'Incoming' ? ($_POST['material_id'] ?: null) : null;
    $stmt->bind_param('isssssi',
        $_POST['work_order_id'],
        $_POST['checkpoint_type'],
        $material_id,
        $_POST['description'],
        $_POST['result'],
        $_POST['remarks'],
        $_POST['id']
    );
    $stmt->execute();
    header("Location: ./");
    exit;
}

