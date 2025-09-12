<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$user_id = $_SESSION['user_id'] ?? 1;
$products_tbl = "inventory_products" ?? "sales_products";

$work_order_id = $_POST['work_order_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity_produced'];
$defects = $_POST['quantity_defective'];
$batch = $_POST['batch_number'];
$remarks = $_POST['remarks'];

$log_output = $conn->query("
    INSERT INTO production_output_logs
    (company_id, user_id, employee_id, work_order_id, product_id, quantity_produced, quantity_defective, batch_number, remarks, recorded_by)
    VALUES ($company_id, $user_id, $employee_id, $work_order_id, $product_id, $quantity, $defects, '$batch', '$remarks', $user_id)
");

// Optional: update stock (move to FG)
//mysqli_query($conn, "UPDATE {$products_tbl} SET current_stock = current_stock + $quantity WHERE id = $product_id");

if($log_output) header("Location: index.php");
