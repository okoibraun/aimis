<?php
require_once '../../../config/db.php';

$req_id = $_POST['requisition_id'];
$issued = $_POST['issued']; // key = requisition_item_id, value = qty
$products_tbl = "inventory_products" ?? "sales_products";

foreach ($issued as $item_id => $qty) {
    $qty = floatval($qty);

    // Get material ID from requisition item
    $result = mysqli_query($conn, "SELECT material_id FROM production_requisition_items WHERE id = $item_id");
    $material_id = mysqli_fetch_assoc($result)['material_id'];

    // Update qty_issued
    mysqli_query($conn, "
        UPDATE production_requisition_items 
        SET qty_issued = $qty 
        WHERE id = $item_id
    ");

    // Deduct from inventory
    mysqli_query($conn, "
        UPDATE {$products_tbl} 
        SET current_stock = current_stock - $qty 
        WHERE id = $material_id
    ");
}

header("Location: index.php");
