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
    $stmt = $conn->prepare("INSERT INTO production_bom (product_id, version, description, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('issi', $_POST['product_id'], $_POST['version'], $_POST['description'], $user_id);
    $stmt->execute();
    $bom_id = $conn->insert_id;

    // Insert BOM Items
    foreach ($_POST['material_id'] as $index => $material_id) {
        $qty = $_POST['material_qty'][$index];
        $uom = $_POST['material_uom'][$index];
        $stmt = $conn->prepare("INSERT INTO production_bom_items (bom_id, material_id, quantity, uom) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iids', $bom_id, $material_id, $qty, $uom);
        $stmt->execute();
    }

    header("Location: view.php?id=$bom_id");
    exit;
}

// if ($_POST['action'] === 'update') {
//     $sql = "UPDATE production_bom SET product_id=?, version=?, description=? WHERE id=?";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, 'sssi',
//         $_POST['product_id'], $_POST['version'], $_POST['description'], $_POST['id']);
//     mysqli_stmt_execute($stmt);
//     header("Location: view.php?id=" . $_POST['id']);
//     exit;
// }
if ($_POST['action'] === 'update') {
    // Update BOM info
    $stmt = $conn->prepare("UPDATE production_bom SET product_id=?, version=?, description=? WHERE id=?");
    $stmt->bind_param('sssi', $_POST['product_id'], $_POST['version'], $_POST['description'], $_POST['id']);
    $stmt->execute();

    $bom_id = $_POST['id'];

    // Clear and re-insert BOM items
    mysqli_query($conn, "DELETE FROM production_bom_items WHERE bom_id = $bom_id");
    foreach ($_POST['material_id'] as $index => $material_id) {
        $qty = $_POST['material_qty'][$index];
        $uom = $_POST['material_uom'][$index];
        $stmt = $conn->prepare($conn, "INSERT INTO production_bom_items (bom_id, material_id, quantity, uom) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iids', $bom_id, $material_id, $qty, $uom);
        $stmt->execute();
    }

    header("Location: view.php?id=$bom_id");
    exit;
}
