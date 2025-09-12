<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

if ($_POST['action'] === 'create') {
    $stmt = $conn->prepare("INSERT INTO production_resources (company_id, user_id, employee_id, name, code, type, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiissss', $company_id, $user_id, $employee_id, $_POST['name'], $_POST['code'], $_POST['type'], $_POST['status']);
    $stmt->execute();
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'update') {
    $stmt = $conn->prepare("UPDATE production_resources SET name=?, code=?, type=?, status=? WHERE id=? AND company_id = ?");
    $stmt->bind_param('ssssii', $_POST['name'], $_POST['code'], $_POST['type'], $_POST['status'], $_POST['id'], $company_id);
    $stmt->execute();
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'assign') {
    $wo_id = $_POST['work_order_id'];
    mysqli_query($conn, "DELETE FROM production_work_order_resources WHERE work_order_id = $wo_id");

    foreach ($_POST['resource_id'] as $rid) {
        $hours = $_POST['assigned_hours'][$rid] ?? 0;
        $stmt = $conn->prepare("INSERT INTO production_work_order_resources (work_order_id, resource_id, assigned_hours)
                                       VALUES (?, ?, ?)");
        $stmt->bind_param('iid', $wo_id, $rid, $hours);
        $stmt->execute();
    }

    header("Location: ../work_orders/view.php?id=$wo_id");
    exit;
}

if ($_POST['action'] === 'delete') {
    $stmt = $conn->prepare("DELETE FROM production_resources WHERE id=?");
    $stmt->bind_param('i', $_POST['id']);
    $stmt->execute();
    header("Location: ./");
    exit;
}