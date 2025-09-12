<?php
require_once '../../../config/db.php';

$req_id = $_POST['requisition_id'];

foreach ($_POST['item_id'] as $i => $item_id) {
    $id = $_POST['item_id'][$i];
    $qty_issued = $_POST['qty_issued'][$i];
    $qty_issued = floatval($qty_issued);

    // Get material ID from requisition item
    //$material_id = $conn->query("SELECT id AS material_id FROM production_requisition_items WHERE id = $item_id")->fetch_assoc()['material_id'];

    // Update qty_issued
    $conn->query("UPDATE production_requisition_items SET qty_issued = $qty_issued WHERE id = $id AND requisition_id = $req_id");

    // // Deduct from inventory
    // mysqli_query($conn, "
    //     UPDATE {$products_tbl} 
    //     SET current_stock = current_stock - $qty 
    //     WHERE id = $material_id
    // ");
}

header("Location: ./");
