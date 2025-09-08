<?php
require_once '../../config/db.php';
require_once '../../modules/sales/includes/helpers.php';
include("../../functions/role_functions.php");

$action = $_POST['action'];
$id = $_POST['id'] ?? null;

if ($action === 'add') {
    $tax_exempt = intval($_POST['tax_exempt']);
    $stmt = $conn->prepare("INSERT INTO accounts_vendors (company_id, user_id, employee_id, name, email, phone, address, city, state, country, tax_id, tax_exempt)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiissssssssi', 
        $company_id,
        $user_id,
        $employee_id,
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['country'],
        $_POST['tax_id'],
        $tax_exempt
    );
    // $stmt->execute();
    if($stmt->execute()) {
        $_SESSION['success'] = "Vendor Added Successfully";
        redirect('./');
    } else {
        $_SESSION['error'] = "Adding Vendor Failed";
        redirect('./');
    }
    $stmt->close();

} elseif ($action === 'edit' && $id) {
    $tax_exempt = intval($_POST['tax_exempt']);
    $stmt = $conn->prepare("UPDATE accounts_vendors SET company_id=?, user_id=?, employee_id=?, name=?, email=?, phone=?, address=?, city=?, state=?, country=?, tax_id=?, tax_exempt=? WHERE id=?");
    $stmt->bind_param('iiissssssssii', 
        $company_id,
        $user_id,
        $employee_id,
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['country'],
        $_POST['tax_id'],
        $tax_exempt,
        $id
    );
    if($stmt->execute()) {
        $_SESSION['success'] = "Vendor details updated successfully";
        redirect('./');
    } else {
        $_SESSION['error'] = "Editing Vendor Failed";
        redirect('./');
    }
    $stmt->close();

} else if ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM accounts_vendors WHERE id=? AND company_id=?");
    $stmt->bind_param('ii', $id, $company_id);
    if($stmt->execute()) header("Location: ./");
    $stmt->close();

} else {
    echo "Invalid action.";
}
