<?php
require_once '../../../config/db.php';
require_once '../includes/helpers.php';

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO sales_customers (company_id, name, email, phone, address, city, country, tax_id, is_active)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssssi', 
        $_SESSION['company_id'],
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
        redirect('../views/customers/list.php');
    } else {
        $_SESSION['error'] = "Adding Customer Failed";
    }
    $stmt->close();
    // header("Location: ../views/customers/list.php");

} elseif ($action === 'edit' && $id) {
    $stmt = $conn->prepare("UPDATE sales_customers SET company_id=?, name=?, email=?, phone=?, address=?, city=?, country=?, tax_id=?, is_active=? WHERE id=?");
    $stmt->bind_param('isssssssii', 
        $_SESSION['company_id'],
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
        redirect('../views/customers/list.php');
    } else {
        $_SESSION['error'] = "Editing Customer Failed";
    }
    $stmt->close();

} else if ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM sales_customers WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/customers/list.php");

} else {
    echo "Invalid action.";
}
