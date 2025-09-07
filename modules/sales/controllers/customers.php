<?php
require_once '../../../config/db.php';
require_once '../includes/helpers.php';
include("../../../functions/role_functions.php");

$action = $_POST['action'];
$id = $_POST['id'] ?? null;

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO sales_customers (company_id, user_id, employee_id, name, email, phone, address, city, country, tax_id, is_active)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisssssssi', 
        $company_id,
        $user_id,
        $employee_id,
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city'],
        $_POST['country'],
        $_POST['tax_id'],
        $_POST['is_active']
    );
    // $stmt->execute();
    if($stmt->execute()) {
        $_SESSION['success'] = "Customer Added Successfully";
        redirect('../views/customers/');
    } else {
        $_SESSION['error'] = "Adding Customer Failed";
        redirect('../views/customers/');
    }
    $stmt->close();

} elseif ($action === 'edit' && $id) {
    $stmt = $conn->prepare("UPDATE sales_customers SET company_id=?, user_id=?, employee_id=?, name=?, email=?, phone=?, address=?, city=?, country=?, tax_id=?, is_active=? WHERE id=?");
    $stmt->bind_param('iiisssssssii', 
        $company_id,
        $user_id,
        $employee_id,
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city'],
        $_POST['country'],
        $_POST['tax_id'],
        $_POST['is_active'],
        $id
    );
    if($stmt->execute()) {
        $_SESSION['success'] = "Customer details updated successfully";
        redirect('../views/customers/');
    } else {
        $_SESSION['error'] = "Editing Customer Failed";
        redirect('../views/customers/');
    }
    $stmt->close();

} else if ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM sales_customers WHERE id=? AND company_id=?");
    $stmt->bind_param('ii', $id, $company_id);
    if($stmt->execute()) header("Location: ../views/customers/");
    $stmt->close();

} else {
    echo "Invalid action.";
}
