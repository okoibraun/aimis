<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

// if ($_POST['action'] === 'create') {
//     $sql = "INSERT INTO production_bom (product_id, version, description, created_by) 
//             VALUES (?, ?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, 'issi',
//         $_POST['product_id'], $_POST['version'], $_POST['description'], $user_id);
//     mysqli_stmt_execute($stmt);
//     $bom_id = mysqli_insert_id($conn);
//     header("Location: view.php?id=$bom_id");
//     exit;
// }
if ($_POST['action'] === 'create') {
    // Insert BOM
    $stmt = $conn->prepare("INSERT INTO production_bom (company_id, user_id, employee_id, product_id, version, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiissi', $company_id, $user_id, $employee_id, $_POST['product_id'], $_POST['version'], $_POST['description'], $user_id);
    $stmt->execute();

    // Insert BOM Items
    $bom_id = $conn->insert_id;
    foreach ($_POST['material'] as $index => $material) {
        $qty = $_POST['material_qty'][$index];
        $uom = $_POST['material_uom'][$index];
        $stmt = $conn->prepare("INSERT INTO production_bom_items (company_id, user_id, employee_id, bom_id, material, quantity, uom) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiisds', $company_id, $user_id, $employee_id, $bom_id, $material, $qty, $uom);
        $stmt->execute();
    }

    //header("Location: view.php?id=$bom_id");
    header("Location: ./");
    exit;
}

if ($_POST['action'] === 'update') {
    // Update BOM info
    $stmt = $conn->prepare("UPDATE production_bom SET product_id=?, version=?, description=? WHERE id=?");
    $stmt->bind_param('issi', $_POST['product_id'], $_POST['version'], $_POST['description'], $_POST['id']);
    $stmt->execute();

    $bom_id = $_POST['id'];

    // Clear and re-insert BOM items
    mysqli_query($conn, "DELETE FROM production_bom_items WHERE bom_id = $bom_id");
    foreach ($_POST['material'] as $index => $material) {
        $qty = $_POST['material_qty'][$index];
        $uom = $_POST['material_uom'][$index];
        $stmt = $conn->prepare("INSERT INTO production_bom_items (company_id, user_id, employee_id, bom_id, material, quantity, uom) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiisds', $company_id, $user_id, $employee_id, $bom_id, $material, $qty, $uom);
        $stmt->execute();
    }

    //header("Location: view.php?id=$bom_id");
    header("Location: ./");
    exit;
}
