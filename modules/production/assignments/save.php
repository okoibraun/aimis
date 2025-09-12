<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$work_order_id = $_POST['work_order_id'];
$resource_id = $_POST['resource_id'];
$start = $_POST['assigned_start'];
$end = $_POST['assigned_end'];
$shift = $_POST['shift'];
$remarks = $_POST['remarks'];

$stmt = $conn->prepare("INSERT INTO production_resource_assignments
    (company_id, user_id, employee_id, work_order_id, resource_id, assigned_start, assigned_end, shift, remarks)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiissss", $company_id, $user_id, $employee_id, $work_order_id, $resource_id, $start, $end, $shift, $remarks);
$insert = $stmt->execute();

if($insert) header("Location: ./");
