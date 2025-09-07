<?php
require_once '../../../config/db.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 1;
$products_tbl = "inventory_products" ?? "sales_products";

$work_order_id = $_POST['work_order_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity_produced'];
$defects = $_POST['quantity_defective'];
$batch = $_POST['batch_number'];
$remarks = $_POST['remarks'];

mysqli_query($conn, "
    INSERT INTO production_output_logs
    (work_order_id, product_id, quantity_produced, quantity_defective, batch_number, remarks, recorded_by)
    VALUES ($work_order_id, $product_id, $quantity, $defects, '$batch', '$remarks', $user_id)
");

// Optional: update stock (move to FG)
mysqli_query($conn, "UPDATE {$products_tbl} SET current_stock = current_stock + $quantity WHERE id = $product_id");

header("Location: index.php");
