<?php
require_once '../../../config/db.php';

$work_order_id = $_POST['work_order_id'];
$resource_id = $_POST['resource_id'];
$start = $_POST['assigned_start'];
$end = $_POST['assigned_end'];
$shift = $_POST['shift'];
$remarks = $_POST['remarks'];

mysqli_query($conn, "
    INSERT INTO production_resource_assignments
    (work_order_id, resource_id, assigned_start, assigned_end, shift, remarks)
    VALUES ($work_order_id, $resource_id, '$start', '$end', '$shift', '$remarks')
");

header("Location: ./");
