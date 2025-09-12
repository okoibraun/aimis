<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

if ($_POST['action'] === 'create') {
    $stmt = $conn->prepare("INSERT INTO production_work_orders (company_id, user_id, employee_id, order_code, product_id, quantity, scheduled_start, scheduled_end, bom_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisiissii', $company_id, $user_id, $employee_id, $_POST['order_code'], $_POST['product_id'], $_POST['quantity'], $_POST['scheduled_start'], $_POST['scheduled_end'], $_POST['bom_id'], $user_id);
    $stmt->execute();
    
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'update') {
    $stmt = $conn->prepare("UPDATE production_work_orders SET order_code=?, product_id=?, quantity=?, scheduled_start=?, scheduled_end=?, bom_id=? WHERE id=?");
    $stmt->bind_param('siissii', $_POST['order_code'], $_POST['product_id'], $_POST['quantity'], $_POST['scheduled_start'], $_POST['scheduled_end'], $_POST['bom_id'], $_POST['id']);
    $stmt->execute();

    header("Location: ./");
    exit;
}
