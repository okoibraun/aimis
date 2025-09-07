<?php
require_once '../../../config/db.php';

$id = $_POST['work_order_id'];
$material = $_POST['material_cost'];
$labor = $_POST['labor_cost'];
$overhead = $_POST['overhead_cost'];

mysqli_query($conn, "
    UPDATE production_work_orders SET 
        actual_material_cost = $material,
        actual_labor_cost = $labor,
        actual_overhead_cost = $overhead
    WHERE id = $id
");

header("Location: view.php?id=$id");
