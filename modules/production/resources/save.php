<?php
require_once '../../../config/db.php';

if ($_POST['action'] === 'create') {
    $stmt = mysqli_prepare($conn, "INSERT INTO production_resources (name, code, type, status) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $_POST['name'], $_POST['code'], $_POST['type'], $_POST['status']);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit;
}

if ($_POST['action'] === 'update') {
    $stmt = mysqli_prepare($conn, "UPDATE production_resources SET name=?, code=?, type=?, status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssi', $_POST['name'], $_POST['code'], $_POST['type'], $_POST['status'], $_POST['id']);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit;
}

if ($_POST['action'] === 'assign') {
    $wo_id = $_POST['work_order_id'];
    mysqli_query($conn, "DELETE FROM production_work_order_resources WHERE work_order_id = $wo_id");

    foreach ($_POST['resource_id'] as $rid) {
        $hours = $_POST['assigned_hours'][$rid] ?? 0;
        $stmt = mysqli_prepare($conn, "INSERT INTO production_work_order_resources (work_order_id, resource_id, assigned_hours)
                                       VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iid', $wo_id, $rid, $hours);
        mysqli_stmt_execute($stmt);
    }

    header("Location: ../work_orders/view.php?id=$wo_id");
    exit;
}

if ($_POST['action'] === 'delete') {
    $stmt = mysqli_prepare($conn, "DELETE FROM production_resources WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit;
}